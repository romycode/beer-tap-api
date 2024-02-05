<?php

namespace App\Dispenser\Infrastructure\Api;

use App\Dispenser\Application\Model\GetSpendingLineForDispenserResponse;
use App\Dispenser\Application\Model\GetSpendingLineForDispenserResponseCollection;
use App\Dispenser\Application\Query\GetSpendingLineForDispenserQuery;
use App\Shared\Domain\QueryBus;
use App\Shared\Domain\UuidGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetDispenserSpendingLinesController extends AbstractController
{
    public function __construct(private QueryBus $queryBus, private readonly UuidGenerator $uuidGenerator)
    {
    }

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $id = $this->uuidGenerator->generate()->toString();
        /** @var GetSpendingLineForDispenserResponseCollection $response */
        $response = $this->queryBus->ask(new GetSpendingLineForDispenserQuery($id));
        $amount = array_reduce(
            $response->items,
            fn(float $acc, GetSpendingLineForDispenserResponse $response) => $acc + $response->totalAmount,
            0
        );
        return $this->json(['amount' => $amount, 'usages' => $response->toArray()], Response::HTTP_OK);
    }
}
