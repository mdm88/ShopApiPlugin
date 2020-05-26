<?php


namespace Sylius\ShopApiPlugin\Request\Cart;


use Swagger\Annotations as SWG;

class PutGenericItemToCartRequest
{
    /**
     * @var string
     * @SWG\Property(type="string", property="productCode")
     */
    protected $productCode;

    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    protected $quantity;

    /**
     * @var string
     * @SWG\Property(type="string", property="variantCode")
     */
    protected $variantCode;

    /**
     * @var array|
     * @SWG\Property(
     *     type="object",
     *     additionalProperties=@SWG\Property(type="string")
     * )
     */
    protected $options;
}
