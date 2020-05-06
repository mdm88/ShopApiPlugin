<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Cart;

use Nelmio\ApiDocBundle\Annotation\Model;
use Sylius\ShopApiPlugin\View\PriceView;

class EstimatedShippingCostView
{
    /**
     * @var PriceView
     * @SWG\Property(ref=@Model(type=PriceView::class))
     */
    public $price;
}
