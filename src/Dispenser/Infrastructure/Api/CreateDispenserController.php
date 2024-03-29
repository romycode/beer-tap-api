<?php

namespace App\Dispenser\Infrastructure\Api;

use App\Dispenser\Application\Command\CreateDispenserCommand;
use App\Shared\Domain\CommandBus;
use App\Shared\Domain\Exception\UnexpectedError;
use App\Shared\Domain\UuidGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class CreateDispenserController extends AbstractController
{
    public function __construct(private CommandBus $commandBus, private readonly UuidGenerator $uuidGenerator)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->getRequestBody($request);
        $errorResponse = $this->validateRequest($data);
        if (null !== $errorResponse) {
            return $errorResponse;
        }

        $id = $this->uuidGenerator->generate()->toString();
        $response = ['id' => $id, 'flow_volume' => $data['flow_volume']];

        try {
            $this->commandBus->execute(
                new CreateDispenserCommand(
                    $id,
                    $data['flow_volume'],
                ),
            );
        } catch (HandlerFailedException $e) {
            if ($e->getPrevious() instanceof UnexpectedError) {
                return $this->json(
                    ['error' => ['message' => $e->getPrevious()->getMessage()]],
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                );
            }
        }

        return $this->json($response, Response::HTTP_OK);
    }

    private function getRequestBody(Request $request): ?array
    {
        return json_decode($request->getContent(), true);
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
}
