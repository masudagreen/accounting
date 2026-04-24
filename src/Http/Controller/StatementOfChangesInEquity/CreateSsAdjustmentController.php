<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\StatementOfChangesInEquity;

use Rucaro\Application\StatementOfChangesInEquity\CreateSsAdjustmentInput;
use Rucaro\Application\StatementOfChangesInEquity\CreateSsAdjustmentUseCase;
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

/** POST /api/v1/ss-adjustments */
final readonly class CreateSsAdjustmentController
{
    public function __construct(
        private CreateSsAdjustmentUseCase $useCase,
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

        $entityId = self::stringOr($json, 'entityId', '');
        if (!UlidGenerator::isValid($entityId)) {
            return ErrorResponse::badRequest('entityId must be a ULID.');
        }
        $fiscalTermId = self::stringOr($json, 'fiscalTermId', '');
        if (!UlidGenerator::isValid($fiscalTermId)) {
            return ErrorResponse::badRequest('fiscalTermId must be a ULID.');
        }

        $sectionRaw = self::stringOr($json, 'sectionCode', '');
        $section = SsSectionCode::tryFrom($sectionRaw);
        if ($section === null) {
            return ErrorResponse::badRequest('sectionCode must be one of the SS section codes.');
        }
        $changeRaw = self::stringOr($json, 'changeTypeCode', '');
        $changeType = SsChangeType::tryFrom($changeRaw);
        if ($changeType === null) {
            return ErrorResponse::badRequest('changeTypeCode must be one of the SS change types.');
        }
        $amount = self::stringOr($json, 'amount', '');
        if ($amount === '' || !is_numeric($amount)) {
            return ErrorResponse::badRequest('amount is required and must be numeric.');
        }

        try {
            $input = new CreateSsAdjustmentInput(
                entityId: $entityId,
                fiscalTermId: $fiscalTermId,
                sectionCode: $section,
                changeType: $changeType,
                amount: $amount,
                label: self::stringOr($json, 'label', $changeType->label()),
                sortOrder: self::intOr($json, 'sortOrder', 0),
                notes: self::nullableString($json, 'notes'),
            );
            $out = $this->useCase->execute($input);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::ok(
            StatementOfChangesInEquityJsonSerializer::adjustmentToArray($out->adjustment),
            null,
            201,
        );
    }

    /**
     * @param array<string, mixed>|list<mixed> $json
     */
    private static function stringOr(array $json, string $key, string $default): string
    {
        $v = $json[$key] ?? null;
        if (is_string($v) && $v !== '') {
            return $v;
        }
        if (is_numeric($v)) {
            return (string) $v;
        }
        return $default;
    }

    /**
     * @param array<string, mixed>|list<mixed> $json
     */
    private static function intOr(array $json, string $key, int $default): int
    {
        $v = $json[$key] ?? null;
        if (is_int($v)) {
            return $v;
        }
        if (is_numeric($v)) {
            return (int) $v;
        }
        return $default;
    }

    /**
     * @param array<string, mixed>|list<mixed> $json
     */
    private static function nullableString(array $json, string $key): ?string
    {
        $v = $json[$key] ?? null;
        return is_string($v) && $v !== '' ? $v : null;
    }
}
