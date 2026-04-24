<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FinancialStatementNotes;

use Rucaro\Application\FinancialStatementNotes\UpdateFsNoteInput;
use Rucaro\Application\FinancialStatementNotes\UpdateFsNoteUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FinancialStatementNotes\FsNoteJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** PATCH /api/v1/fs-notes/{id} */
final readonly class UpdateFsNoteController
{
    public function __construct(
        private UpdateFsNoteUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        if ($this->auth->authenticate($request->header('authorization')) === null) {
            return ErrorResponse::unauthorized();
        }
        $id = $request->queryString('id');
        if ($id === null || !UlidGenerator::isValid($id)) {
            return ErrorResponse::badRequest('id must be a ULID.');
        }
        $json = $request->json;
        if (!is_array($json)) {
            return ErrorResponse::badRequest('JSON body is required.');
        }

        $sortOrder = null;
        if (array_key_exists('sortOrder', $json)) {
            $raw = $json['sortOrder'];
            $sortOrder = is_int($raw) ? $raw : (is_numeric($raw) ? (int) $raw : null);
        }
        $isActive = null;
        if (array_key_exists('isActive', $json)) {
            $isActive = (bool) $json['isActive'];
        }

        try {
            $input = new UpdateFsNoteInput(
                id: $id,
                category: self::optionalString($json, 'category'),
                label: self::optionalString($json, 'label'),
                body: self::optionalString($json, 'body'),
                sortOrder: $sortOrder,
                isActive: $isActive,
            );
            $out = $this->useCase->execute($input);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::ok(FsNoteJsonSerializer::toArray($out->note));
    }

    /**
     * @param array<string, mixed>|list<mixed> $json
     */
    private static function optionalString(array $json, string $key): ?string
    {
        if (!array_key_exists($key, $json)) {
            return null;
        }
        $v = $json[$key];
        return is_string($v) ? $v : null;
    }
}
