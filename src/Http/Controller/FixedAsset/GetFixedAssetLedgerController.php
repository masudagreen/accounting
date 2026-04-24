<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FixedAsset;

use Rucaro\Application\FixedAsset\GetFixedAssetLedgerInput;
use Rucaro\Application\FixedAsset\GetFixedAssetLedgerUseCase;
use Rucaro\Domain\FixedAsset\FixedAssetLedgerGeneratorInterface;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FixedAsset\FixedAssetJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/fixed-assets/ledger?entityId=&fiscalTermId=&format=json|pdf */
final readonly class GetFixedAssetLedgerController
{
    public function __construct(
        private GetFixedAssetLedgerUseCase $useCase,
        private FixedAssetLedgerGeneratorInterface $generator,
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
        $fiscalTermId = $request->queryString('fiscalTermId');
        if ($fiscalTermId !== null && !UlidGenerator::isValid($fiscalTermId)) {
            return ErrorResponse::badRequest('fiscalTermId must be a valid ULID when provided.');
        }
        $fixedAssetId = $request->queryString('fixedAssetId');
        if ($fixedAssetId !== null && !UlidGenerator::isValid($fixedAssetId)) {
            return ErrorResponse::badRequest('fixedAssetId must be a valid ULID when provided.');
        }
        $format = strtolower($request->queryString('format') ?? 'json');

        $out = $this->useCase->execute(new GetFixedAssetLedgerInput(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fixedAssetId: $fixedAssetId,
        ));

        if ($format === 'pdf') {
            $pdf = $this->generator->render($out);
            return new JsonResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="fixed-assets.pdf"',
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }
        return EnvelopeResponse::ok(FixedAssetJsonSerializer::ledgerToArray($out));
    }
}
