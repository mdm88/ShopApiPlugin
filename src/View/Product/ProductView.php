<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sylius\ShopApiPlugin\View\ImageView;

class ProductView
{
    /** @var string */
    public $code;

    /** @var string */
    public $name;

    /** @var string */
    public $slug;

    /** @var string */
    public $channelCode;

    /** @var string */
    public $breadcrumb;

    /** @var string */
    public $description;

    /** @var string */
    public $shortDescription;

    /** @var string */
    public $metaKeywords;

    /** @var string */
    public $metaDescription;

    /** @var string */
    public $averageRating;

    /**
     * @var ProductTaxonView
     * @SWG\Property(ref=@Model(type=ProductTaxonView::class))
     */
    public $taxons;

    /**
     * @var array
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=ProductVariantView::class))
     * )
     */
    public $variants = [];

    /**
     * @var array
     */
    public $attributes = [];

    /**
     * @var array
     */
    public $associations = [];

    /**
     * @var ImageView[]
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=ImageView::class))
     * )
     */
    public $images = [];

    public function __construct()
    {
        $this->taxons = new ProductTaxonView();
    }
}
