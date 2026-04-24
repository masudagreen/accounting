<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Ledger;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Application\Ledger\QueryLedgerUseCase;
use Rucaro\Application\Ledger\QueryLedgerUseCaseInput;
use Rucaro\Domain\Ledger\LedgerGeneratorInterface;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ledger\LedgerJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * GET /api/v1/ledger
 *
 * Query params:
 *   - entityId       (required, ULID)
 *   - fiscalTermId   (required, ULID)
 *   - accountTitleId (optional, ULID — when present only one book is returned)
 *   - from           (optional, YYYY-MM-DD — defaults to fiscal term start_date)
 *   - to             (optional, YYYY-MM-DD — defaults to fiscal term end_date)
 *   - format         (optional, json|pdf; default json)
 *
 * Responses:
 *   - JSON: envelope { data: Ledger }
 *   - PDF:  `application/pdf` attachment
 */
final readonly class GetLedgerController
{
    public function __construct(
        private QueryLedgerUseCase $useCase,
        private LedgerGeneratorInterface $generator,
        private AuthenticateBearer $auth,
        private PDO $pdo,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $userId = $this->auth->authenticate($request->header('authorization'));
        if ($userId === null) {
            return ErrorResponse::unauthorized();
        }

        $entityId = $request->queryString('entityId');
        $fiscalTermId = $request->queryString('fiscalTermId');
        if ($entityId === null || $fiscalTermId === null) {
            return ErrorResponse::badRequest('entityId and fiscalTermId query parameters are required.');
        }
        if (!UlidGenerator::isValid($entityId) || !UlidGenerator::isValid($fiscalTermId)) {
            return ErrorResponse::badRequest('entityId / fiscalTermId must be valid ULIDs.');
        }

        $accountTitleId = $request->queryString('accountTitleId');
        if ($accountTitleId !== null && !UlidGenerator::isValid($accountTitleId)) {
            return ErrorResponse::badRequest('accountTitleId must be a valid ULID when provided.');
        }

        [$termStart, $termEnd] = $this->lookupFiscalTermBounds($fiscalTermId);

        $from = self::parseDate($request->queryString('from'))
            ?? $termStart
            ?? new DateTimeImmutable('1970-01-01', new DateTimeZone('UTC'));
        $to = self::parseDate($request->queryString('to'))
            ?? $termEnd
            ?? new DateTimeImmutable('now', new DateTimeZone('UTC'));

        $format = strtolower($request->queryString('format') ?? 'json');

        $output = $this->useCase->execute(new QueryLedgerUseCaseInput(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            accountTitleId: $accountTitleId,
            fromDate: $from,
            toDate: $to,
        ));

        if ($format === 'pdf') {
            $pdf = $this->generator->render($output->ledger);
            return new JsonResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="ledger.pdf"',
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }

        return EnvelopeResponse::ok(LedgerJsonSerializer::toArray($output->ledger));
    }

    private static function parseDate(?string $raw): ?DateTimeImmutable
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * @return array{0: ?DateTimeImmutable, 1: ?DateTimeImmutable}
     */
    private function lookupFiscalTermBounds(string $fiscalTermId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT start_date, end_date FROM fiscal_terms WHERE id = :id LIMIT 1',
        );
        $stmt->execute([':id' => UlidGenerator::decode($fiscalTermId)]);
        /** @var array<string, string>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return [null, null];
        }
        return [
            self::parseDate((string) ($row['start_date'] ?? '')),
            self::parseDate((string) ($row['end_date'] ?? '')),
        ];
    }
}
