<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Customer;

use Swagger\Annotations as SWG;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Command\Customer\GenerateResetPasswordToken;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class GenerateResetPasswordTokenRequest implements RequestInterface
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $email;

    protected function __construct(Request $request)
    {
        $this->email = $request->request->get('email');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new GenerateResetPasswordToken($this->email);
    }
}
