<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Swagger\Annotations as SWG;

final class ChooseShippingMethodAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var CommandProviderInterface */
    private $chooseShippingMethodCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        CommandProviderInterface $chooseShippingMethodCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->chooseShippingMethodCommandProvider = $chooseShippingMethodCommandProvider;
    }

    /**
     * Choosing a cart shipping method.
     *
     * This endpoint will allow you to choose a cart shipping method.
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
     *     name="shippingId",
     *     in="path",
     *     type="string",
     *     description="Order number of shipment for which shipping method should be specified.",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="contents",
     *     in="body",
     *     required=true,
     *     @Model(type=Sylius\ShopApiPlugin\Request\Checkout\ChooseShippingMethodRequest::class)
     * )
     * @SWG\Response(
     *     response=204,
     *     description="Shipping method has been chosen."
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
        $this->bus->dispatch($this->chooseShippingMethodCommandProvider->getCommand($request));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
