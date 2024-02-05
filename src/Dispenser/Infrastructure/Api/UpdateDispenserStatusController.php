<?php

namespace App\Dispenser\Infrastructure\Api;

use App\Dispenser\Application\Command\UpdateStatusDispenserCommand;
use App\Dispenser\Domain\Model\DispenserStatus;
use App\Dispenser\Domain\Model\Exception\DispenserStatusUpdateFailed;
use App\Shared\Domain\Clock;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateDispenserStatusController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus, private Clock $clock)
    {
        $this->messageBus = $commandBus;
    }

    private function validateRequest(array $data): ?JsonResponse
    {
        if (!array_key_exists('status', $data)) {
            return $this->json(
                ['error' => ['message' => 'missing required field "status" with one of those values: [open, close].']],
                Response::HTTP_BAD_REQUEST,
            );
        }

        if (!in_array($data['status'], array_map(static fn(\UnitEnum $case) => $case->value, DispenserStatus::cases()))) {
            return $this->json(
                ['error' => ['message' => 'field "status" accepts one of those values: [open, close].']],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return null;
    }

    private function getRequestBody(Request $request): ?array
    {
        return json_decode($request->getContent(), true);
    }

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $data = $this->getRequestBody($request);

        $res = $this->validateRequest($data);
        if (null !== $res) {
            return $res;
        }

        $updatedAt = array_key_exists('updated_at', $data)
            ? \DateTimeImmutable::createFromFormat(DateTimeInterface::RFC3339, $data['updated_at'])
            : $this->clock->current();

        try {
            $this->handle(
                new UpdateStatusDispenserCommand(
                    $id,
                    $data['status'],
                    $updatedAt,
                ),
            );
        } catch (HandlerFailedException $e) {
            if ($e->getPrevious() instanceof DispenserStatusUpdateFailed) {
                return $this->json(
                    ['error' => ['message' => 'dispenser is already opened/closed']],
                    Response::HTTP_CONFLICT,
                );
            }
        }

        return $this->json(data: null, status: Response::HTTP_ACCEPTED);
    }
}
