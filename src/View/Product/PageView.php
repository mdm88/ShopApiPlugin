<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class PageView
{
    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $page;

    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $limit;

    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $pages;

    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $total;

    /**
     * @var PageLinksView
     * @SWG\Property(ref=@Model(type=PageLinksView::class))
     */
    public $links;

    /**
     * @var array
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=ProductView::class))
     * )
     */
    public $items = [];

    public function __construct()
    {
        $this->links = new PageLinksView();
    }
}
