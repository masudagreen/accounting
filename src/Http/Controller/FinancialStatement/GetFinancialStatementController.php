<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FinancialStatement;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCase;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCaseInput;
use Rucaro\Domain\FinancialStatement\FinancialStatementGeneratorInterface;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FinancialStatement\JsonFinancialStatementSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * GET /api/v1/financial-statements
 *
 * Query params:
 *   - entityId     (required, ULID)
 *   - fiscalTermId (required, ULID)
 *   - kind         (optional, BS|PL|CS|ALL; default ALL)
 *   - asOf         (optional, YYYY-MM-DD; default today)
 *   - from         (optional, YYYY-MM-DD; defaults to fiscal term start)
 *   - format       (optional, json|pdf; default json)
 *
 * Responses:
 *   - JSON: envelope { data: FinancialStatements }
 *   - PDF:  `application/pdf` attachment
 */
final readonly class GetFinancialStatementController
{
    public function __construct(
        private GenerateFinancialStatementUseCase $useCase,
        private FinancialStatementGeneratorInterface $generator,
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

        $kind = FinancialStatementKind::fromQueryString($request->queryString('kind'));

        $asOf = self::parseDate($request->queryString('asOf'))
            ?? new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $from = self::parseDate($request->queryString('from'))
            ?? $this->lookupFiscalTermStart($fiscalTermId)
            ?? $asOf;

        $format = strtolower($request->queryString('format') ?? 'json');

        $fs = $this->useCase->execute(new GenerateFinancialStatementUseCaseInput(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            kind: $kind,
            fromDate: $from,
            asOf: $asOf,
        ));

        if ($format === 'pdf') {
            $pdf = $this->generator->render($fs);
            $filename = sprintf(
                'financial-statement-%s-%s.pdf',
                strtolower($kind->value),
                $asOf->format('Ymd'),
            );
            return new JsonResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }

        return EnvelopeResponse::ok(JsonFinancialStatementSerializer::toArray($fs));
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

    private function lookupFiscalTermStart(string $fiscalTermId): ?DateTimeImmutable
    {
        $stmt = $this->pdo->prepare('SELECT start_date FROM fiscal_terms WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => UlidGenerator::decode($fiscalTermId)]);
        /** @var string|false $raw */
        $raw = $stmt->fetchColumn();
        if ($raw === false || !is_string($raw) || $raw === '') {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }
}
