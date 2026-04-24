<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\CashPlan;

use Rucaro\Application\CashPlan\CashPlanEntryInput;
use Rucaro\Application\CashPlan\UpdateCashPlanInput;
use Rucaro\Application\CashPlan\UpdateCashPlanUseCase;
use Rucaro\Domain\CashPlan\CashPlanEntry;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\CashPlan\CashPlanJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** PATCH /api/v1/cash-plans/{id} */
final readonly class UpdateCashPlanController
{
    public function __construct(
        private UpdateCashPlanUseCase $useCase,
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

        try {
            $input = new UpdateCashPlanInput(
                id: $id,
                name: self::optionalString($json, 'name'),
                openingBalance: self::optionalString($json, 'openingBalance'),
                currencyCode: self::optionalString($json, 'currencyCode'),
                notes: self::optionalString($json, 'notes'),
                entries: array_key_exists('entries', $json) ? self::parseEntries($json['entries']) : null,
            );
            $out = $this->useCase->execute($input);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::ok(CashPlanJsonSerializer::toArray($out->plan));
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
    private static function optionalString(array $json, string $key): ?string
    {
        $v = $json[$key] ?? null;
        return is_string($v) ? $v : null;
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
