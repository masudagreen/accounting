<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FixedAsset;

use Rucaro\Application\FixedAsset\ListFixedAssetsUseCase;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FixedAsset\FixedAssetJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/fixed-assets?entityId=...&includeDisposed=1 */
final readonly class ListFixedAssetController
{
    public function __construct(
        private ListFixedAssetsUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        if ($this->auth->authenticate($request->header('authorization')) === null) {
            return ErrorResponse::unauthorized();
        }
        $entityId = $request->queryString('entityId');
        if ($entityId === null || !UlidGenerator::isValid($entityId)) {
            return ErrorResponse::badRequest('entityId query parameter is required and must be a ULID.');
        }
        $includeDisposed = $request->queryBool('includeDisposed') === true;
        $assets = $this->useCase->execute($entityId, $includeDisposed);
        $data = array_map(
            static fn ($a): array => FixedAssetJsonSerializer::toArray($a),
            $assets,
        );
        return EnvelopeResponse::ok(['items' => $data]);
    }
}
