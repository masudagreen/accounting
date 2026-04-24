<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FinancialStatementNotes;

use Rucaro\Application\FinancialStatementNotes\BulkImportFsNotesFromTemplatesInput;
use Rucaro\Application\FinancialStatementNotes\BulkImportFsNotesFromTemplatesUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FinancialStatementNotes\FsNoteJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** POST /api/v1/fs-notes/bulk-import */
final readonly class BulkImportFsNotesController
{
    public function __construct(
        private BulkImportFsNotesFromTemplatesUseCase $useCase,
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

        /** @var list<string> $codes */
        $codes = [];
        $rawCodes = $json['templateCodes'] ?? [];
        if (is_array($rawCodes)) {
            foreach ($rawCodes as $c) {
                if (is_string($c) && $c !== '') {
                    $codes[] = $c;
                }
            }
        }

        try {
            $inserted = $this->useCase->execute(
                new BulkImportFsNotesFromTemplatesInput(
                    entityId: $entityId,
                    fiscalTermId: $fiscalTermId,
                    templateCodes: $codes,
                ),
            );
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::ok(
            [
                'inserted' => FsNoteJsonSerializer::toArrayList($inserted),
                'skipped'  => max(0, count($codes) - count($inserted)),
            ],
            ['total' => count($inserted)],
            201,
        );
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
