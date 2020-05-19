<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\AddressBook;

use Swagger\Annotations as SWG;

class AddressView
{
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
    public $countryCode;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $street;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $city;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $postcode;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $provinceCode;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $provinceName;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $company;

    /**
     * @var string
     * @SWG\Property(type="string")
     */
    public $phoneNumber;
}
