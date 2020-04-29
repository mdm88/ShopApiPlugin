<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\CommandProvider\ChannelBasedCommandProviderInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Swagger\Annotations as SWG;

final class AddReviewByCodeAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var ChannelBasedCommandProviderInterface */
    private $addReviewByCodeCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        ChannelContextInterface $channelContext,
        ChannelBasedCommandProviderInterface $addReviewByCodeCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->channelContext = $channelContext;
        $this->addReviewByCodeCommandProvider = $addReviewByCodeCommandProvider;
    }

    /**
     * Add a review to the product.
     *
     * This endpoint will allow you to add a new review to the product.
     * Remember, that it should be accepted by an administrator before it will be available in the review list.
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
     *     name="content",
     *     in="body",
     *     required=true,
     *     @Model(ref=Sylius\ShopApiPlugin\Command\Product\AddProductReviewByCode::class)
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Review has been added to the product."
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid input, validation failed."
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $validationResults = $this->addReviewByCodeCommandProvider->validate($request, $channel);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }

        $this->bus->dispatch($this->addReviewByCodeCommandProvider->getCommand($request, $channel));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_CREATED));
    }
}
