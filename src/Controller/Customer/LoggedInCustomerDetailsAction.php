<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Sylius\ShopApiPlugin\Factory\Customer\CustomerViewFactoryInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class LoggedInCustomerDetailsAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInShopUserProvider;

    /** @var CustomerViewFactoryInterface */
    private $customerViewFactory;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
        CustomerViewFactoryInterface $customerViewFactory
    ) {
        $this->viewHandler = $viewHandler;
        $this->loggedInShopUserProvider = $loggedInShopUserProvider;
        $this->customerViewFactory = $customerViewFactory;
    }

    /**
     * Provides currently logged in user details.
     *
     * @SWG\Tag(name="Users")
     * @SWG\Response(
     *     response=200,
     *     description="Provides currently logged in user details.",
     *     @Model(type=Sylius\ShopApiPlugin\View\Customer\CustomerView::class)
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
        if (!$this->loggedInShopUserProvider->isUserLoggedIn()) {
            return $this->viewHandler->handle(View::create(null, Response::HTTP_UNAUTHORIZED));
        }

        $customer = $this->loggedInShopUserProvider->provide()->getCustomer();
        Assert::notNull($customer);

        return $this->viewHandler->handle(View::create($this->customerViewFactory->create($customer)));
    }
}
