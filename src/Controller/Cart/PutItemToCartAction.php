<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sylius\ShopApiPlugin\Command\Cart\PutOptionBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Command\Cart\PutVariantBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Normalizer\RequestCartTokenNormalizerInterface;
use Sylius\ShopApiPlugin\ViewRepository\Cart\CartViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Swagger\Annotations as SWG;

final class PutItemToCartAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CartViewRepositoryInterface */
    private $cartQuery;

    /** @var RequestCartTokenNormalizerInterface */
    private $requestCartTokenNormalizer;

    /** @var CommandProviderInterface */
    private $putItemToCartCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CartViewRepositoryInterface $cartQuery,
        RequestCartTokenNormalizerInterface $requestCartTokenNormalizer,
        CommandProviderInterface $putItemToCartCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->cartQuery = $cartQuery;
        $this->requestCartTokenNormalizer = $requestCartTokenNormalizer;
        $this->putItemToCartCommandProvider = $putItemToCartCommandProvider;
    }

    /**
     * Add an item to your cart.
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
     *     type="string",
     *     description="Description of an item. The smallest required amount of data is a product code and quantity for a simple product. Configurable products will require an additional `variant_code` or `options` field, but never both.",
     *     required=true
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
        try {
            $request = $this->requestCartTokenNormalizer->doNotAllowNullCartToken($request);
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }

        $validationResults = $this->putItemToCartCommandProvider->validate($request);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }

        /** @var PutOptionBasedConfigurableItemToCart|PutSimpleItemToCart|PutVariantBasedConfigurableItemToCart $command */
        $command = $this->putItemToCartCommandProvider->getCommand($request);
        $this->bus->dispatch($command);

        try {
            return $this->viewHandler->handle(View::create(
                $this->cartQuery->getOneByToken($command->orderToken()),
                Response::HTTP_CREATED
            ));
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}
