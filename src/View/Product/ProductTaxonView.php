<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class ProductTaxonView
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $main;

    /**
     * @var string[]
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref=@Model(type="string"))
     * )
     */
    public $others = [];
}
