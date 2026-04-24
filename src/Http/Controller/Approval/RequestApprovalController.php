<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Approval;

use DateTimeZone;
use Rucaro\Application\Approval\IssueApprovalTokenUseCase;
use Rucaro\Application\Approval\IssueApprovalTokenUseCaseInput;
use Rucaro\Domain\Approval\ApprovalChannel;
use Rucaro\Domain\Approval\ApprovalTargetKind;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Response\EnvelopeResponse;
use Rucaro\Http\Response\ErrorResponse;
use Rucaro\Http\Response\JsonResponse;
use Rucaro\Http\ServerRequest;

/**
 * `POST /api/v1/journals/{id}/request-approval` — issue a fresh approval
 * token and dispatch it over the configured channel.
 *
 * Query: `?id={journalId}` (legacy style consistent with other Journal
 * routes). Body: `{channel, recipient, ttlHours?}`.
 */
final readonly class RequestApprovalController
{
    public function __construct(
        private IssueApprovalTokenUseCase $issue,
        private AuthenticateBearer $auth,
    ) {
    }

    public function __invoke(ServerRequest $request): JsonResponse
    {
        $userId = $this->auth->authenticate($request->header('authorization'));
        if ($userId === null) {
            return ErrorResponse::unauthorized();
        }

        $journalId = $request->queryString('id');
        if ($journalId === null) {
            return ErrorResponse::badRequest('Journal id is required.');
        }

        $json = is_array($request->json) ? $request->json : [];
        $channelRaw = is_string($json['channel'] ?? null) ? (string) $json['channel'] : 'null';
        $channel = ApprovalChannel::tryFrom($channelRaw);
        if ($channel === null) {
            return ErrorResponse::unprocessable('Invalid channel.', [
                'channel' => ['must be one of: email, line, slack, discord, null'],
            ]);
        }

        $recipient = is_string($json['recipient'] ?? null) ? trim((string) $json['recipient']) : '';
        if ($channel !== ApprovalChannel::Null && $recipient === '') {
            return ErrorResponse::unprocessable('Recipient is required for non-null channels.', [
                'recipient' => ['must not be empty'],
            ]);
        }

        $ttlHours = null;
        if (isset($json['ttlHours']) && (is_int($json['ttlHours']) || (is_string($json['ttlHours']) && ctype_digit($json['ttlHours'])))) {
            $ttlHours = (int) $json['ttlHours'];
            if ($ttlHours < 1) {
                return ErrorResponse::unprocessable('ttlHours must be a positive integer.', [
                    'ttlHours' => ['must be >= 1'],
                ]);
            }
        }

        try {
            $output = $this->issue->execute(new IssueApprovalTokenUseCaseInput(
                targetKind: ApprovalTargetKind::Journal,
                targetId: $journalId,
                channel: $channel,
                recipient: $recipient,
                issuedByUserId: $userId,
                ttlHours: $ttlHours,
            ));
        } catch (EntityNotFoundException $e) {
            return ErrorResponse::notFound($e->getMessage());
        }

        return EnvelopeResponse::ok(
            data: [
                'tokenPrefix' => $output->tokenPrefix,
                'channel'     => $output->channel->value,
                'recipient'   => $output->recipient,
                'expiresAt'   => $output->expiresAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z'),
            ],
            status: 201,
        );
    }
}
