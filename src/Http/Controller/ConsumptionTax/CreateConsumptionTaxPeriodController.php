<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\ConsumptionTax;

use Rucaro\Application\ConsumptionTax\CreateConsumptionTaxPeriodUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\ConsumptionTax\ConsumptionTaxSettlementJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** POST /api/v1/consumption-tax/periods */
final readonly class CreateConsumptionTaxPeriodController
{
    public function __construct(
        private CreateConsumptionTaxPeriodUseCase $useCase,
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
        $fiscalTermId = is_string($json['fiscalTermId'] ?? null) ? (string) $json['fiscalTermId'] : '';
        if (!UlidGenerator::isValid($fiscalTermId)) {
            return ErrorResponse::badRequest('fiscalTermId must be a ULID.');
        }
        try {
            $period = $this->useCase->execute(
                entityId: $entityId,
                fiscalTermId: $fiscalTermId,
                periodFromIso: is_string($json['periodFrom'] ?? null) ? (string) $json['periodFrom'] : '',
                periodToIso: is_string($json['periodTo'] ?? null) ? (string) $json['periodTo'] : '',
                method: is_string($json['method'] ?? null) ? (string) $json['method'] : 'principle',
                simplifiedBusinessCategory: isset($json['simplifiedBusinessCategory']) && is_int($json['simplifiedBusinessCategory'])
                    ? (int) $json['simplifiedBusinessCategory']
                    : null,
                isInterim: (bool) ($json['isInterim'] ?? false),
            );
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        } catch (\Exception $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::ok(
            ConsumptionTaxSettlementJsonSerializer::periodToArray($period),
            null,
            201,
        );
    }
}
