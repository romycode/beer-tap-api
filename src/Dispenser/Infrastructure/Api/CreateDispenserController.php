<?php

namespace App\Dispenser\Infrastructure\Api;

use App\Dispenser\Application\Command\CreateDispenserCommand;
use App\Shared\Domain\CommandBus;
use App\Shared\Domain\UuidGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateDispenserController extends AbstractController
{
    public function __construct(private CommandBus $commandBus, private readonly UuidGenerator $uuidGenerator)
    {
    }

    private function validateRequest(?array $data): ?JsonResponse
    {
        if (null === $data || !array_key_exists('flow_volume', $data)) {
            return $this->json(
                ['error' => ['message' => 'missing required field "flow_volume".']],
                Response::HTTP_BAD_REQUEST,
            );
        }

        if (!is_numeric($data['flow_volume'])) {
            return $this->json(
                ['error' => ['message' => 'missing required field "flow_volume".']],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return null;
    }

    private function getRequestBody(Request $request): ?array
    {
        return json_decode($request->getContent(), true);
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->getRequestBody($request);
        $res = $this->validateRequest($data);
        if (null !== $res) {
            return $res;
        }

        $id = $this->uuidGenerator->generate()->toString();
        $this->commandBus->execute(
            new CreateDispenserCommand(
                $id,
                $data['flow_volume'],
            ),
        );

        return $this->json(['id' => $id, 'flow_volume' => $data['flow_volume']], Response::HTTP_OK);
    }
}
