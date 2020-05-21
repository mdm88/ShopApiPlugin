<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Order;

use Swagger\Annotations as SWG;
use Sylius\ShopApiPlugin\Command\Order\UpdatePaymentMethod;
use Symfony\Component\HttpFoundation\Request;

class UpdatePaymentMethodRequest
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $token;

    /**
     * @var mixed
     * @SWG\Property(type="string", property="paymentIdentifier")
     */
    protected $paymentIdentifier;

    /**
     * @var string
     * @SWG\Property(type="string", property="paymentMethod")
     */
    protected $paymentMethod;

    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->paymentIdentifier = $request->attributes->get('paymentId');
        $this->paymentMethod = $request->request->get('method');
    }

    public function getCommand(): UpdatePaymentMethod
    {
        return new UpdatePaymentMethod($this->token, $this->paymentIdentifier, $this->paymentMethod);
    }

    public function getOrderToken(): string
    {
        return $this->token;
    }

    /** @return int|string */
    public function getPaymentId()
    {
        return $this->paymentIdentifier;
    }
}
