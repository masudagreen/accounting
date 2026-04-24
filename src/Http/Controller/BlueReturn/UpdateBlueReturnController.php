<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\BlueReturn;

use Rucaro\Application\BlueReturn\UpdateBlueReturnInput;
use Rucaro\Application\BlueReturn\UpdateBlueReturnUseCase;
use Rucaro\Domain\BlueReturn\BlueReturnFormType;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\BlueReturn\BlueReturnJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** PATCH /api/v1/blue-returns/{id} */
final readonly class UpdateBlueReturnController
{
    public function __construct(
        private UpdateBlueReturnUseCase $useCase,
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
        $json = $request->json;
        if (!is_array($json)) {
            return ErrorResponse::badRequest('JSON body is required.');
        }

        $formType = null;
        if (isset($json['formType']) && is_string($json['formType'])) {
            $formType = BlueReturnFormType::tryFrom($json['formType']);
            if ($formType === null) {
                return ErrorResponse::badRequest('formType must be one of general / agricultural / real_estate.');
            }
        }

        $snapshot = null;
        if (array_key_exists('snapshot', $json)) {
            if (!is_array($json['snapshot'])) {
                return ErrorResponse::badRequest('snapshot must be an object.');
            }
            $snapshot = $json['snapshot'];
        }

        try {
            $out = $this->useCase->execute(new UpdateBlueReturnInput(
                id: $id,
                formType: $formType,
                snapshot: $snapshot,
            ));
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        } catch (InvariantViolationException $e) {
            return ErrorResponse::of(409, 'INVARIANT_VIOLATION', $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return ErrorResponse::badRequest($e->getMessage());
        }
        return EnvelopeResponse::ok(BlueReturnJsonSerializer::toArray($out->form));
    }
}
