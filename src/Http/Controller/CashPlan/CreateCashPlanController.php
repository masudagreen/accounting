<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\CashPlan;

use Rucaro\Application\CashPlan\CashPlanEntryInput;
use Rucaro\Application\CashPlan\CreateCashPlanInput;
use Rucaro\Application\CashPlan\CreateCashPlanUseCase;
use Rucaro\Domain\CashPlan\CashPlanEntry;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\CashPlan\CashPlanJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** POST /api/v1/cash-plans */
final readonly class CreateCashPlanController
{
    public function __construct(
        private CreateCashPlanUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $userId = $this->auth->authenticate($request->header('authorization'));
        if ($userId === null) {
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

        try {
            $input = new CreateCashPlanInput(
                entityId: $entityId,
                fiscalTermId: $fiscalTermId,
                name: self::stringOr($json, 'name', ''),
                openingBalance: self::stringOr($json, 'openingBalance', '0.0000'),
                currencyCode: strtoupper(self::stringOr($json, 'currencyCode', 'JPY')),
                notes: self::nullableString($json, 'notes'),
                entries: self::parseEntries($json['entries'] ?? []),
                createdBy: $userId,
            );
            $out = $this->useCase->execute($input);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::ok(CashPlanJsonSerializer::toArray($out->plan), null, 201);
    }

    /**
     * @param mixed $raw
     * @return list<CashPlanEntryInput>
     */
    private static function parseEntries(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $idx => $entry) {
            if (!is_array($entry)) {
                continue;
            }
            $amounts = self::parseAmounts($entry['monthlyAmounts'] ?? []);
            $sortOrder = $entry['sortOrder'] ?? $idx;
            $out[] = new CashPlanEntryInput(
                category: is_string($entry['category'] ?? null) ? (string) $entry['category'] : '',
                label: is_string($entry['label'] ?? null) ? (string) $entry['label'] : '',
                sortOrder: is_int($sortOrder) ? $sortOrder : (int) $sortOrder,
                monthlyAmounts: $amounts,
                memo: self::nullableString($entry, 'memo'),
                id: self::nullableString($entry, 'id'),
            );
        }
        return $out;
    }

    /**
     * @param mixed $raw
     * @return list<string>
     */
    private static function parseAmounts(mixed $raw): array
    {
        $out = [];
        if (is_array($raw)) {
            // Accept either list form or associative `month_1..month_12`.
            for ($m = 1; $m <= CashPlanEntry::MONTHS; $m++) {
                $v = $raw[$m - 1] ?? $raw['month_' . $m] ?? '0.0000';
                $out[] = is_string($v) ? $v : (string) $v;
            }
        } else {
            for ($m = 1; $m <= CashPlanEntry::MONTHS; $m++) {
                $out[] = '0.0000';
            }
        }
        return $out;
    }

    /**
     * @param array<string, mixed>|list<mixed> $json
     */
    private static function stringOr(array $json, string $key, string $default): string
    {
        $v = $json[$key] ?? null;
        return is_string($v) && $v !== '' ? $v : $default;
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
