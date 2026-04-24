<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FixedAsset;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Application\FixedAsset\GenerateDepreciationScheduleInput;
use Rucaro\Application\FixedAsset\GenerateDepreciationScheduleUseCase;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FixedAsset\FixedAssetJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * POST /api/v1/fixed-assets/depreciate
 *
 * Body / query params:
 *   - entityId     (required, ULID)
 *   - fiscalTermId (required, ULID)
 *   - fixedAssetId (optional, ULID — generate for one asset only)
 */
final readonly class GenerateDepreciationController
{
    public function __construct(
        private GenerateDepreciationScheduleUseCase $useCase,
        private AuthenticateBearer $auth,
        private PDO $pdo,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        if ($this->auth->authenticate($request->header('authorization')) === null) {
            return ErrorResponse::unauthorized();
        }
        $entityId = $request->queryString('entityId') ?? self::jsonString($request, 'entityId');
        $fiscalTermId = $request->queryString('fiscalTermId') ?? self::jsonString($request, 'fiscalTermId');
        $fixedAssetId = $request->queryString('fixedAssetId') ?? self::jsonString($request, 'fixedAssetId');
        if ($entityId === null || !UlidGenerator::isValid($entityId)
            || $fiscalTermId === null || !UlidGenerator::isValid($fiscalTermId)
        ) {
            return ErrorResponse::badRequest('entityId and fiscalTermId (ULID) are required.');
        }
        if ($fixedAssetId !== null && !UlidGenerator::isValid($fixedAssetId)) {
            return ErrorResponse::badRequest('fixedAssetId must be a valid ULID when provided.');
        }
        [$termStart, $termEnd] = $this->lookupFiscalTermBounds($fiscalTermId);
        if ($termStart === null || $termEnd === null) {
            return ErrorResponse::badRequest('fiscalTermId does not resolve to a known fiscal term.');
        }
        $out = $this->useCase->execute(new GenerateDepreciationScheduleInput(
            entityId: $entityId,
            fiscalTermId: $fiscalTermId,
            fiscalTermStart: $termStart,
            fiscalTermEnd: $termEnd,
            fixedAssetId: $fixedAssetId,
        ));
        $data = array_map(
            static fn ($e): array => FixedAssetJsonSerializer::scheduleEntryToArray($e),
            $out->entries,
        );
        return EnvelopeResponse::ok(['entries' => $data]);
    }

    private static function jsonString(ServerRequest $request, string $key): ?string
    {
        $v = is_array($request->json) ? ($request->json[$key] ?? null) : null;
        return is_string($v) && $v !== '' ? $v : null;
    }

    /**
     * @return array{0: ?DateTimeImmutable, 1: ?DateTimeImmutable}
     */
    private function lookupFiscalTermBounds(string $fiscalTermId): array
    {
        $stmt = $this->pdo->prepare('SELECT start_date, end_date FROM fiscal_terms WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => UlidGenerator::decode($fiscalTermId)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return [null, null];
        }
        return [
            self::parseDate((string) ($row['start_date'] ?? '')),
            self::parseDate((string) ($row['end_date'] ?? '')),
        ];
    }

    private static function parseDate(string $raw): ?DateTimeImmutable
    {
        if ($raw === '' || !preg_match('/^\d{4}-\d{2}-\d{2}/', $raw)) {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }
}
