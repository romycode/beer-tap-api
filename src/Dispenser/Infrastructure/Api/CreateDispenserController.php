<?php

namespace App\Dispenser\Infrastructure\Api;

use App\Dispenser\Application\Command\CreateDispenserCommand;
use App\Shared\Domain\UuidGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateDispenserController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus, private readonly UuidGenerator $uuidGenerator)
    {
        $this->messageBus = $messageBus;
    }

    private function validateRequest(array $data): ?JsonResponse
    {
        if (!array_key_exists('flow_volume', $data)) {
            return $this->json(
                ['error' => ['message' => 'missing required field "flow_volume".']],
                Response::HTTP_BAD_REQUEST,
            );
        }

        if (!array_key_exists('flow_volume', $data)) {
            return $this->json(
                ['error' => ['message' => 'missing required field "flow_volume".']],
                Response::HTTP_BAD_REQUEST,
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
        $this->handle(
            new CreateDispenserCommand(
                $id,
                $data['flow_volume'],
            ),
        );

        return $this->json(['id' => $id, 'flow_volume' => $data['flow_volume']], Response::HTTP_OK);
    }
}
