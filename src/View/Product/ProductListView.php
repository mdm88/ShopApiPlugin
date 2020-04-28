<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class ProductListView
{
    /**
     * @var ProductView[]
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=Sylius\ShopApiPlugin\View\Product\ProductView::class))
     * )
     */
    public $items = [];
}
