<?php

namespace Sylius\ShopApiPlugin\Controller\Customer;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Sylius\Bundle\UserBundle\Form\Model\ChangePassword;
use Sylius\Bundle\UserBundle\Form\Type\UserChangePasswordType;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var LoggedInShopUserProvider */
    private $loggedInUserProvider;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var FormFactory */
    private $formFactory;

    /** @var ObjectManager */
    protected $manager;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        LoggedInShopUserProvider $loggedInUserProvider,
        EventDispatcherInterface $eventDispatcher,
        FormFactory $formFactory,
        ObjectManager $manager
    ) {
        $this->viewHandler = $viewHandler;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->manager = $manager;
    }

    /**
     * Updates the password of the user that is currently logged in.
     *
     * @SWG\Tag(name="Users")
     * @SWG\Parameter(
     *     name="content",
     *     in="body",
     *     required=true,
     *     @Model(type=Sylius\ShopApiPlugin\Request\Customer\ChangePasswordRequest::class)
     * )
     * @SWG\Response(
     *     response=204,
     *     description="Update password request success."
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid input, validation failed.",
     *     @Model(type=Sylius\ShopApiPlugin\View\ValidationErrorView::class)
     * )
     * @SWG\Response(
     *     response=401,
     *     description="User token invalid"
     * )
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        if (!$this->loggedInUserProvider->isUserLoggedIn()) {
            return $this->viewHandler->handle(View::create(null, Response::HTTP_UNAUTHORIZED));
        }

        $user = $this->loggedInUserProvider->provide();

        $changePassword = new ChangePassword();
        $form = $this->formFactory->createNamed('', UserChangePasswordType::class, $changePassword, ['csrf_protection' => false]);

        if ($form->handleRequest($request)->isValid()) {
            $user->setPlainPassword($changePassword->getNewPassword());

            $this->eventDispatcher->dispatch(UserEvents::PRE_PASSWORD_CHANGE, new GenericEvent($user));

            $this->manager->flush();

            $this->eventDispatcher->dispatch(UserEvents::POST_PASSWORD_CHANGE, new GenericEvent($user));

            return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
        }

        return $this->viewHandler->handle(View::create($form, Response::HTTP_BAD_REQUEST));
    }
}
