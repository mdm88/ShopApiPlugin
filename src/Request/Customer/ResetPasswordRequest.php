<?php

namespace Sylius\ShopApiPlugin\Request\Customer;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class ResetPasswordRequest
{
    /**
     * @var ResetPassword
     * @SWG\Property(ref=@Model(type=ResetPassword::class))
     */
    protected $password;
}
