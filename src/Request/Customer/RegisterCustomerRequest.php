<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Customer;

use Swagger\Annotations as SWG;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Command\Customer\RegisterCustomer;
use Sylius\ShopApiPlugin\Request\ChannelBasedRequestInterface;
use Symfony\Component\HttpFoundation\Request;

class RegisterCustomerRequest implements ChannelBasedRequestInterface
{
    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $email;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $plainPassword;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $firstName;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $lastName;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $channelCode;

    /**
     * @var bool
     * @SWG\Property(type="boolean")
     */
    protected $subscribedToNewsletter;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    protected $phoneNumber;

    protected function __construct(Request $request, string $channelCode)
    {
        $this->channelCode = $channelCode;

        $this->email = $request->request->get('email');
        $this->plainPassword = $request->request->get('plainPassword');
        $this->firstName = $request->request->get('firstName');
        $this->lastName = $request->request->get('lastName');
        $this->subscribedToNewsletter = $request->request->get('subscribedToNewsletter');
        $this->phoneNumber = $request->request->get('phoneNumber');
    }

    public static function fromHttpRequestAndChannel(Request $request, ChannelInterface $channel): ChannelBasedRequestInterface
    {
        return new self($request, $channel->getCode());
    }

    public function getCommand(): CommandInterface
    {
        return new RegisterCustomer(
            $this->email,
            $this->plainPassword,
            $this->firstName,
            $this->lastName,
            $this->channelCode,
            $this->subscribedToNewsletter,
            $this->phoneNumber
        );
    }
}
