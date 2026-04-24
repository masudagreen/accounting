<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Approval;

use Rucaro\Application\Approval\FindApprovalByTokenUseCase;
use Rucaro\Domain\Approval\Exception\TokenNotFoundException;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;

/**
 * `GET /api/v1/approvals/{token}` — capability URL.
 *
 * Unauthenticated: the token itself is the capability. Returns:
 *   - 200 with the token + target payload when active or responded (so the
 *     reviewer can see what they previously decided).
 *   - 404 when the token has never existed.
 *   - 410 Gone when the token has expired without a response.
 *
 * The token is taken from `?token=…` so the route collector doesn't need
 * path parameter handling.
 */
final readonly class GetApprovalController
{
    public function __construct(
        private FindApprovalByTokenUseCase $find,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $token = $request->queryString('token');
        if ($token === null) {
            return ErrorResponse::badRequest('token is required.');
        }

        try {
            $output = $this->find->execute($token);
        } catch (TokenNotFoundException $e) {
            return ErrorResponse::notFound($e->getMessage());
        } catch (EntityNotFoundException $e) {
            return ErrorResponse::notFound($e->getMessage());
        }

        if ($output->status === 'expired') {
            return ErrorResponse::of(410, 'APPROVAL_TOKEN_EXPIRED', 'Approval token has expired.');
        }

        return EnvelopeResponse::ok(data: [
            'token'  => ApprovalSerializer::token($output->token, $output->status),
            'target' => ApprovalSerializer::target($output->target),
        ]);
    }
}
