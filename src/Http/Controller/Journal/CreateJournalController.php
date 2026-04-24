<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Journal;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Application\Journal\CreateJournalUseCase;
use Rucaro\Application\Journal\CreateJournalUseCaseInput;
use Rucaro\Application\Journal\JournalLineInput;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;

final readonly class CreateJournalController
{
    public function __construct(
        private CreateJournalUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $userId = $this->auth->authenticate($request->header('authorization'));
        if ($userId === null) {
            return ErrorResponse::unauthorized();
        }

        $body = $request->json;
        if (!is_array($body)) {
            return ErrorResponse::badRequest('Request body must be a JSON object.');
        }

        try {
            $input = $this->buildInput($body, $userId);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable('Journal payload failed validation.', $e->errors());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }

        try {
            $journal = $this->useCase->execute($input);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable('Journal payload failed validation.', $e->errors());
        } catch (InvariantViolationException $e) {
            return ErrorResponse::of(422, $e->domainCode() ?? 'INVARIANT_VIOLATION', $e->getMessage(), [
                'invariant' => (string) ($e->context()['invariant'] ?? 'unknown'),
                'context'   => $e->context(),
            ]);
        }

        return EnvelopeResponse::ok(data: JournalSerializer::toArray($journal), status: 201);
    }

    /**
     * @param array<string, mixed>|list<mixed> $body
     */
    private function buildInput(array $body, string $userId): CreateJournalUseCaseInput
    {
        $entityId = self::requireString($body, 'entityId');
        $fiscalTermId = self::requireString($body, 'fiscalTermId');
        $journalDateRaw = self::requireString($body, 'journalDate');

        try {
            $journalDate = new DateTimeImmutable($journalDateRaw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            throw ValidationException::withErrors([
                'journalDate' => ['journalDate must be an ISO 8601 date (YYYY-MM-DD)'],
            ]);
        }

        $summary = isset($body['summary']) && is_string($body['summary']) ? $body['summary'] : '';
        $source = isset($body['source']) && is_string($body['source']) ? $body['source'] : 'manual';
        $currency = isset($body['currencyCode']) && is_string($body['currencyCode'])
            ? $body['currencyCode']
            : 'JPY';
        $sourceReceiptId = isset($body['sourceReceiptId']) && is_string($body['sourceReceiptId'])
            ? $body['sourceReceiptId']
            : null;

        $linesRaw = $body['lines'] ?? null;
        if (!is_array($linesRaw) || $linesRaw === []) {
            throw ValidationException::withErrors([
                'lines' => ['lines must be a non-empty array'],
            ]);
        }

        $lines = [];
        foreach (array_values($linesRaw) as $idx => $line) {
            if (!is_array($line)) {
                throw ValidationException::withErrors([
                    sprintf('lines[%d]', $idx) => ['line must be a JSON object'],
                ]);
            }
            $lines[] = new JournalLineInput(
                side: self::requireString($line, 'side'),
                accountTitleId: self::requireString($line, 'accountTitleId'),
                subAccountTitleId: isset($line['subAccountTitleId']) && is_string($line['subAccountTitleId'])
                    ? $line['subAccountTitleId']
                    : null,
                amount: self::requireString($line, 'amount'),
                taxRatePercent: isset($line['taxRatePercent']) && is_string($line['taxRatePercent'])
                    ? $line['taxRatePercent']
                    : '0.00',
                taxAmount: isset($line['taxAmount']) && is_string($line['taxAmount'])
                    ? $line['taxAmount']
                    : '0.0000',
                isTaxReduced: isset($line['isTaxReduced']) && is_bool($line['isTaxReduced'])
                    ? $line['isTaxReduced']
                    : false,
                memo: isset($line['memo']) && is_string($line['memo']) ? $line['memo'] : '',
            );
        }

        return new CreateJournalUseCaseInput(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            journalDate: $journalDate,
            summary: $summary,
            source: $source,
            sourceReceiptId: $sourceReceiptId,
            currencyCode: $currency,
            createdBy: $userId,
            lines: $lines,
        );
    }

    /**
     * @param array<string, mixed>|list<mixed> $body
     */
    private static function requireString(array $body, string $field): string
    {
        $v = $body[$field] ?? null;
        if (!is_string($v) || $v === '') {
            throw ValidationException::withErrors([
                $field => [sprintf("'%s' is required and must be a non-empty string", $field)],
            ]);
        }
        return $v;
    }
}
