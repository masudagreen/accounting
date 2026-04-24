<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Approval;

use Rucaro\Application\Approval\RespondToApprovalUseCase;
use Rucaro\Application\Approval\RespondToApprovalUseCaseInput;
use Rucaro\Domain\Approval\ApprovalDecision;
use Rucaro\Domain\Approval\Exception\AlreadyRespondedException;
use Rucaro\Domain\Approval\Exception\TokenExpiredException;
use Rucaro\Domain\Approval\Exception\TokenNotFoundException;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Exception\InvariantViolationException;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;

/**
 * `POST /api/v1/approvals/{token}` — one-time consumption endpoint.
 *
 * Body: `{decision: 'approved'|'rejected', responseDetail?: string, actorEmail?: string}`.
 * The token is the capability so no Bearer is required. Returns:
 *   - 200 with the committed decision on success.
 *   - 404 when the token does not exist.
 *   - 410 Gone when the token expired or was already responded to.
 *   - 422 when the decision is missing or the target aggregate rejects the
 *     transition.
 */
final readonly class PostApprovalController
{
    public function __construct(
        private RespondToApprovalUseCase $respond,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $token = $request->queryString('token');
        if ($token === null) {
            return ErrorResponse::badRequest('token is required.');
        }

        $json = is_array($request->json) ? $request->json : [];
        $decisionRaw = is_string($json['decision'] ?? null) ? (string) $json['decision'] : '';
        $decision = ApprovalDecision::tryFrom($decisionRaw);
        if ($decision === null) {
            return ErrorResponse::unprocessable('decision is required.', [
                'decision' => ['must be either "approved" or "rejected"'],
            ]);
        }
        $responseDetail = is_string($json['responseDetail'] ?? null) ? (string) $json['responseDetail'] : '';
        $actorUserId = is_string($json['actorUserId'] ?? null) ? (string) $json['actorUserId'] : '';

        try {
            $output = $this->respond->execute(new RespondToApprovalUseCaseInput(
                tokenPlaintext: $token,
                decision: $decision,
                responseDetail: $responseDetail,
                actorUserId: $actorUserId,
            ));
        } catch (TokenNotFoundException $e) {
            return ErrorResponse::notFound($e->getMessage());
        } catch (TokenExpiredException $e) {
            return ErrorResponse::of(410, 'APPROVAL_TOKEN_EXPIRED', $e->getMessage(), [
                'context' => $e->context(),
            ]);
        } catch (AlreadyRespondedException $e) {
            return ErrorResponse::of(410, 'APPROVAL_TOKEN_ALREADY_RESPONDED', $e->getMessage(), [
                'context' => $e->context(),
            ]);
        } catch (EntityNotFoundException $e) {
            return ErrorResponse::notFound($e->getMessage());
        } catch (InvariantViolationException $e) {
            return ErrorResponse::of(422, $e->domainCode() ?? 'INVARIANT_VIOLATION', $e->getMessage(), [
                'context' => $e->context(),
            ]);
        } catch (ValidationException $e) {
            return ErrorResponse::unprocessable($e->getMessage(), $e->errors());
        }

        return EnvelopeResponse::ok(data: [
            'token'  => ApprovalSerializer::token($output->token, 'responded'),
            'target' => ApprovalSerializer::target($output->target),
        ]);
    }
}
