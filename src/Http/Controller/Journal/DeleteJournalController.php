<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Journal;

use Rucaro\Application\Journal\DeleteJournalUseCase;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;

final readonly class DeleteJournalController
{
    public function __construct(
        private DeleteJournalUseCase $useCase,
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

        try {
            $this->useCase->execute($id, $userId);
        } catch (EntityNotFoundException $e) {
            return ErrorResponse::notFound($e->getMessage());
        } catch (InvariantViolationException $e) {
            return ErrorResponse::of(422, $e->domainCode() ?? 'INVARIANT_VIOLATION', $e->getMessage(), [
                'context' => $e->context(),
            ]);
        }

        return EnvelopeResponse::ok(data: ['id' => $id, 'deleted' => true]);
    }
}
