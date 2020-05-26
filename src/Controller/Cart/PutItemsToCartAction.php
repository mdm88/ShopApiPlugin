<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sylius\ShopApiPlugin\Normalizer\RequestCartTokenNormalizerInterface;
use Sylius\ShopApiPlugin\Request\Cart\PutOptionBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Request\Cart\PutSimpleItemToCartRequest;
use Sylius\ShopApiPlugin\Request\Cart\PutVariantBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\View\ValidationErrorView;
use Sylius\ShopApiPlugin\ViewRepository\Cart\CartViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swagger\Annotations as SWG;

final class PutItemsToCartAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidatorInterface */
    private $validator;

    /** @var CartViewRepositoryInterface */
    private $cartQuery;

    /** @var string */
    private $validationErrorViewClass;

    /** @var RequestCartTokenNormalizerInterface */
    private $requestCartTokenNormalizer;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidatorInterface $validator,
        CartViewRepositoryInterface $cartQuery,
        RequestCartTokenNormalizerInterface $requestCartTokenNormalizer,
        string $validationErrorViewClass
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validator = $validator;
        $this->cartQuery = $cartQuery;
        $this->requestCartTokenNormalizer = $requestCartTokenNormalizer;
        $this->validationErrorViewClass = $validationErrorViewClass;
    }

    /**
     * Add multiple items to your cart.
     *
     * This endpoint will allow you to add a new item to your cart.
     *
     * @SWG\Tag(name="Cart")
     * @SWG\Parameter(
     *     name="token",
     *     in="path",
     *     type="string",
     *     description="Cart identifier.",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="content",
     *     in="body",
     *     description="Description of items. The smallest required amount of data is a product code and quantity for a simple product. Configurable products will require an additional `variant_code` or `options` field, but never both.",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Sylius\ShopApiPlugin\Request\Cart\PutGenericItemToCartRequest::class))
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Item has been added to the cart",
     *     @Model(type=Sylius\ShopApiPlugin\View\Cart\CartSummaryView::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid input, validation failed.",
     *     @Model(type=Sylius\ShopApiPlugin\View\ValidationErrorView::class)
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        /** @var ConstraintViolationListInterface[] $validationResults */
        $validationResults = [];
        $commandRequests = [];
        $commandsToExecute = [];

        try {
            $request = $this->requestCartTokenNormalizer->doNotAllowNullCartToken($request);
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }

        $token = $request->attributes->get('token');

        foreach ($request->request->get('items') as $item) {
            $item['token'] = $token;
            $commandRequests[] = $this->provideCommandRequest($item);
        }

        foreach ($commandRequests as $commandRequest) {
            $validationResult = $this->validator->validate($commandRequest);

            if (0 === count($validationResult)) {
                $commandsToExecute[] = $commandRequest->getCommand();
            }

            $validationResults[] = $validationResult;
        }

        if (!$this->isValid($validationResults)) {
            /** @var ValidationErrorView $errorMessage */
            $errorMessage = new $this->validationErrorViewClass();

            $errorMessage->code = Response::HTTP_BAD_REQUEST;
            $errorMessage->message = 'Validation failed';

            foreach ($validationResults as $validationResult) {
                $errors = [];

                /** @var ConstraintViolationInterface $result */
                foreach ($validationResult as $result) {
                    $errors[$result->getPropertyPath()][] = $result->getMessage();
                }

                $errorMessage->errors[] = $errors;
            }

            return $this->viewHandler->handle(View::create($errorMessage, Response::HTTP_BAD_REQUEST));
        }

        foreach ($commandsToExecute as $commandToExecute) {
            $this->bus->dispatch($commandToExecute);
        }

        try {
            return $this->viewHandler->handle(
                View::create($this->cartQuery->getOneByToken($token), Response::HTTP_CREATED)
            );
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    /** @return PutOptionBasedConfigurableItemToCartRequest|PutSimpleItemToCartRequest|PutVariantBasedConfigurableItemToCartRequest */
    private function provideCommandRequest(array $item)
    {
        $hasVariantCode = isset($item['variantCode']);
        $hasOptions = isset($item['options']);

        if (!$hasVariantCode && !$hasOptions) {
            return PutSimpleItemToCartRequest::fromArray($item);
        }

        if ($hasVariantCode && !$hasOptions) {
            return PutVariantBasedConfigurableItemToCartRequest::fromArray($item);
        }

        if (!$hasVariantCode && $hasOptions) {
            return PutOptionBasedConfigurableItemToCartRequest::fromArray($item);
        }

        throw new NotFoundHttpException('Variant not found for given configuration');
    }

    private function isValid(array $validationResults): bool
    {
        foreach ($validationResults as $validationResult) {
            if (0 !== count($validationResult)) {
                return false;
            }
        }

        return true;
    }
}
