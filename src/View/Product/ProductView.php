<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sylius\ShopApiPlugin\View\ImageView;

class ProductView
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
    public $slug;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $channelCode;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $breadcrumb;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $description;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $shortDescription;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $metaKeywords;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $metaDescription;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
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
