<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\CommandProvider\ChannelBasedCommandProviderInterface;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Sylius\ShopApiPlugin\Exception\UserNotFoundException;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Swagger\Annotations as SWG;

final class RequestPasswordResettingAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var CommandProviderInterface */
    private $generateResetPasswordTokenCommandProvider;

    /** @var ChannelBasedCommandProviderInterface */
    private $sendResetPasswordTokenCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ChannelContextInterface $channelContext,
        CommandProviderInterface $generateResetPasswordTokenCommandProvider,
        ChannelBasedCommandProviderInterface $sendResetPasswordTokenCommandProvider,
        ?ValidationErrorViewFactoryInterface $validationErrorViewFactory
    ) {
        if (null !== $validationErrorViewFactory) {
            @trigger_error('Passing ValidationErrorViewFactory as the fourth argument is deprecated', \E_USER_DEPRECATED);
        }

        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->channelContext = $channelContext;
        $this->generateResetPasswordTokenCommandProvider = $generateResetPasswordTokenCommandProvider;
        $this->sendResetPasswordTokenCommandProvider = $sendResetPasswordTokenCommandProvider;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
    }

    /**
     * Request resetting password of user with passed email.
     *
     * Email with reset password path will be sent to user. Default path for password resetting is `/password-reset/{token}`. To change it, you need to override template `@SyliusShopApi\\Email\\passwordReset.html.twig`.
     *
     * @SWG\Tag(name="Users")
     * @SWG\Parameter(
     *     name="content",
     *     in="body",
     *     required=true,
     *     @Model(type=Sylius\ShopApiPlugin\Request\Customer\GenerateResetPasswordTokenRequest::class)
     * )
     * @SWG\Response(
     *     response=204,
     *     description="Reset password request has been sent if the email exists in our system."
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
        $validationResults = $this->generateResetPasswordTokenCommandProvider->validate($request);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }

        try {
            $this->bus->dispatch($this->generateResetPasswordTokenCommandProvider->getCommand($request));
            $this->bus->dispatch($this->sendResetPasswordTokenCommandProvider->getCommand($request, $channel));
        } catch (HandlerFailedException $exception) {
            $previousException = $exception->getPrevious();
            if ($previousException instanceof UserNotFoundException) {
                return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
            }

            throw $exception;
        }

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
