<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\CommandProvider\ChannelBasedCommandProviderInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class RegisterCustomerAction
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
    private $registerCustomerCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        ChannelContextInterface $channelContext,
        ChannelBasedCommandProviderInterface $registerCustomerCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->channelContext = $channelContext;
        $this->registerCustomerCommandProvider = $registerCustomerCommandProvider;
    }

    /**
     * Registering a new user.
     *
     * This creates a new user that can log in the shop.
     *
     * @SWG\Tag(name="Users")
     * @SWG\Parameter(
     *     name="content",
     *     in="body",
     *     required=true,
     *     @Model(type=Sylius\ShopApiPlugin\Request\Customer\RegisterCustomerRequest::class)
     * )
     * @SWG\Response(
     *     response=204,
     *     description="The user was successfully created."
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
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $validationResults = $this->registerCustomerCommandProvider->validate($request, $channel);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }

        $this->bus->dispatch($this->registerCustomerCommandProvider->getCommand($request, $channel));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
