<?php

namespace Sylius\ShopApiPlugin\Request\Customer;

use Swagger\Annotations as SWG;

class LoginRequest
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $email;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $password;

    /**
     * @var string
     * @SWG\Property(type="string", description="The token of the current cart which should be assign to the customer")
     */
    protected $token;
}
