<?php

namespace App\Tests\Application\Dispenser\Infrastructure\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UpdateDispenserStatusControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function createDispenser(): string
    {
        $this->client->request('POST', '/dispenser', [], [], [], '{"flow_volume": 0.1 }');
        return json_decode($this->client->getResponse()->getContent(), true)['id'];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function testShouldUpdateDispenserStatusToOpened(): void
    {
        $id = $this->createDispenser();

        $this->client->request('PUT', "/dispenser/" . $id . "/status", [], [], [], '{"status": "open" }');

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }

    public function testShouldUpdateDispenserStatusToClosed(): void
    {
        $id = $this->createDispenser();

        $this->client->request('PUT', "/dispenser/" . $id . "/status", [], [], [], '{"status": "open" }');
        $this->client->request('PUT', "/dispenser/" . $id . "/status", [], [], [], '{"status": "close" }');

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }

    public function testShouldThrowErrorIfAlreadyClosed(): void
    {
        $id = $this->createDispenser();

        $this->client->request(
            'PUT',
            "/dispenser/" . $id . "/status",
            [],
            [],
            [],
            '{"status": "close" }'
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
    }
}
