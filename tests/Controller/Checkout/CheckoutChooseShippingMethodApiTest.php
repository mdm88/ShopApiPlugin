<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Checkout;

use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\AddressOrder;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class CheckoutChooseShippingMethodApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_allows_to_choose_shipping_method(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->handle(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]), Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ])
        ));

        $data =
<<<EOT
        {
            "method": "DHL"
        }
EOT;

        $this->client->request('PUT', $this->getShippingUrl($token) . '/0', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_choose_shipping_method_in_non_existent_channel(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->handle(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]), Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ])
        ));

        $data =
<<<EOT
        {
            "method": "DHL"
        }
EOT;

        $this->client->request('PUT', sprintf('/shop-api/SPACE_KLINGON/checkout/%s/shipping/0', $token), [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @param string $token
     *
     * @return string
     */
    private function getShippingUrl($token): string
    {
        return sprintf('/shop-api/WEB_GB/checkout/%s/shipping', $token);
    }
}
