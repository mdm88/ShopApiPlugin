<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

use Swagger\Annotations as SWG;

class VariantOptionValueView
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
    public $value;
}
