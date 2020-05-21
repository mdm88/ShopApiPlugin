<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Cart;

use Swagger\Annotations as SWG;
use Sylius\ShopApiPlugin\Command\Cart\RemoveCoupon;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class RemoveCouponRequest implements RequestInterface
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $token;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new RemoveCoupon($this->token);
    }
}
