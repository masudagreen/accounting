<?php

declare(strict_types=1);

namespace Rucaro\Http\Controller\Approval;

use DateTimeZone;
use Rucaro\Domain\Approval\ApprovalTargetInterface;
use Rucaro\Domain\Approval\ApprovalToken;

/**
 * Consistent JSON serialisation for approval-related responses.
 *
 * Split out so every controller under Controller/Approval uses identical
 * field names; keeps the API contract self-describing.
 */
final class ApprovalSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function token(ApprovalToken $token, string $status): array
    {
        return [
            'id'             => $token->id,
            'prefix'         => $token->tokenPrefix,
            'targetKind'     => $token->targetKind->value,
            'targetId'       => $token->targetId,
            'channel'        => $token->channel->value,
            'recipient'      => $token->recipient,
            'issuedAt'       => self::fmt($token->issuedAt),
            'expiresAt'      => self::fmt($token->expiresAt),
            'respondedAt'    => $token->respondedAt !== null ? self::fmt($token->respondedAt) : null,
            'decision'       => $token->decision?->value,
            'responseDetail' => $token->responseDetail,
            'status'         => $status,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function target(ApprovalTargetInterface $target): array
    {
        return [
            'kind'    => $target->kind()->value,
            'id'      => $target->id(),
            'summary' => $target->summary(),
            'details' => $target->details(),
        ];
    }

    private static function fmt(\DateTimeImmutable $t): string
    {
        return $t->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z');
    }
}
