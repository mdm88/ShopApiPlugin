<?php


namespace Sylius\ShopApiPlugin\Request\Cart;


use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class PutMultipleItemsRequest
{
    /**
     * @var array
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=Sylius\ShopApiPlugin\Request\Cart\PutGenericItemToCartRequest::class))
     * )
     */
    protected $items;
}
