<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\BlueReturn;

use Rucaro\Application\BlueReturn\GetBlueReturnUseCase;
use Rucaro\Domain\BlueReturn\BlueReturnPdfGeneratorInterface;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\BlueReturn\BlueReturnJsonSerializer;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/** GET /api/v1/blue-returns/{id}?format=json|pdf */
final readonly class GetBlueReturnController
{
    public function __construct(
        private GetBlueReturnUseCase $useCase,
        private BlueReturnPdfGeneratorInterface $generator,
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
        $form = $this->useCase->execute($id);
        if ($form === null) {
            return ErrorResponse::notFound(sprintf('blue return %s not found.', $id));
        }
        $format = strtolower($request->queryString('format') ?? 'json');
        if ($format === 'pdf') {
            $pdf = $this->generator->render($form);
            return new JsonResponse(
                status: 200,
                headers: [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="blue-return.pdf"',
                    'Content-Length'      => (string) strlen($pdf),
                ],
                body: $pdf,
            );
        }
        return EnvelopeResponse::ok(BlueReturnJsonSerializer::toArray($form));
    }
}
