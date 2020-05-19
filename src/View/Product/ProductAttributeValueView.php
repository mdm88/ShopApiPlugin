<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

use Swagger\Annotations as SWG;

class ProductAttributeValueView
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
    public $type;

    /**
     * @var mixed
     * @SWG\Property(type="string")
     */
    public $value;
}
