<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Cart;

use Swagger\Annotations as SWG;

class PaymentMethodView
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $code;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $name;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $description;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $instructions;
}
