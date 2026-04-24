<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FixedAsset;

use Rucaro\Application\FixedAsset\UpdateFixedAssetUseCase;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FixedAsset\FixedAssetJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** PATCH /api/v1/fixed-assets/{id} */
final readonly class UpdateFixedAssetController
{
    public function __construct(
        private UpdateFixedAssetUseCase $useCase,
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
        $json = is_array($request->json) ? $request->json : [];
        /** @var array<string, mixed> $patch */
        $patch = [];
        foreach ([
            'assetName', 'categoryCode', 'assetAccountTitleId', 'accumulatedDepreciationAccountTitleId',
            'depreciationExpenseAccountTitleId', 'residualValue', 'usefulLifeYears', 'method',
            'quantity', 'departmentCode', 'note',
        ] as $key) {
            if (array_key_exists($key, $json)) {
                $patch[$key] = $json[$key];
            }
        }
        try {
            /** @var array{
             *     assetName?: string,
             *     categoryCode?: string,
             *     assetAccountTitleId?: ?string,
             *     accumulatedDepreciationAccountTitleId?: ?string,
             *     depreciationExpenseAccountTitleId?: ?string,
             *     residualValue?: string,
             *     usefulLifeYears?: int,
             *     method?: string,
             *     quantity?: int,
             *     departmentCode?: ?string,
             *     note?: ?string,
             * } $patch */
            $asset = $this->useCase->execute($id, $patch);
        } catch (EntityNotFoundException) {
            return ErrorResponse::notFound('Fixed asset not found.');
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        }
        return EnvelopeResponse::ok(FixedAssetJsonSerializer::toArray($asset));
    }
}
