<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

use Swagger\Annotations as SWG;

class ImageView
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
    public $path;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $cachedPath;
}
