<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\ShopApiPlugin\Model\PaginatorDetails;
use Sylius\ShopApiPlugin\ViewRepository\Product\ProductReviewsViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowProductReviewsBySlugAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ProductReviewsViewRepositoryInterface */
    private $productReviewsViewRepository;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ProductReviewsViewRepositoryInterface $productReviewsViewRepository,
        ChannelContextInterface $channelContext
    ) {
        $this->viewHandler = $viewHandler;
        $this->productReviewsViewRepository = $productReviewsViewRepository;
        $this->channelContext = $channelContext;
    }

    /**
     * Show reviews.
     *
     * This endpoint will return a paginated list of all reviews related to the product identified by code.
     *
     * @SWG\Tag(name="Products")
     * @SWG\Parameter(
     *     name="slug",
     *     in="path",
     *     type="string",
     *     description="Slug of expected product.",
     *     required=true
     * )
     * @SWG\Response(
     *     response=200,
     *     description="A paginated list of all reviews related to the product identified by code.",
     *     @Model(type=Sylius\ShopApiPlugin\View\Product\ReviewsPageView::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Product not found.",
     *     @Model(type=Sylius\ShopApiPlugin\View\ValidationErrorView::class)
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        try {
            $channel = $this->channelContext->getChannel();

            $page = $this->productReviewsViewRepository->getByProductSlug(
                $request->attributes->get('slug'),
                $channel->getCode(),
                new PaginatorDetails($request->attributes->get('_route'), $request->query->all()),
                $request->query->get('locale')
            );

            return $this->viewHandler->handle(View::create($page, Response::HTTP_OK));
        } catch (ChannelNotFoundException $exception) {
            throw new NotFoundHttpException('Channel has not been found.');
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
