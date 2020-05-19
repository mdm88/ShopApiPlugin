<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Order;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Sylius\ShopApiPlugin\View\Order\PlacedOrderView;
use Sylius\ShopApiPlugin\ViewRepository\Order\PlacedOrderViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Swagger\Annotations as SWG;

final class ShowOrderDetailsAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInUserProvider;

    /** @var PlacedOrderViewRepositoryInterface */
    private $placedOrderQuery;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        LoggedInShopUserProviderInterface $loggedInUserProvider,
        PlacedOrderViewRepositoryInterface $placedOrderQuery
    ) {
        $this->viewHandler = $viewHandler;
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->placedOrderQuery = $placedOrderQuery;
    }

    /**
     * Shows a list of orders of the customer.
     *
     * This endpoint will return an array of orders of the customer.
     *
     * @SWG\Tag(name="Orders")
     * @SWG\Response(
     *     response=200,
     *     description="Shows a list of placed orders of the customer.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Sylius\ShopApiPlugin\View\Order\PlacedOrderView::class))
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="User token invalid."
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Order with given tokenValue not found."
     * )
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $groups = [GroupsExclusionStrategy::DEFAULT_GROUP];
        $user = null;
        if ($this->loggedInUserProvider->isUserLoggedIn()) {
            /** @var ShopUserInterface $user */
            $user = $this->loggedInUserProvider->provide();
            $groups[] = 'logged_in_user';
        }

        try {
            $order = $this->getPlacedOrderView(
                (string) $request->attributes->get('tokenValue'),
                $user
            );
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }

        $view = View::create($order, Response::HTTP_OK);
        $view->getContext()->setGroups($groups);

        return $this->viewHandler->handle($view);
    }

    private function getPlacedOrderView(string $token, ShopUserInterface $user = null): PlacedOrderView
    {
        if (null !== $user) {
            return $this
                ->placedOrderQuery
                ->getOneCompletedByCustomerEmailAndToken($user->getEmail(), $token);
        }

        return $this
            ->placedOrderQuery
            ->getOneCompletedByGuestAndToken($token);
    }
}
