<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FinancialStatementNotes;

use Rucaro\Application\FinancialStatementNotes\ListFsNotesUseCase;
use Rucaro\Domain\FinancialStatementNotes\FsNotesPdfGeneratorInterface;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FinancialStatementNotes\FsNoteJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/fs-notes/export?entityId=&fiscalTermId=&format=json|pdf */
final readonly class ExportFsNotesController
{
    public function __construct(
        private ListFsNotesUseCase $useCase,
        private FsNotesPdfGeneratorInterface $generator,
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

        $notes = $this->useCase->execute($entityId, $fiscalTermId, true);
        $format = strtolower($request->queryString('format') ?? 'json');
        if ($format === 'pdf') {
            $pdf = $this->generator->render($notes, $entityId, $fiscalTermId);
            return new JsonResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="fs-notes.pdf"',
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }
        return EnvelopeResponse::list(
            FsNoteJsonSerializer::toArrayList($notes),
            ['total' => count($notes)],
        );
    }
}
