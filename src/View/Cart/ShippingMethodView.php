<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Cart;

use Swagger\Annotations as SWG;
use Sylius\ShopApiPlugin\View\PriceView;

class ShippingMethodView
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
     * @var PriceView
     * @SWG\Property(type="string")
     */
    public $price;

    public function __construct()
    {
        $this->price = new PriceView();
    }
}
