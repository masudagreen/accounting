<?php

declare(strict_types=1);

namespace App\Domain\Ledger;

/**
 * 元帳エントリの絞込条件.
 *
 * 期間 / 部門 / 補助科目 の組合せ.
 */
final readonly class LedgerFilter
{
    private function __construct(
        private ?\DateTimeImmutable $from = null,
        private ?\DateTimeImmutable $to = null,
        private ?string $departmentId = null,
        private ?string $subAccountTitleId = null,
    ) {
    }

    public static function none(): self
    {
        return new self();
    }

    public static function byDateRange(\DateTimeImmutable $from, \DateTimeImmutable $to): self
    {
        return new self(from: $from, to: $to);
    }

    public static function byDepartment(string $departmentId): self
    {
        return new self(departmentId: $departmentId);
    }

    public static function bySubAccount(string $subAccountTitleId): self
    {
        return new self(subAccountTitleId: $subAccountTitleId);
    }

    public function withDateRange(\DateTimeImmutable $from, \DateTimeImmutable $to): self
    {
        return new self($from, $to, $this->departmentId, $this->subAccountTitleId);
    }

    public function withDepartment(string $departmentId): self
    {
        return new self($this->from, $this->to, $departmentId, $this->subAccountTitleId);
    }

    public function withSubAccount(string $subAccountTitleId): self
    {
        return new self($this->from, $this->to, $this->departmentId, $subAccountTitleId);
    }

    public function matches(LedgerEntry $entry): bool
    {
        if ($this->from !== null && $entry->date() < $this->from) {
            return false;
        }
        if ($this->to !== null && $entry->date() > $this->to) {
            return false;
        }
        if ($this->departmentId !== null && $entry->departmentId() !== $this->departmentId) {
            return false;
        }
        if ($this->subAccountTitleId !== null && $entry->subAccountTitleId() !== $this->subAccountTitleId) {
            return false;
        }
        return true;
    }
}
