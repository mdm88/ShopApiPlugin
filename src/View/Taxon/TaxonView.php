<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Taxon;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class TaxonView
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
    public $description;

    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $position;

    /**
     * @var array
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=TaxonView::class))
     * )
     */
    public $children = [];

    /**
     * @var array
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=ImageView::class))
     * )
     */
    public $images = [];
}
