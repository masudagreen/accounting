<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FinancialStatementNotes;

use Rucaro\Application\FinancialStatementNotes\CreateFsNoteInput;
use Rucaro\Application\FinancialStatementNotes\CreateFsNoteUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FinancialStatementNotes\FsNoteJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** POST /api/v1/fs-notes */
final readonly class CreateFsNoteController
{
    public function __construct(
        private CreateFsNoteUseCase $useCase,
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

        $sortOrderRaw = $json['sortOrder'] ?? 0;
        $sortOrder = is_int($sortOrderRaw) ? $sortOrderRaw : (is_numeric($sortOrderRaw) ? (int) $sortOrderRaw : 0);
        $isActive = isset($json['isActive']) ? (bool) $json['isActive'] : true;

        try {
            $input = new CreateFsNoteInput(
                entityId: $entityId,
                fiscalTermId: $fiscalTermId,
                category: self::stringOr($json, 'category', ''),
                label: self::stringOr($json, 'label', ''),
                body: self::stringOr($json, 'body', ''),
                templateCode: self::nullableString($json, 'templateCode'),
                sortOrder: $sortOrder,
                isActive: $isActive,
            );
            $out = $this->useCase->execute($input);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::ok(FsNoteJsonSerializer::toArray($out->note), null, 201);
    }

    /**
     * @param array<string, mixed>|list<mixed> $json
     */
    private static function stringOr(array $json, string $key, string $default): string
    {
        $v = $json[$key] ?? null;
        return is_string($v) && $v !== '' ? $v : $default;
    }

    /**
     * @param array<string, mixed>|list<mixed> $json
     */
    private static function nullableString(array $json, string $key): ?string
    {
        $v = $json[$key] ?? null;
        return is_string($v) && $v !== '' ? $v : null;
    }
}
