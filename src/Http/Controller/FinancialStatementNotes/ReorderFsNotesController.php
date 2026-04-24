<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FinancialStatementNotes;

use Rucaro\Application\FinancialStatementNotes\ReorderFsNotesUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** POST /api/v1/fs-notes/reorder */
final readonly class ReorderFsNotesController
{
    public function __construct(
        private ReorderFsNotesUseCase $useCase,
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

        $entityId = self::stringOr($json, 'entityId', '');
        if (!UlidGenerator::isValid($entityId)) {
            return ErrorResponse::badRequest('entityId must be a ULID.');
        }
        $fiscalTermId = self::stringOr($json, 'fiscalTermId', '');
        if (!UlidGenerator::isValid($fiscalTermId)) {
            return ErrorResponse::badRequest('fiscalTermId must be a ULID.');
        }

        /** @var list<string> $ids */
        $ids = [];
        $raw = $json['orderedIds'] ?? [];
        if (is_array($raw)) {
            foreach ($raw as $v) {
                if (is_string($v) && $v !== '') {
                    $ids[] = $v;
                }
            }
        }

        try {
            $updated = $this->useCase->execute($entityId, $fiscalTermId, $ids);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        }
        return EnvelopeResponse::ok([
            'updated' => $updated,
            'total'   => count($ids),
        ]);
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
