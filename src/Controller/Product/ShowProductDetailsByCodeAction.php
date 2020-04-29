<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\ShopApiPlugin\ViewRepository\Product\ProductDetailsViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowProductDetailsByCodeAction
{
    /** @var ProductDetailsViewRepositoryInterface */
    private $productCatalog;

    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        ProductDetailsViewRepositoryInterface $productCatalog,
        ViewHandlerInterface $viewHandler,
        ChannelContextInterface $channelContext
    ) {
        $this->productCatalog = $productCatalog;
        $this->viewHandler = $viewHandler;
        $this->channelContext = $channelContext;
    }

    /**
     * Show a product with the given code.
     *
     * This endpoint will return a product with the given code.
     *
     * @SWG\Tag(name="Products")
     * @SWG\Parameter(
     *     name="code",
     *     in="path",
     *     type="string",
     *     description="Code of expected product.",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="locale",
     *     in="query",
     *     type="string",
     *     description="Locale in which products should be shown.",
     *     required=false
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Show a product with the given code."
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        try {
            $channel = $this->channelContext->getChannel();

            return $this->viewHandler->handle(View::create($this->productCatalog->findOneByCode(
                $request->attributes->get('code'),
                $channel->getCode(),
                $request->query->get('locale')
            ), Response::HTTP_OK));
        } catch (ChannelNotFoundException $exception) {
            throw new NotFoundHttpException('Channel has not been found.');
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
