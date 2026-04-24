<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FixedAsset;

use Rucaro\Application\FixedAsset\GetFixedAssetUseCase;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FixedAsset\FixedAssetJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/fixed-assets/{id} */
final readonly class GetFixedAssetController
{
    public function __construct(
        private GetFixedAssetUseCase $useCase,
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
            return ErrorResponse::badRequest('id path parameter must be a ULID.');
        }
        $asset = $this->useCase->execute($id);
        if ($asset === null) {
            return ErrorResponse::notFound('Fixed asset not found.');
        }
        return EnvelopeResponse::ok(FixedAssetJsonSerializer::toArray($asset));
    }
}
