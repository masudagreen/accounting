<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\FixedAsset;

use DateTimeImmutable;
use DateTimeZone;
use Rucaro\Application\FixedAsset\CreateFixedAssetInput;
use Rucaro\Application\FixedAsset\CreateFixedAssetUseCase;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\FixedAsset\FixedAssetJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** POST /api/v1/fixed-assets */
final readonly class CreateFixedAssetController
{
    public function __construct(
        private CreateFixedAssetUseCase $useCase,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $userId = $this->auth->authenticate($request->header('authorization'));
        if ($userId === null) {
            return ErrorResponse::unauthorized();
        }
        $json = $request->json;
        if (!is_array($json)) {
            return ErrorResponse::badRequest('JSON body is required.');
        }

        $entityId = self::stringOr($json, 'entityId', '');
        if (!UlidGenerator::isValid($entityId)) {
            return ErrorResponse::badRequest('entityId must be a ULID.');
        }
        try {
            $input = new CreateFixedAssetInput(
                entityId: $entityId,
                assetCode: self::stringOr($json, 'assetCode', ''),
                assetName: self::stringOr($json, 'assetName', ''),
                categoryCode: self::stringOr($json, 'categoryCode', 'other'),
                assetAccountTitleId: self::nullableUlid($json, 'assetAccountTitleId'),
                accumulatedDepreciationAccountTitleId: self::nullableUlid($json, 'accumulatedDepreciationAccountTitleId'),
                depreciationExpenseAccountTitleId: self::nullableUlid($json, 'depreciationExpenseAccountTitleId'),
                acquisitionDate: self::requiredDate($json, 'acquisitionDate'),
                serviceStartDate: self::requiredDate($json, 'serviceStartDate'),
                acquisitionCost: self::stringOr($json, 'acquisitionCost', '0.0000'),
                residualValue: self::stringOr($json, 'residualValue', '0.0000'),
                usefulLifeYears: self::intOr($json, 'usefulLifeYears', 0),
                method: self::stringOr($json, 'method', 'straight_line'),
                quantity: self::intOr($json, 'quantity', 1),
                departmentCode: self::nullableString($json, 'departmentCode'),
                note: self::nullableString($json, 'note'),
                createdBy: $userId,
            );
            $output = $this->useCase->execute($input);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::ok(FixedAssetJsonSerializer::toArray($output->asset), null, 201);
    }

    /**
     * @param array<string, mixed>|list<mixed> $json
     */
    private static function stringOr(array $json, string $key, string $default): string
    {
        $v = $json[$key] ?? null;
        return is_string($v) && $v !== '' ? $v : $default;
    }

    /**
     * @param array<string, mixed>|list<mixed> $json
     */
    private static function intOr(array $json, string $key, int $default): int
    {
        $v = $json[$key] ?? null;
        if (is_int($v)) {
            return $v;
        }
        if (is_string($v) && ctype_digit($v)) {
            return (int) $v;
        }
        return $default;
    }

    /**
     * @param array<string, mixed>|list<mixed> $json
     */
    private static function nullableUlid(array $json, string $key): ?string
    {
        $v = $json[$key] ?? null;
        if (!is_string($v) || $v === '') {
            return null;
        }
        return UlidGenerator::isValid($v) ? $v : null;
    }

    /**
     * @param array<string, mixed>|list<mixed> $json
     */
    private static function nullableString(array $json, string $key): ?string
    {
        $v = $json[$key] ?? null;
        return is_string($v) && $v !== '' ? $v : null;
    }

    /**
     * @param array<string, mixed>|list<mixed> $json
     */
    private static function requiredDate(array $json, string $key): DateTimeImmutable
    {
        $v = $json[$key] ?? null;
        if (!is_string($v) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
            throw ValidationException::withErrors([
                $key => [sprintf('%s must be YYYY-MM-DD.', $key)],
            ]);
        }
        try {
            return new DateTimeImmutable($v, new DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw ValidationException::withErrors([$key => [$e->getMessage()]]);
        }
    }
}
