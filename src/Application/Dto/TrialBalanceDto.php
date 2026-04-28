<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * 試算表の1行を UI に渡すための DTO.
 *
 * Smarty テンプレートに渡しやすい配列へ変換できる.
 */
final readonly class TrialBalanceDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly int $opening,
        public readonly int $periodDebits,
        public readonly int $periodCredits,
        public readonly int $closing,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'opening'      => $this->opening,
            'periodDebits' => $this->periodDebits,
            'periodCredits' => $this->periodCredits,
            'closing'      => $this->closing,
        ];
    }
}
