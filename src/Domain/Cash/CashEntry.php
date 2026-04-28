<?php

declare(strict_types=1);

namespace App\Domain\Cash;

use App\Domain\Money\Money;

/**
 * 収支ログ1件.
 *
 * 収入・支出を簡易に記録し、後で仕訳へ変換する値オブジェクト.
 *
 * 不変条件:
 *  - id は非空文字
 *  - amount は正 (0 および負は不可)
 *  - cashAccountTitleId / counterAccountTitleId は非空文字
 */
final readonly class CashEntry
{
    private function __construct(
        private string $id,
        private \DateTimeImmutable $date,
        private CashDirection $direction,
        private Money $amount,
        private string $counterAccountTitleId,
        private string $cashAccountTitleId,
        private ?string $description,
        private CashEntryStatus $status,
    ) {
    }

    public static function of(
        string $id,
        \DateTimeImmutable $date,
        CashDirection $direction,
        Money $amount,
        string $counterAccountTitleId,
        string $cashAccountTitleId,
        ?string $description,
        CashEntryStatus $status,
    ): self {
        if ($id === '') {
            throw new \InvalidArgumentException('id must not be empty');
        }
        if (! $amount->isPositive()) {
            throw new \InvalidArgumentException('amount must be positive');
        }
        if ($counterAccountTitleId === '') {
            throw new \InvalidArgumentException('counterAccountTitleId must not be empty');
        }
        if ($cashAccountTitleId === '') {
            throw new \InvalidArgumentException('cashAccountTitleId must not be empty');
        }

        return new self(
            $id,
            $date,
            $direction,
            $amount,
            $counterAccountTitleId,
            $cashAccountTitleId,
            $description,
            $status,
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function date(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function direction(): CashDirection
    {
        return $this->direction;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function counterAccountTitleId(): string
    {
        return $this->counterAccountTitleId;
    }

    public function cashAccountTitleId(): string
    {
        return $this->cashAccountTitleId;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function status(): CashEntryStatus
    {
        return $this->status;
    }

    /**
     * ステータスを変更した新しいインスタンスを返す (immutable).
     */
    public function withStatus(CashEntryStatus $status): self
    {
        return new self(
            $this->id,
            $this->date,
            $this->direction,
            $this->amount,
            $this->counterAccountTitleId,
            $this->cashAccountTitleId,
            $this->description,
            $status,
        );
    }
}
