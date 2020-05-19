<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

use Swagger\Annotations as SWG;

class PriceView
{
    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $current;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $currency;
}
