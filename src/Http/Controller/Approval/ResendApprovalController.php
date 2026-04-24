<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Approval;

use DateTimeZone;
use Rucaro\Application\Approval\ResendApprovalUseCase;
use Rucaro\Application\Approval\ResendApprovalUseCaseInput;
use Rucaro\Domain\Approval\Exception\TokenNotFoundException;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;

/**
 * `POST /api/v1/approvals/{prefix}/resend` — operator-driven re-issue.
 *
 * Requires Bearer authentication because it triggers an outbound mail /
 * message on behalf of the signed-in operator. Only the token prefix is
 * accepted so the plaintext capability is never round-tripped through this
 * endpoint.
 */
final readonly class ResendApprovalController
{
    public function __construct(
        private ResendApprovalUseCase $resend,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $userId = $this->auth->authenticate($request->header('authorization'));
        if ($userId === null) {
            return ErrorResponse::unauthorized();
        }

        $prefix = $request->queryString('prefix');
        if ($prefix === null) {
            return ErrorResponse::badRequest('prefix is required.');
        }

        try {
            $output = $this->resend->execute(new ResendApprovalUseCaseInput(
                tokenPrefix: $prefix,
                issuedByUserId: $userId,
            ));
        } catch (TokenNotFoundException $e) {
            return ErrorResponse::notFound($e->getMessage());
        } catch (EntityNotFoundException $e) {
            return ErrorResponse::notFound($e->getMessage());
        }

        return EnvelopeResponse::ok(data: [
            'tokenPrefix' => $output->tokenPrefix,
            'channel'     => $output->channel->value,
            'recipient'   => $output->recipient,
            'expiresAt'   => $output->expiresAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z'),
        ]);
    }
}
