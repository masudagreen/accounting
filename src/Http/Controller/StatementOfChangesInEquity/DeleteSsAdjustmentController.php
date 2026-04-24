<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\StatementOfChangesInEquity;

use Rucaro\Application\StatementOfChangesInEquity\DeleteSsAdjustmentUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** DELETE /api/v1/ss-adjustments/{id} */
final readonly class DeleteSsAdjustmentController
{
    public function __construct(
        private DeleteSsAdjustmentUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        if ($this->auth->authenticate($request->header('authorization')) === null) {
            return ErrorResponse::unauthorized();
        }
        $id = $request->queryString('id');
        if ($id === null || !UlidGenerator::isValid($id)) {
            return ErrorResponse::badRequest('id must be a ULID.');
        }
        try {
            $this->useCase->execute($id);
        } catch (ValidationException $e) {
            return ErrorResponse::notFound($e->getMessage());
        }
        return EnvelopeResponse::ok(['id' => $id, 'deleted' => true]);
    }
}
