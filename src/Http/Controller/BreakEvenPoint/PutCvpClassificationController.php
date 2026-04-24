<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\BreakEvenPoint;

use Rucaro\Application\BreakEvenPoint\UpsertCvpClassificationInput;
use Rucaro\Application\BreakEvenPoint\UpsertCvpClassificationsUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\BreakEvenPoint\BreakEvenPointJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** PUT /api/v1/cvp-classifications (bulk upsert) */
final readonly class PutCvpClassificationController
{
    public function __construct(
        private UpsertCvpClassificationsUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        if ($this->auth->authenticate($request->header('authorization')) === null) {
            return ErrorResponse::unauthorized();
        }
        $json = $request->json;
        if (!is_array($json)) {
            return ErrorResponse::badRequest('JSON body is required.');
        }
        $entityId = is_string($json['entityId'] ?? null) ? (string) $json['entityId'] : '';
        if (!UlidGenerator::isValid($entityId)) {
            return ErrorResponse::badRequest('entityId must be a ULID.');
        }
        $rowsRaw = $json['rows'] ?? null;
        if (!is_array($rowsRaw)) {
            return ErrorResponse::badRequest('rows must be an array.');
        }
        $rows = [];
        foreach ($rowsRaw as $r) {
            if (!is_array($r)) {
                continue;
            }
            $rows[] = new UpsertCvpClassificationInput(
                accountTitleId: is_string($r['accountTitleId'] ?? null) ? (string) $r['accountTitleId'] : '',
                costType: is_string($r['costType'] ?? null) ? (string) $r['costType'] : '',
                variableRatio: is_string($r['variableRatio'] ?? null) ? (string) $r['variableRatio'] : '1.0000',
                notes: is_string($r['notes'] ?? null) && $r['notes'] !== '' ? (string) $r['notes'] : null,
            );
        }
        try {
            $built = $this->useCase->execute($entityId, $rows);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::list(
            BreakEvenPointJsonSerializer::classificationsToArray($built),
            ['total' => count($built)],
        );
    }
}
