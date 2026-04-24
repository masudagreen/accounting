<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FixedAsset;

use Rucaro\Application\FixedAsset\PostDepreciationJournalInput;
use Rucaro\Application\FixedAsset\PostDepreciationJournalUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** POST /api/v1/fixed-assets/depreciate-all — posts journals for all unposted schedule entries of the fiscal term. */
final readonly class PostDepreciationJournalController
{
    public function __construct(
        private PostDepreciationJournalUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $userId = $this->auth->authenticate($request->header('authorization'));
        if ($userId === null) {
            return ErrorResponse::unauthorized();
        }
        $json = is_array($request->json) ? $request->json : [];
        $entityId = is_string($json['entityId'] ?? null) ? (string) $json['entityId'] : null;
        $fiscalTermId = is_string($json['fiscalTermId'] ?? null) ? (string) $json['fiscalTermId'] : null;
        if ($entityId === null || !UlidGenerator::isValid($entityId)
            || $fiscalTermId === null || !UlidGenerator::isValid($fiscalTermId)
        ) {
            return ErrorResponse::badRequest('entityId and fiscalTermId are required ULIDs.');
        }
        try {
            $out = $this->useCase->execute(new PostDepreciationJournalInput(
                entityId: $entityId,
                fiscalTermId: $fiscalTermId,
                postedBy: $userId,
            ));
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        }
        return EnvelopeResponse::ok(['postings' => $out->postings]);
    }
}
