<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\BlueReturn;

use Rucaro\Application\BlueReturn\CreateBlueReturnInput;
use Rucaro\Application\BlueReturn\CreateBlueReturnUseCase;
use Rucaro\Domain\BlueReturn\BlueReturnFormType;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\BlueReturn\BlueReturnJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** POST /api/v1/blue-returns */
final readonly class CreateBlueReturnController
{
    public function __construct(
        private CreateBlueReturnUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $userId = $this->auth->authenticate($request->header('authorization'));
        if ($userId === null) {
            return ErrorResponse::unauthorized();
        }
        $json = $request->json;
        if (!is_array($json)) {
            return ErrorResponse::badRequest('JSON body is required.');
        }

        $entityId = self::stringOr($json, 'entityId', '');
        if (!UlidGenerator::isValid($entityId)) {
            return ErrorResponse::badRequest('entityId must be a ULID.');
        }
        $fiscalTermId = self::stringOr($json, 'fiscalTermId', '');
        if (!UlidGenerator::isValid($fiscalTermId)) {
            return ErrorResponse::badRequest('fiscalTermId must be a ULID.');
        }

        $formTypeRaw = is_string($json['formType'] ?? null) ? (string) $json['formType'] : 'general';
        $formType = BlueReturnFormType::tryFrom($formTypeRaw);
        if ($formType === null) {
            return ErrorResponse::badRequest('formType must be one of general / agricultural / real_estate.');
        }

        $snapshotRaw = $json['snapshot'] ?? [];
        if (!is_array($snapshotRaw)) {
            return ErrorResponse::badRequest('snapshot must be an object.');
        }

        try {
            $input = new CreateBlueReturnInput(
                entityId: $entityId,
                fiscalTermId: $fiscalTermId,
                formType: $formType,
                snapshot: $snapshotRaw,
                createdBy: $userId,
            );
            $out = $this->useCase->execute($input);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::ok(BlueReturnJsonSerializer::toArray($out->form), null, 201);
    }

    /**
     * @param array<string, mixed>|list<mixed> $json
     */
    private static function stringOr(array $json, string $key, string $default): string
    {
        $v = $json[$key] ?? null;
        return is_string($v) && $v !== '' ? $v : $default;
    }
}
