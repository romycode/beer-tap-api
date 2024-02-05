<?php

namespace App\Health\Infrastructure\Api;

use App\Health\Application\Model\GetHealthResponse;
use App\Health\Application\Query\GetHealthQuery;
use App\Shared\Domain\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GetHealthController extends AbstractController
{
    public function __construct(private readonly QueryBus $queryBus)
    {
    }

    public function __invoke(): JsonResponse
    {
        /** @var GetHealthResponse $healthResponse */
        $healthResponse = $this->queryBus->ask(new GetHealthQuery());

        return $this->json(
            $healthResponse,
            $healthResponse->getStatus() > 0 ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
