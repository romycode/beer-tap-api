<?php

namespace App\Tests\Application\Dispenser\Infrastructure\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CreateDispenserControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function testShouldReturnBadRequestIfAreMissingFields(): void
    {
        $this->client->request('POST', '/dispenser', [], [], [], "{}");

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonStringEqualsJsonString(
            '{"error":{"message":"missing required field \"flow_volume\"."}}',
            $this->client->getResponse()->getContent()
        );

        $this->client->request('POST', '/dispenser', [], [], [], "");

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonStringEqualsJsonString(
            '{"error":{"message":"missing required field \"flow_volume\"."}}',
            $this->client->getResponse()->getContent()
        );
    }

    public function testShouldReturnDispenserInformation(): void
    {
        $this->client->request('POST', '/dispenser', [], [], [], '{ "flow_volume": 0.1 }');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString(
            '"flow_volume":0.1',
            $this->client->getResponse()->getContent(),
        );
    }
}
