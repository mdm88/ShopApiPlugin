<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Cart;

use Swagger\Annotations as SWG;

class TotalsView
{
    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $total;

    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $items;

    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $taxes;

    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $shipping;

    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $promotion;
}
