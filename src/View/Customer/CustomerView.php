<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Customer;

use Swagger\Annotations as SWG;

class CustomerView
{
    /**
     * @var int
     * @SWG\Property(type="integer")
     */
    public $id;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $firstName;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $lastName;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $email;

    /**
     * @var \DateTimeInterface|null
     * @SWG\Property(type="string")
     */
    public $birthday;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $gender;

    /**
     * @var string|null
     * @SWG\Property(type="string")
     */
    public $phoneNumber;

    /**
     * @var bool
     * @SWG\Property(type="boolean")
     */
    public $subscribedToNewsletter;
}
