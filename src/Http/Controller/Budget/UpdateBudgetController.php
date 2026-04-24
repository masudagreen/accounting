<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Budget;

use Rucaro\Application\Budget\BudgetLineItemInput;
use Rucaro\Application\Budget\UpdateBudgetInput;
use Rucaro\Application\Budget\UpdateBudgetUseCase;
use Rucaro\Domain\Budget\BudgetLineItem;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Budget\BudgetJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** PATCH /api/v1/budgets/{id} */
final readonly class UpdateBudgetController
{
    public function __construct(
        private UpdateBudgetUseCase $useCase,
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
            $input = new UpdateBudgetInput(
                id: $id,
                name: self::optionalString($json, 'name'),
                notes: self::optionalString($json, 'notes'),
                lineItems: array_key_exists('lineItems', $json)
                    ? self::parseLineItems($json['lineItems'])
                    : null,
            );
            $out = $this->useCase->execute($input);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (InvariantViolationException $e) {
            return ErrorResponse::of(409, 'INVARIANT_VIOLATION', $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::ok(BudgetJsonSerializer::toArray($out->budget));
    }

    /**
     * @param mixed $raw
     * @return list<BudgetLineItemInput>
     */
    private static function parseLineItems(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $idx => $li) {
            if (!is_array($li)) {
                continue;
            }
            $amounts = self::parseAmounts($li['monthlyAmounts'] ?? []);
            $sortOrder = $li['sortOrder'] ?? $idx;
            $out[] = new BudgetLineItemInput(
                accountTitleId: is_string($li['accountTitleId'] ?? null) ? (string) $li['accountTitleId'] : '',
                subAccountTitleId: self::nullableString($li, 'subAccountTitleId'),
                sortOrder: is_int($sortOrder) ? $sortOrder : (int) $sortOrder,
                monthlyAmounts: $amounts,
                memo: self::nullableString($li, 'memo'),
                id: self::nullableString($li, 'id'),
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
            for ($m = 1; $m <= BudgetLineItem::MONTHS; $m++) {
                $v = $raw[$m - 1] ?? $raw['month_' . $m] ?? '0.0000';
                $out[] = is_string($v) ? $v : (string) $v;
            }
        } else {
            for ($m = 1; $m <= BudgetLineItem::MONTHS; $m++) {
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
