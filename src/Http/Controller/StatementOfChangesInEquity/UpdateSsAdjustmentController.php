<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\StatementOfChangesInEquity;

use Rucaro\Application\StatementOfChangesInEquity\UpdateSsAdjustmentInput;
use Rucaro\Application\StatementOfChangesInEquity\UpdateSsAdjustmentUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\StatementOfChangesInEquity\StatementOfChangesInEquityJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** PATCH /api/v1/ss-adjustments/{id} */
final readonly class UpdateSsAdjustmentController
{
    public function __construct(
        private UpdateSsAdjustmentUseCase $useCase,
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

        $section = null;
        if (array_key_exists('sectionCode', $json)) {
            $raw = is_string($json['sectionCode']) ? (string) $json['sectionCode'] : '';
            $section = SsSectionCode::tryFrom($raw);
            if ($section === null) {
                return ErrorResponse::badRequest('sectionCode must be one of the SS section codes.');
            }
        }
        $changeType = null;
        if (array_key_exists('changeTypeCode', $json)) {
            $raw = is_string($json['changeTypeCode']) ? (string) $json['changeTypeCode'] : '';
            $changeType = SsChangeType::tryFrom($raw);
            if ($changeType === null) {
                return ErrorResponse::badRequest('changeTypeCode must be one of the SS change types.');
            }
        }

        $amount = null;
        if (array_key_exists('amount', $json)) {
            $raw = $json['amount'];
            if (!is_numeric($raw)) {
                return ErrorResponse::badRequest('amount must be numeric when provided.');
            }
            $amount = (string) $raw;
        }

        $sortOrder = null;
        if (array_key_exists('sortOrder', $json)) {
            $raw = $json['sortOrder'];
            if (!is_numeric($raw)) {
                return ErrorResponse::badRequest('sortOrder must be numeric when provided.');
            }
            $sortOrder = (int) $raw;
        }

        $label = array_key_exists('label', $json) && is_string($json['label'])
            ? (string) $json['label']
            : null;
        $notes = array_key_exists('notes', $json) && is_string($json['notes'])
            ? (string) $json['notes']
            : null;

        try {
            $input = new UpdateSsAdjustmentInput(
                id: $id,
                sectionCode: $section,
                changeType: $changeType,
                amount: $amount,
                label: $label,
                sortOrder: $sortOrder,
                notes: $notes,
            );
            $out = $this->useCase->execute($input);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::ok(
            StatementOfChangesInEquityJsonSerializer::adjustmentToArray($out->adjustment),
        );
    }
}
