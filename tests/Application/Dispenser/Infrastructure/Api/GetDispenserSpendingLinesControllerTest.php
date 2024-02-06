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
        $base = \DateTimeImmutable::createFromFormat(\DateTimeInterface::RFC3339, '2024-02-05T06:50:37+00:00');

        $id = $this->createDispenser();
        $this->client->request('PUT', "/dispenser/" . $id . "/status", [], [], [], sprintf('{"status": "open", "updated_at": "%s" }', $base->format(\DateTimeInterface::RFC3339)));
        $this->client->request('PUT', "/dispenser/" . $id . "/status", [], [], [], sprintf('{"status": "close", "updated_at": "%s" }', $base->modify('+3 second')->format(\DateTimeInterface::RFC3339)));
        $this->client->request('GET', '/dispenser/'.$id.'/spending');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonStringEqualsJsonString(
            '{"amount":3.675,"usages":[{"closed_at":"2024-02-05T06:50:40+00:00","flow_volume":0.1,"opened_at":"2024-02-05T06:50:37+00:00","total_amount":3.675}]}',
            $this->client->getResponse()->getContent()
        );
    }
}
