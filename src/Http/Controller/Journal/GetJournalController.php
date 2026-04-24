<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Journal;

use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;

final readonly class GetJournalController
{
    public function __construct(
        private JournalRepositoryInterface $journals,
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

        $journal = $this->journals->findById($id);
        if ($journal === null) {
            return ErrorResponse::notFound();
        }

        return EnvelopeResponse::ok(data: JournalSerializer::toArray($journal));
    }
}
