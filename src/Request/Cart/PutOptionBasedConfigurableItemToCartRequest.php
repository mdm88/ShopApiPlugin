<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Cart;

use Swagger\Annotations as SWG;
use Sylius\ShopApiPlugin\Command\Cart\PutOptionBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class PutOptionBasedConfigurableItemToCartRequest implements RequestInterface
{
    /** @var string */
    protected $token;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $productCode;

    /**
     * @var array|
     * @SWG\Property(
     *     type="object",
     *     additionalProperties={type="string"}
     * )
     */
    protected $options;

    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    protected $quantity;

    protected function __construct(?string $token, ?string $productCode, ?array $options, ?int $quantity)
    {
        $this->token = $token;
        $this->productCode = $productCode;
        $this->options = $options;
        $this->quantity = $quantity;
    }

    public function getProductCode(): string
    {
        return $this->productCode ?? '';
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public static function fromArray(array $item): self
    {
        return new self(
            $item['token'] ?? null,
            $item['productCode'] ?? null,
            $item['options'] ?? null,
            $item['quantity'] ?? null
        );
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self(
            $request->attributes->get('token'),
            $request->request->get('productCode'),
            $request->request->get('options'),
            $request->request->getInt('quantity', 1)
        );
    }

    public function getCommand(): CommandInterface
    {
        return new PutOptionBasedConfigurableItemToCart(
            $this->token,
            $this->productCode,
            $this->options,
            $this->quantity
        );
    }
}
