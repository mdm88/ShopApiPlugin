<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Customer;

use Swagger\Annotations as SWG;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Command\Customer\VerifyAccount;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class VerifyAccountRequest implements RequestInterface
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $token;

    protected function __construct(Request $request)
    {
        $this->token = $request->query->get('token');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new VerifyAccount($this->token);
    }
}
