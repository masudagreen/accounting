<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\StatementOfChangesInEquity;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Application\StatementOfChangesInEquity\GenerateStatementOfChangesInEquityInput;
use Rucaro\Application\StatementOfChangesInEquity\GenerateStatementOfChangesInEquityUseCase;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;
use Rucaro\Domain\StatementOfChangesInEquity\StatementOfChangesInEquityPdfGeneratorInterface;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\StatementOfChangesInEquity\StatementOfChangesInEquityJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/statement-of-changes-in-equity?entityId=&fiscalTermId=&format=json|pdf */
final readonly class GetStatementOfChangesInEquityController
{
    public function __construct(
        private GenerateStatementOfChangesInEquityUseCase $useCase,
        private StatementOfChangesInEquityPdfGeneratorInterface $pdf,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        if ($this->auth->authenticate($request->header('authorization')) === null) {
            return ErrorResponse::unauthorized();
        }

        $entityId = $request->queryString('entityId');
        if ($entityId === null || !UlidGenerator::isValid($entityId)) {
            return ErrorResponse::badRequest('entityId query parameter is required and must be a ULID.');
        }
        $fiscalTermId = $request->queryString('fiscalTermId');
        if ($fiscalTermId === null || !UlidGenerator::isValid($fiscalTermId)) {
            return ErrorResponse::badRequest('fiscalTermId query parameter is required and must be a ULID.');
        }

        $utc = new DateTimeZone('UTC');
        try {
            $fromDate = new DateTimeImmutable(
                $request->queryString('fromDate') ?? 'first day of January ' . date('Y'),
                $utc,
            );
            $toDate = new DateTimeImmutable(
                $request->queryString('toDate') ?? 'last day of December ' . date('Y'),
                $utc,
            );
        } catch (\Exception $e) {
            return ErrorResponse::badRequest('fromDate / toDate must be ISO-8601 dates.');
        }

        $netIncomeRaw = $request->queryString('netIncome');
        $netIncome = null;
        if ($netIncomeRaw !== null && $netIncomeRaw !== '') {
            if (!is_numeric($netIncomeRaw)) {
                return ErrorResponse::badRequest('netIncome must be numeric when provided.');
            }
            $netIncome = (string) $netIncomeRaw;
        }

        $openingBalances = self::parseOpeningBalances($request);

        $input = new GenerateStatementOfChangesInEquityInput(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fromDate: $fromDate,
            toDate: $toDate,
            currencyCode: $request->queryString('currencyCode') ?? 'JPY',
            openingBalances: $openingBalances,
            netIncome: $netIncome,
        );

        try {
            $ss = $this->useCase->execute($input);
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }

        $format = strtolower($request->queryString('format') ?? 'json');
        if ($format === 'pdf') {
            $pdf = $this->pdf->render($ss);
            return new JsonResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="statement-of-changes-in-equity.pdf"',
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }
        return EnvelopeResponse::ok(StatementOfChangesInEquityJsonSerializer::statementToArray($ss));
    }

    /**
     * Reads `opening[<section_code>]=<decimal>` query-string entries.
     *
     * @return array<string, string>
     */
    private static function parseOpeningBalances(ServerRequest $request): array
    {
        $out = [];
        foreach (SsSectionCode::ordered() as $code) {
            $raw = $request->queryString('opening.' . $code->value);
            if ($raw !== null && $raw !== '' && is_numeric($raw)) {
                $out[$code->value] = (string) $raw;
            }
        }
        return $out;
    }
}
