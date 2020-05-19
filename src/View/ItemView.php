<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sylius\ShopApiPlugin\View\Product\ProductView;

class ItemView
{
    /**
     * @var mixed
     * @SWG\Property(type="string")
     */
    public $id;

    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $quantity;

    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $total;

    /**
     * @var ProductView
     * @SWG\Property(ref=@Model(type=ProductView::class))
     */
    public $product;

    public function __construct()
    {
        $this->product = new ProductView();
    }
}
