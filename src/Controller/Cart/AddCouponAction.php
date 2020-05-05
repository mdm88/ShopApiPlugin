<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sylius\ShopApiPlugin\Command\Cart\AddCoupon;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\ViewRepository\Cart\CartViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Swagger\Annotations as SWG;

final class AddCouponAction
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
    private $addCouponCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CartViewRepositoryInterface $cartQuery,
        CommandProviderInterface $addCouponCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->cartQuery = $cartQuery;
        $this->addCouponCommandProvider = $addCouponCommandProvider;
    }

    /**
     * Add a promotion coupon code to the cart.
     *
     * This endpoint will allow you to add a promotion coupon code to the cart and receive the discount.
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
     *     required=true
     * )
     * @SWG\Response(
     *     response=204,
     *     description="Coupon has been assigned to the cart."
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid input, validation failed.",
     *     @Model(type=Sylius\ShopApiPlugin\View\Cart\EstimatedShippingCostView::class)
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $validationResults = $this->addCouponCommandProvider->validate($request);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }

        /** @var AddCoupon $addCouponCommand */
        $addCouponCommand = $this->addCouponCommandProvider->getCommand($request);

        $this->bus->dispatch($addCouponCommand);

        try {
            return $this->viewHandler->handle(View::create(
                $this->cartQuery->getOneByToken($addCouponCommand->orderToken()),
                Response::HTTP_OK
            ));
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}
