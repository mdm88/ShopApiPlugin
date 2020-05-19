<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

use Swagger\Annotations as SWG;

class PageLinksView
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $self;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $first;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $last;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $next;
}
