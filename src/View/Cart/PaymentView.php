<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Cart;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sylius\ShopApiPlugin\View\PriceView;

class PaymentView
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $state;

    /**
     * @var PaymentMethodView
     * @SWG\Property(ref=@Model(type=PaymentMethodView::class))
     */
    public $method;

    /**
     * @var PriceView
     * @SWG\Property(ref=@Model(type=PriceView::class))
     */
    public $price;

    public function __construct()
    {
        $this->method = new PaymentMethodView();
        $this->price = new PriceView();
    }
}
