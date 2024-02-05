<?php

namespace App\Tests\Application\Dispenser\Infrastructure\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GetDispenserSpendingLinesControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function createDispenser(): string
    {
        $this->client->request('POST', '/dispenser', [], [], [], '{"flow_volume": 0.1 }');
        return json_decode($this->client->getResponse()->getContent(), true)['id'];
    }

    public function testShouldReturnBadRequestIfAreMissingFields(): void
    {
        $base = (new \DateTimeImmutable())->modify('-1 day');

        $id = $this->createDispenser();
        $this->client->request('PUT', "/dispenser/" . $id . "/status", [], [], [], sprintf('{"status": "open", "updated_at": "%s" }', $base->format(\DateTimeInterface::RFC3339)));
        $this->client->request('PUT', "/dispenser/" . $id . "/status", [], [], [], sprintf('{"status": "close", "updated_at": "%s" }', $base->format(\DateTimeInterface::RFC3339)));
        $this->client->request('GET', '/dispenser/'.$id.'/spending');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonStringEqualsJsonString(
            '{"amount":0,"usages":[]}',
            $this->client->getResponse()->getContent()
        );
    }
}
