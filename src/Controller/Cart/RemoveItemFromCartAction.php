<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sylius\ShopApiPlugin\Command\Cart\RemoveItemFromCart;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\ViewRepository\Cart\CartViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Swagger\Annotations as SWG;

final class RemoveItemFromCartAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CartViewRepositoryInterface */
    private $cartQuery;

    /** @var CommandProviderInterface */
    private $removeItemFromCartCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CartViewRepositoryInterface $cartQuery,
        CommandProviderInterface $removeItemFromCartCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->cartQuery = $cartQuery;
        $this->removeItemFromCartCommandProvider = $removeItemFromCartCommandProvider;
    }

    /**
     * Remove cart item.
     *
     * This endpoint will remove one item from your cart.
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
     *     name="identifier",
     *     in="path",
     *     type="string",
     *     description="Identifier of a specific item. Can be found in the cart summary.",
     *     required=true
     * )
     * @SWG\Response(
     *     response=204,
     *     description="Cart item has been removed.",
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
        $validationResults = $this->removeItemFromCartCommandProvider->validate($request);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }

        /** @var RemoveItemFromCart $removeItemFromCartCommand */
        $removeItemFromCartCommand = $this->removeItemFromCartCommandProvider->getCommand($request);

        $this->bus->dispatch($removeItemFromCartCommand);

        try {
            return $this->viewHandler->handle(View::create(
                $this->cartQuery->getOneByToken($removeItemFromCartCommand->orderToken()),
                Response::HTTP_OK
            ));
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}
