<?php

namespace Sylius\ShopApiPlugin\View\Customer;

use Swagger\Annotations as SWG;

class LoginSuccessView
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $token;
}
