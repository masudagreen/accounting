<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\ConsumptionTax;

use Rucaro\Application\ConsumptionTax\UpsertAccountTitleTaxDefaultsUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\ConsumptionTax\ConsumptionTaxSettlementJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** PUT /api/v1/consumption-tax/account-title-defaults */
final readonly class PutAccountTitleTaxDefaultsController
{
    public function __construct(
        private UpsertAccountTitleTaxDefaultsUseCase $useCase,
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
        $rowsRaw = $json['rows'] ?? [];
        if (!is_array($rowsRaw)) {
            return ErrorResponse::badRequest('rows must be an array.');
        }
        /** @var list<array{accountTitleId: string, categoryCode: string, rateCode?: ?string}> $rows */
        $rows = [];
        foreach ($rowsRaw as $r) {
            if (!is_array($r)) {
                continue;
            }
            $at = is_string($r['accountTitleId'] ?? null) ? (string) $r['accountTitleId'] : '';
            $cat = is_string($r['categoryCode'] ?? null) ? (string) $r['categoryCode'] : '';
            $rate = array_key_exists('rateCode', $r) && is_string($r['rateCode']) ? (string) $r['rateCode'] : null;
            $rows[] = [
                'accountTitleId' => $at,
                'categoryCode'   => $cat,
                'rateCode'       => $rate,
            ];
        }
        try {
            $saved = $this->useCase->execute($entityId, $rows);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        }
        return EnvelopeResponse::list(
            ConsumptionTaxSettlementJsonSerializer::defaultsToArrayList($saved),
            ['total' => count($saved)],
        );
    }
}
