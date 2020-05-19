<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Checkout;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sylius\ShopApiPlugin\View\Cart\ShippingMethodView;

class ShipmentView
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $state;

    /**
     * @var ShippingMethodView
     * @SWG\Property(ref=@Model(type=ShippingMethodView::class))
     */
    public $method;

    public function __construct()
    {
        $this->method = new ShippingMethodView();
    }
}
