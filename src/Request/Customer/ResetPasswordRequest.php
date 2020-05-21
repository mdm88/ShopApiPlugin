<?php

namespace Sylius\ShopApiPlugin\Request\Customer;

use Swagger\Annotations as SWG;

class ResetPasswordRequest
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $first;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $second;
}
