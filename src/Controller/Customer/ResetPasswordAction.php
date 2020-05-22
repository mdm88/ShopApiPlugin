<?php

namespace Sylius\ShopApiPlugin\Controller\Customer;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sylius\Bundle\UserBundle\Form\Model\PasswordReset;
use Sylius\Bundle\UserBundle\Form\Type\UserResetPasswordType;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var MetadataInterface */
    private $metadata;

    /** @var RepositoryInterface */
    protected $repository;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var FormFactory */
    private $formFactory;

    /** @var ObjectManager */
    protected $manager;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        MetadataInterface $metadata,
        RepositoryInterface $repository,
        EventDispatcherInterface $eventDispatcher,
        FormFactory $formFactory,
        ObjectManager $manager
    ) {
        $this->viewHandler = $viewHandler;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->metadata = $metadata;
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->manager = $manager;
    }

    /**
     * Password reset.
     *
     * This endpoint resets the user password.
     *
     * @SWG\Tag(name="Users")
     * @SWG\Parameter(
     *     name="token",
     *     in="path",
     *     type="string",
     *     description="Password reset token.",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="content",
     *     in="body",
     *     required=true,
     *     @Model(type=Sylius\ShopApiPlugin\Request\Customer\ResetPasswordRequest::class)
     * )
     * @SWG\Response(
     *     response=204,
     *     description="Update password request success."
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Token not found. Or invalid input, validation failed.",
     *     @Model(type=Sylius\ShopApiPlugin\View\ValidationErrorView::class)
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $token = $request->attributes->get('token');

        /** @var UserInterface $user */
        $user = $this->repository->findOneBy(['passwordResetToken' => $token]);
        if (null === $user) {
            //throw new NotFoundHttpException('Token not found.');
            return $this->viewHandler->handle(View::create(null, Response::HTTP_BAD_REQUEST));
        }

        $resetting = $this->metadata->getParameter('resetting');
        $lifetime = new \DateInterval($resetting['token']['ttl']);
        if (!$user->isPasswordRequestNonExpired($lifetime)) {
            $user->setPasswordResetToken(null);
            $user->setPasswordRequestedAt(null);

            $this->manager->flush();

            return $this->viewHandler->handle(View::create(null, Response::HTTP_BAD_REQUEST));
        }

        $passwordReset = new PasswordReset();
        $form = $this->formFactory->createNamed('', UserResetPasswordType::class, $passwordReset, ['csrf_protection' => false]);

        if ($form->handleRequest($request)->isValid()) {
            $user->setPlainPassword($passwordReset->getPassword());
            $user->setPasswordResetToken(null);
            $user->setPasswordRequestedAt(null);

            $this->eventDispatcher->dispatch(UserEvents::PRE_PASSWORD_RESET, new GenericEvent($user));

            $this->manager->flush();

            $this->eventDispatcher->dispatch(UserEvents::POST_PASSWORD_RESET, new GenericEvent($user));

            return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
        }

        return $this->viewHandler->handle(View::create($form, Response::HTTP_BAD_REQUEST));
    }
}
