<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Cart;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sylius\ShopApiPlugin\View\AddressBook\AddressView;
use Sylius\ShopApiPlugin\View\Checkout\ShipmentView;
use Sylius\ShopApiPlugin\View\ItemView;

class CartSummaryView
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $tokenValue;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $channel;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $currency;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $locale;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $checkoutState;

    /**
     * @var ItemView[]
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=ItemView::class))
     * )
     */
    public $items = [];

    /**
     * @var TotalsView
     * @SWG\Property(ref=@Model(type=TotalsView::class))
     */
    public $totals;

    /**
     * @var AddressView
     * @SWG\Property(ref=@Model(type=AddressView::class))
     */
    public $shippingAddress;

    /**
     * @var AddressView
     * @SWG\Property(ref=@Model(type=AddressView::class))
     */
    public $billingAddress;

    /**
     * @var PaymentView[]
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=PaymentView::class))
     * )
     */
    public $payments = [];

    /**
     * @var ShipmentView[]
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=ShipmentView::class))
     * )
     */
    public $shipments = [];

    /**
     * @var AdjustmentView[]
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=AdjustmentView::class))
     * )
     */
    public $cartDiscounts = [];

    /**
     * @var string|null
     * @SWG\Property(type="string")
     */
    public $couponCode;

    public function __construct()
    {
        $this->totals = new TotalsView();
    }
}
