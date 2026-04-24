<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FixedAsset;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Application\FixedAsset\DisposeFixedAssetUseCase;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FixedAsset\FixedAssetJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** POST /api/v1/fixed-assets/{id}/dispose */
final readonly class DisposeFixedAssetController
{
    public function __construct(
        private DisposeFixedAssetUseCase $useCase,
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
        $disposalDateRaw = is_array($request->json) ? ($request->json['disposalDate'] ?? null) : null;
        if (!is_string($disposalDateRaw) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $disposalDateRaw)) {
            return ErrorResponse::badRequest('disposalDate must be YYYY-MM-DD.');
        }
        try {
            $asset = $this->useCase->execute(
                $id,
                new DateTimeImmutable($disposalDateRaw, new DateTimeZone('UTC')),
            );
        } catch (EntityNotFoundException) {
            return ErrorResponse::notFound('Fixed asset not found.');
        }
        return EnvelopeResponse::ok(FixedAssetJsonSerializer::toArray($asset));
    }
}
