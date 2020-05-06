<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

use Swagger\Annotations as SWG;

class ValidationErrorView
{
    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $code;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $message;

    /**
     * @var array
     */
    public $errors = [];
}
