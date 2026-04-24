<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Journal;

use Rucaro\Application\Journal\ListJournalsUseCase;
use Rucaro\Application\Journal\ListJournalsUseCaseInput;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;

final readonly class ListJournalController
{
    public function __construct(
        private ListJournalsUseCase $useCase,
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
        if ($entityId === null) {
            return ErrorResponse::badRequest('entityId query parameter is required.');
        }

        $page = $request->positiveInt('page', 1, 1);
        $pageSize = $request->positiveInt('pageSize', 50, 1, 200);

        $output = $this->useCase->execute(new ListJournalsUseCaseInput(
            entityId: $entityId,
            page: $page,
            pageSize: $pageSize,
            fiscalTermId: $request->queryString('fiscalTermId'),
            from: $request->queryString('from'),
            to: $request->queryString('to'),
            status: $request->queryString('status'),
            source: $request->queryString('source'),
            search: $request->queryString('q'),
            includeTrashed: $request->queryBool('includeTrashed') ?? false,
        ));

        $items = array_map(
            static fn ($j): array => JournalSerializer::toArray($j),
            $output->items,
        );

        return EnvelopeResponse::list($items, [
            'total'    => $output->total,
            'page'     => $output->page,
            'pageSize' => $output->pageSize,
        ]);
    }
}
