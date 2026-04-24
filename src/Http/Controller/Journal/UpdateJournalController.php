<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Journal;

use Rucaro\Application\Journal\JournalLineInput;
use Rucaro\Application\Journal\UpdateJournalUseCase;
use Rucaro\Application\Journal\UpdateJournalUseCaseInput;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;

final readonly class UpdateJournalController
{
    public function __construct(
        private UpdateJournalUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $userId = $this->auth->authenticate($request->header('authorization'));
        if ($userId === null) {
            return ErrorResponse::unauthorized();
        }

        $id = $request->queryString('id');
        if ($id === null) {
            return ErrorResponse::badRequest('Journal id is required.');
        }

        $body = $request->json;
        if (!is_array($body)) {
            return ErrorResponse::badRequest('Request body must be a JSON object.');
        }

        $linesRaw = $body['lines'] ?? null;
        if (!is_array($linesRaw) || $linesRaw === []) {
            return ErrorResponse::unprocessable('lines is required.', [
                'lines' => ['lines must be a non-empty array'],
            ]);
        }

        try {
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

            $summary = isset($body['summary']) && is_string($body['summary']) ? $body['summary'] : null;

            $journal = $this->useCase->execute(new UpdateJournalUseCaseInput(
                journalId: $id,
                updatedBy: $userId,
                lines: $lines,
                summary: $summary,
            ));
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable('Journal payload failed validation.', $e->errors());
        } catch (InvariantViolationException $e) {
            return ErrorResponse::of(422, $e->domainCode() ?? 'INVARIANT_VIOLATION', $e->getMessage(), [
                'context' => $e->context(),
            ]);
        } catch (EntityNotFoundException $e) {
            return ErrorResponse::notFound($e->getMessage());
        }

        return EnvelopeResponse::ok(data: JournalSerializer::toArray($journal));
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
