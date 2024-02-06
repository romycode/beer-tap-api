<?php

namespace App\Dispenser\Infrastructure\Api;

use App\Dispenser\Application\Model\GetSpendingLineForDispenserResponse;
use App\Dispenser\Application\Model\GetSpendingLineForDispenserResponseCollection;
use App\Dispenser\Application\Query\GetSpendingLineForDispenserQuery;
use App\Shared\Domain\Exception\UnexpectedError;
use App\Shared\Domain\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class GetDispenserSpendingLinesController extends AbstractController
{
    public function __construct(private readonly QueryBus $queryBus)
    {
    }

    public function __invoke(Request $request, string $id): JsonResponse
    {
        try {
            /** @var GetSpendingLineForDispenserResponseCollection $response */
            $response = $this->queryBus->ask(new GetSpendingLineForDispenserQuery($id));
        } catch (HandlerFailedException $e) {
            if ($e->getPrevious() instanceof UnexpectedError) {
                return $this->json(
                    ['error' => ['message' => $e->getPrevious()->getMessage()]],
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                );
            }
        }

        $amount = array_reduce(
            $response->items,
            fn(float $acc, GetSpendingLineForDispenserResponse $response) => $acc + $response->totalAmount,
            0
        );
        return $this->json(['amount' => $amount, 'usages' => $response->toArray()], Response::HTTP_OK);
    }
}
