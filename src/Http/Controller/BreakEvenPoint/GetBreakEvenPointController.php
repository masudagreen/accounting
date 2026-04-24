<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\BreakEvenPoint;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Application\BreakEvenPoint\AnalyzeBreakEvenPointInput;
use Rucaro\Application\BreakEvenPoint\AnalyzeBreakEvenPointUseCase;
use Rucaro\Domain\BreakEvenPoint\BreakEvenPointPdfGeneratorInterface;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\BreakEvenPoint\BreakEvenPointJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/break-even-point?entityId=&fiscalTermId=&fromDate=&toDate=&format=json|pdf */
final readonly class GetBreakEvenPointController
{
    public function __construct(
        private AnalyzeBreakEvenPointUseCase $useCase,
        private BreakEvenPointPdfGeneratorInterface $generator,
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
            return ErrorResponse::badRequest('entityId must be a ULID.');
        }
        $fiscalTermId = $request->queryString('fiscalTermId');
        if ($fiscalTermId === null || !UlidGenerator::isValid($fiscalTermId)) {
            return ErrorResponse::badRequest('fiscalTermId must be a ULID.');
        }
        $fromDateRaw = $request->queryString('fromDate');
        $toDateRaw = $request->queryString('toDate');
        if ($fromDateRaw === null || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDateRaw)) {
            return ErrorResponse::badRequest('fromDate must be YYYY-MM-DD.');
        }
        if ($toDateRaw === null || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDateRaw)) {
            return ErrorResponse::badRequest('toDate must be YYYY-MM-DD.');
        }
        try {
            $fromDate = new DateTimeImmutable($fromDateRaw, new DateTimeZone('UTC'));
            $toDate = new DateTimeImmutable($toDateRaw, new DateTimeZone('UTC'));
        } catch (\Exception $e) {
            return ErrorResponse::badRequest('invalid date: ' . $e->getMessage());
        }
        if ($fromDate > $toDate) {
            return ErrorResponse::badRequest('fromDate must be <= toDate.');
        }
        $currency = strtoupper($request->queryString('currencyCode') ?? 'JPY');

        $analysis = $this->useCase->execute(new AnalyzeBreakEvenPointInput(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fromDate: $fromDate,
            toDate: $toDate,
            currencyCode: $currency,
        ));

        $format = strtolower($request->queryString('format') ?? 'json');
        if ($format === 'pdf') {
            $pdf = $this->generator->render($analysis);
            return new JsonResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="break-even-point.pdf"',
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }
        return EnvelopeResponse::ok(BreakEvenPointJsonSerializer::analysisToArray($analysis));
    }
}
