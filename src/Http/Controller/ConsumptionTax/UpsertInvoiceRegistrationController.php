<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\ConsumptionTax;

use Rucaro\Application\ConsumptionTax\UpsertInvoiceRegistrationUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\ConsumptionTax\ConsumptionTaxSettlementJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** POST /api/v1/consumption-tax/invoice-registrations */
final readonly class UpsertInvoiceRegistrationController
{
    public function __construct(
        private UpsertInvoiceRegistrationUseCase $useCase,
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
        $id = is_string($json['id'] ?? null) && $json['id'] !== '' ? (string) $json['id'] : null;
        if ($id !== null && !UlidGenerator::isValid($id)) {
            return ErrorResponse::badRequest('id must be a ULID when provided.');
        }
        $entityId = is_string($json['entityId'] ?? null) ? (string) $json['entityId'] : '';
        if (!UlidGenerator::isValid($entityId)) {
            return ErrorResponse::badRequest('entityId must be a ULID.');
        }
        try {
            $reg = $this->useCase->execute(
                id: $id,
                entityId: $entityId,
                counterpartyName: is_string($json['counterpartyName'] ?? null) ? (string) $json['counterpartyName'] : '',
                registrationNumber: is_string($json['registrationNumber'] ?? null) && $json['registrationNumber'] !== '' ? (string) $json['registrationNumber'] : null,
                isRegistered: (bool) ($json['isRegistered'] ?? false),
                registeredFrom: is_string($json['registeredFrom'] ?? null) && $json['registeredFrom'] !== '' ? (string) $json['registeredFrom'] : null,
                registeredUntil: is_string($json['registeredUntil'] ?? null) && $json['registeredUntil'] !== '' ? (string) $json['registeredUntil'] : null,
                notes: is_string($json['notes'] ?? null) && $json['notes'] !== '' ? (string) $json['notes'] : null,
            );
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::ok(
            ConsumptionTaxSettlementJsonSerializer::invoiceRegistrationToArray($reg),
            null,
            $id === null ? 201 : 200,
        );
    }
}
