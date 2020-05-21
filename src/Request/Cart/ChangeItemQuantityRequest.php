<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Cart;

use Swagger\Annotations as SWG;
use Sylius\ShopApiPlugin\Command\Cart\ChangeItemQuantity;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class ChangeItemQuantityRequest implements RequestInterface
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $token;

    /**
     * @var mixed
     * @SWG\Property(type="string")
     */
    protected $id;

    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    protected $quantity;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->id = $request->attributes->get('id');
        $this->quantity = $request->request->getInt('quantity');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new ChangeItemQuantity($this->token, $this->id, $this->quantity);
    }
}
