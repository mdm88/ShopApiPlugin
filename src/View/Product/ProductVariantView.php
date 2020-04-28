<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

use Nelmio\ApiDocBundle\Annotation\Model;
use Sylius\ShopApiPlugin\View\ImageView;
use Sylius\ShopApiPlugin\View\PriceView;
use Swagger\Annotations as SWG;

class ProductVariantView
{
    /** @var string */
    public $code;

    /** @var string */
    public $name;

    /** @var array */
    public $axis = [];

    /** @var array */
    public $nameAxis = [];

    /** @var bool */
    public $available;

    /**
     * @var PriceView
     * @SWG\Property(ref=@Model(type=Sylius\ShopApiPlugin\View\PriceView::class))
     */
    public $price;

    /**
     * @var PriceView|null
     * @SWG\Property(ref=@Model(type=Sylius\ShopApiPlugin\View\PriceView::class))
     */
    public $originalPrice;

    /**
     * @var ImageView[]
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=Sylius\ShopApiPlugin\View\ImageView::class))
     * )
     */
    public $images = [];

    public function __construct()
    {
        $this->price = new PriceView();
    }
}
