<?php

namespace Sylius\ShopApiPlugin\Request\Customer;

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
