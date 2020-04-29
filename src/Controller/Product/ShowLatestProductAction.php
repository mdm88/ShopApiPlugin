<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\ShopApiPlugin\ViewRepository\Product\ProductLatestViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Swagger\Annotations as SWG;

final class ShowLatestProductAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ProductLatestViewRepositoryInterface */
    private $productLatestQuery;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ProductLatestViewRepositoryInterface $productLatestQuery,
        ChannelContextInterface $channelContext
    ) {
        $this->viewHandler = $viewHandler;
        $this->productLatestQuery = $productLatestQuery;
        $this->channelContext = $channelContext;
    }

    /**
     * Show latest products.
     *
     * This endpoint will return an array of latest products.
     *
     * @SWG\Tag(name="products")
     * @SWG\Parameter(
     *     name="locale",
     *     in="query",
     *     type="string",
     *     description="Locale in which products should be shown.",
     *     required=false
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="Number of expected products per page.",
     *     required=false
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Array of latest products.",
     *     @Model(type=Sylius\ShopApiPlugin\View\Product\ProductListView::class)
     * )
     */
    public function __invoke(Request $request): Response
    {
        try {
            $channel = $this->channelContext->getChannel();

            return $this->viewHandler->handle(View::create($this->productLatestQuery->getLatestProducts(
                $channel->getCode(),
                $request->query->get('locale'),
                $request->query->getInt('limit', 4)
            ), Response::HTTP_OK));
        } catch (ChannelNotFoundException $exception) {
            throw new NotFoundHttpException('Channel has not been found.');
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
