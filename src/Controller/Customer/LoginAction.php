<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Customer;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

final class LoginAction
{
    /**
     * @SWG\Post(
     *     path="/shop-api/login",
     *     @SWG\Tag(name="Users")
     *     @SWG\Parameter(
     *         name="content",
     *         in="body",
     *         required=true,
     *         @Model(type=Sylius\ShopApiPlugin\Request\Customer\LoginRequest::class)
     *     )
     *     @SWG\Response(
     *         response="200",
     *         description="An example resource",
     *         @Model(type=Sylius\ShopApiPlugin\View\Customer\LoginSuccessView::class)
     *     )
     * )
     */
    public function __invoke()
    {

    }
}
