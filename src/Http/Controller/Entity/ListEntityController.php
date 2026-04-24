<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Entity;

use DateTimeZone;
use Rucaro\Application\Entity\ListEntitiesUseCase;
use Rucaro\Application\Entity\ListEntitiesUseCaseInput;
use Rucaro\Domain\Entity\Entity;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;

final readonly class ListEntityController
{
    public function __construct(
        private ListEntitiesUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $userId = $this->auth->authenticate($request->header('authorization'));
        if ($userId === null) {
            return ErrorResponse::unauthorized();
        }

        $page = $request->positiveInt('page', 1, 1);
        $pageSize = $request->positiveInt('pageSize', 50, 1, 200);

        $output = $this->useCase->execute(new ListEntitiesUseCaseInput(
            ownerUserId: $userId,
            page: $page,
            pageSize: $pageSize,
            search: $request->queryString('q'),
            isActive: $request->queryBool('isActive'),
        ));

        $items = array_map(static fn (Entity $e): array => [
            'id'               => $e->id,
            'ownerUserId'      => $e->ownerUserId,
            'name'             => $e->name,
            'nationCode'       => $e->nationCode,
            'currencyCode'     => $e->currencyCode,
            'fiscalStartMmDd'  => $e->fiscalStartMmDd,
            'isActive'         => $e->isActive,
            'createdAt'        => $e->createdAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z'),
            'updatedAt'        => $e->updatedAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z'),
            'deletedAt'        => $e->deletedAt?->setTimezone(new DateTimeZone('UTC'))?->format('Y-m-d\TH:i:s.u\Z'),
        ], $output->items);

        return EnvelopeResponse::list($items, [
            'total'    => $output->total,
            'page'     => $output->page,
            'pageSize' => $output->pageSize,
        ]);
    }
}
