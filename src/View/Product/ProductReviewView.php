<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

use DateTimeInterface;
use Swagger\Annotations as SWG;

class ProductReviewView
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $title;

    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $rating;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $comment;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $author;

    /**
     * @var DateTimeInterface
     * @SWG\Property(type="string")
     */
    public $createdAt;
}
