<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Taxon;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class TaxonDetailsView
{
    /**
     * @var TaxonView
     * @SWG\Property(ref=@Model(type=TaxonView::class))
     */
    public $self;

    /**
     * @var TaxonView
     * @SWG\Property(ref=@Model(type=TaxonView::class))
     */
    public $parentTree;
}
