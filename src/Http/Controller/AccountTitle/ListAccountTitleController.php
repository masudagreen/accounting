<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\AccountTitle;

use DateTimeZone;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCaseInput;
use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;

final readonly class ListAccountTitleController
{
    public function __construct(
        private ListAccountTitlesUseCase $useCase,
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

        $output = $this->useCase->execute(new ListAccountTitlesUseCaseInput(
            entityId: $entityId,
            page: $page,
            pageSize: $pageSize,
            category: $request->queryString('category'),
            isActive: $request->queryBool('isActive'),
            search: $request->queryString('q'),
        ));

        $items = array_map(static fn (AccountTitle $a): array => [
            'id'          => $a->id,
            'entityId'    => $a->entityId,
            'code'        => $a->code,
            'name'        => $a->name,
            'category'    => $a->category,
            'normalSide'  => $a->normalSide,
            'parentId'    => $a->parentId,
            'sortOrder'   => $a->sortOrder,
            'isActive'    => $a->isActive,
            'createdAt'   => $a->createdAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z'),
            'updatedAt'   => $a->updatedAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z'),
        ], $output->items);

        return EnvelopeResponse::list($items, [
            'total'    => $output->total,
            'page'     => $output->page,
            'pageSize' => $output->pageSize,
        ]);
    }
}
