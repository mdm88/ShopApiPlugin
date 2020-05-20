<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Sylius\ShopApiPlugin\Exception\WrongUserException;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Swagger\Annotations as SWG;

final class CompleteOrderAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CommandProviderInterface */
    private $assignCustomerToCartCommandProvider;

    /** @var CommandProviderInterface */
    private $completeOrderCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CommandProviderInterface $assignCustomerToCartCommandProvider,
        CommandProviderInterface $completeOrderCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->assignCustomerToCartCommandProvider = $assignCustomerToCartCommandProvider;
        $this->completeOrderCommandProvider = $completeOrderCommandProvider;
    }

    /**
     * Completing checkout.
     *
     * This endpoint will allow you to complete the checkout.
     *
     * @SWG\Tag(name="Checkout")
     * @SWG\Parameter(
     *     name="token",
     *     in="path",
     *     type="string",
     *     description="Cart identifier.",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="contents",
     *     in="body",
     *     required=true,
     *     @Model(type="Sylius\ShopApiPlugin\Request\Checkout\CompleteOrderRequest::class")
     * )
     * @SWG\Response(
     *     response=204,
     *     description="Checkout has been completed."
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid input, validation failed.",
     *     @Model(type=Sylius\ShopApiPlugin\View\ValidationErrorView::class)
     * )
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        try {
            $validationResults = $this->completeOrderCommandProvider->validate($request);
            if (0 !== count($validationResults)) {
                return $this->viewHandler->handle(View::create(
                    $this->validationErrorViewFactory->create($validationResults),
                    Response::HTTP_BAD_REQUEST
                ));
            }

            if (null !== $request->request->get('email')) {
                $this->bus->dispatch($this->assignCustomerToCartCommandProvider->getCommand($request));
            }

            $this->bus->dispatch($this->completeOrderCommandProvider->getCommand($request));
        } catch (HandlerFailedException $exception) {
            $previousException = $exception->getPrevious();

            if ($previousException instanceof WrongUserException) {
                return $this->viewHandler->handle(
                    View::create(
                        'You need to be logged in with the same user that wants to complete the order',
                        Response::HTTP_UNAUTHORIZED
                    )
                );
            }

            throw $exception;
        }

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
