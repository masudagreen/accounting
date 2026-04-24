<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FinancialStatement\Multi;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Rucaro\Application\FinancialStatement\Multi\GenerateMultiPeriodFinancialStatementInput;
use Rucaro\Application\FinancialStatement\Multi\GenerateMultiPeriodFinancialStatementUseCase;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FinancialStatement\Multi\MultiPeriodFinancialStatementGeneratorInterface;
use Rucaro\Infrastructure\FinancialStatement\Multi\MultiPeriodJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * GET /api/v1/financial-statements/multi
 *
 * Query params:
 *   - entityId        (required, ULID)
 *   - fiscalTermIds   (required, comma-separated list of ULIDs, 1〜5)
 *   - kind            (optional, BS|PL|CS|ALL; default ALL)
 *   - asOf            (optional, YYYY-MM-DD; default = each term's end date)
 *   - format          (optional, json|pdf; default json)
 *   - currencyCode    (optional, default JPY)
 *
 * Responses:
 *   - JSON: envelope { data: MultiPeriodFinancialStatements }
 *   - PDF:  `application/pdf` attachment (landscape)
 */
final readonly class GetMultiPeriodFinancialStatementController
{
    public function __construct(
        private GenerateMultiPeriodFinancialStatementUseCase $useCase,
        private MultiPeriodFinancialStatementGeneratorInterface $generator,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $userId = $this->auth->authenticate($request->header('authorization'));
        if ($userId === null) {
            return ErrorResponse::unauthorized();
        }

        $entityId = $request->queryString('entityId');
        $termsRaw = $request->queryString('fiscalTermIds');
        if ($entityId === null || $termsRaw === null) {
            return ErrorResponse::badRequest('entityId and fiscalTermIds query parameters are required.');
        }
        if (!UlidGenerator::isValid($entityId)) {
            return ErrorResponse::badRequest('entityId must be a valid ULID.');
        }

        $ids = self::parseIdList($termsRaw);
        if ($ids === null) {
            return ErrorResponse::badRequest(
                'fiscalTermIds must be a comma-separated list of valid ULIDs.',
            );
        }
        if (count($ids) < 1) {
            return ErrorResponse::badRequest('fiscalTermIds must contain at least one id.');
        }
        if (count($ids) > GenerateMultiPeriodFinancialStatementInput::MAX_PERIODS) {
            return ErrorResponse::badRequest(sprintf(
                'fiscalTermIds must not exceed %d entries.',
                GenerateMultiPeriodFinancialStatementInput::MAX_PERIODS,
            ));
        }

        $kind = FinancialStatementKind::fromQueryString($request->queryString('kind'));
        $asOf = self::parseDate($request->queryString('asOf'));
        $format = strtolower($request->queryString('format') ?? 'json');
        $currency = $request->queryString('currencyCode') ?? 'JPY';

        try {
            $multi = $this->useCase->execute(new GenerateMultiPeriodFinancialStatementInput(
                entityId: $entityId,
                fiscalTermIds: $ids,
                kind: $kind,
                asOf: $asOf,
                currencyCode: $currency,
            ));
        } catch (InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }

        if ($format === 'pdf') {
            $pdf = $this->generator->render($multi);
            $filename = sprintf(
                'multi-period-financial-statement-%s-%s.pdf',
                strtolower($kind->value),
                (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Ymd'),
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

        return EnvelopeResponse::ok(MultiPeriodJsonSerializer::toArray($multi));
    }

    /**
     * @return list<string>|null
     */
    private static function parseIdList(string $raw): ?array
    {
        $parts = array_map('trim', explode(',', $raw));
        $out = [];
        foreach ($parts as $p) {
            if ($p === '') {
                continue;
            }
            if (!UlidGenerator::isValid($p)) {
                return null;
            }
            $out[] = $p;
        }
        return $out;
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
}
