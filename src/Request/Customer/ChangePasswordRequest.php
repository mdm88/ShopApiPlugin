<?php

namespace Sylius\ShopApiPlugin\Request\Customer;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class ChangePasswordRequest
{
    /**
     * @var string
     * @SWG\Property(type="string", property="currentPassword")
     */
    protected $currentPassword;

    /**
     * @var ResetPasswordRequest
     * @SWG\Property(ref=@Model(type=ResetPasswordRequest::class), property="newPassword")
     */
    protected $newPassword;
}
