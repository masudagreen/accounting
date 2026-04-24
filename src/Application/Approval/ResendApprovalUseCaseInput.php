<?php

declare(strict_types=1);

namespace Rucaro\Application\Approval;

/**
 * Input DTO for {@see ResendApprovalUseCase}. Operators only ever supply
 * the token prefix — the plaintext is never transmitted through this path.
 */
final readonly class ResendApprovalUseCaseInput
{
    public function __construct(
        public string $tokenPrefix,
        public string $issuedByUserId,
    ) {
    }
}
