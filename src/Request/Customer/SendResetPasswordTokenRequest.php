<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Customer;

use Swagger\Annotations as SWG;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Command\Customer\SendResetPasswordToken;
use Sylius\ShopApiPlugin\Request\ChannelBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;

class SendResetPasswordTokenRequest implements ChannelBasedRequestInterface
{
    /**
     * @var string
     */
    protected $channelCode;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $email;

    protected function __construct(Request $request, string $channelCode)
    {
        $this->email = $request->request->get('email');
        $this->channelCode = $channelCode;
    }

    public static function fromHttpRequestAndChannel(Request $request, ChannelInterface $channel): ChannelBasedRequestInterface
    {
        return new self($request, $channel->getCode());
    }

    public function getCommand(): CommandInterface
    {
        return new SendResetPasswordToken($this->email, $this->channelCode);
    }
}
