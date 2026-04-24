<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FinancialStatementNotes;

use Rucaro\Application\FinancialStatementNotes\ListFsNoteTemplatesUseCase;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FinancialStatementNotes\FsNoteJsonSerializer;

/** GET /api/v1/fs-note-templates */
final readonly class ListFsNoteTemplatesController
{
    public function __construct(
        private ListFsNoteTemplatesUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        if ($this->auth->authenticate($request->header('authorization')) === null) {
            return ErrorResponse::unauthorized();
        }
        $templates = $this->useCase->execute();
        return EnvelopeResponse::list(
            FsNoteJsonSerializer::templateList($templates),
            ['total' => count($templates)],
        );
    }
}
