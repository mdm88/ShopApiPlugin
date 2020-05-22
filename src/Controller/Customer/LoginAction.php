<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Customer;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

final class LoginAction
{
    /**
     * Logs the user in and returns the token.
     *
     * This route is needed to log the user in and get an access token.
     *
     * @SWG\Tag(name="Users")
     * @SWG\Parameter(
     *     name="content",
     *     in="body",
     *     required=true,
     *     @Model(type=Sylius\ShopApiPlugin\Request\Customer\LoginRequest::class)
     * )
     * @SWG\Response(
     *     response="200",
     *     description="User was logged in.",
     *     @Model(type=Sylius\ShopApiPlugin\View\Customer\LoginSuccessView::class)
     * )
     * @SWG\Response(
     *     response="401",
     *     description="Login failed.",
     *     @Model(type=Sylius\ShopApiPlugin\View\ValidationErrorView::class)
     * )
     */
    public function __invoke()
    {

    }
}
