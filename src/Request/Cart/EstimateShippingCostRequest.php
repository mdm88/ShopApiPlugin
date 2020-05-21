<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Cart;

use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;

class EstimateShippingCostRequest
{
    /**
     * @var string
     * @SWG\Property(type="string", property="cartToken")
     */
    protected $cartToken;

    /**
     * @var string
     * @SWG\Property(type="string", property="countryCode")
     */
    protected $countryCode;

    /**
     * @var string
     * @SWG\Property(type="string", property="provinceCode")
     */
    protected $provinceCode;

    public function __construct(Request $request)
    {
        $this->cartToken = $request->attributes->get('token');
        $this->countryCode = $request->query->get('countryCode');
        $this->provinceCode = $request->query->get('provinceCode');
    }

    public function cartToken(): string
    {
        return $this->cartToken;
    }

    public function countryCode(): string
    {
        return $this->countryCode;
    }

    public function provinceCode(): ?string
    {
        return $this->provinceCode;
    }
}
