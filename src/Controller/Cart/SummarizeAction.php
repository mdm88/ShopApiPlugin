<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sylius\ShopApiPlugin\ViewRepository\Cart\CartViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Swagger\Annotations as SWG;

final class SummarizeAction
{
    /** @var CartViewRepositoryInterface */
    private $cartQuery;

    /** @var ViewHandlerInterface */
    private $viewHandler;

    public function __construct(
        CartViewRepositoryInterface $cartQuery,
        ViewHandlerInterface $viewHandler
    ) {
        $this->cartQuery = $cartQuery;
        $this->viewHandler = $viewHandler;
    }

    /**
     * Show summarized cart.
     *
     * This endpoint shows you the current calculated state of cart.
     *
     * @SWG\Tag(name="Cart")
     * @SWG\Parameter(
     *     name="token",
     *     in="path",
     *     type="string",
     *     description="Cart identifier.",
     *     required=true
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Current state of the cart, with calculated prices and related items.",
     *     @Model(type=Sylius\ShopApiPlugin\View\Cart\CartSummaryView::class)
     * )
     * @SWG\Response(
     *     response=404,
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
            return $this->viewHandler->handle(
                View::create(
                    $this->cartQuery->getOneByToken($request->attributes->get('token')),
                    Response::HTTP_OK
                )
            );
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
