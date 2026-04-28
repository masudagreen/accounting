<?php

declare(strict_types=1);

namespace App\Domain\Banks\Stub;

use App\Domain\Banks\BankStatement;
use App\Domain\Banks\BankStatementImporter;
use App\Domain\Cash\CashDirection;
use App\Domain\Money\Money;

/**
 * テスト用 DryRun 銀行アダプタ.
 *
 * rawData は一切解析せず、固定の BankStatement リストを返す.
 * 各銀行の実アダプタ実装前の結合テスト・デモ用途に使用する.
 *
 * 実アダプタ実装時は BankStatementImporter を実装し、
 * BankAdapterRegistry::register() で登録すること.
 */
final class DryRunBankAdapter implements BankStatementImporter
{
    /**
     * @return list<BankStatement>
     */
    public function importStatements(string $rawData): array
    {
        return [
            new BankStatement(
                date: new \DateTimeImmutable('2026-04-01'),
                description: '振込入金 テスト株式会社',
                amount: Money::ofYen(100_000),
                direction: CashDirection::In,
                balanceAfter: Money::ofYen(600_000),
            ),
            new BankStatement(
                date: new \DateTimeImmutable('2026-04-10'),
                description: '公共料金引落 電力会社',
                amount: Money::ofYen(15_000),
                direction: CashDirection::Out,
                balanceAfter: Money::ofYen(585_000),
            ),
            new BankStatement(
                date: new \DateTimeImmutable('2026-04-20'),
                description: '家賃引落',
                amount: Money::ofYen(80_000),
                direction: CashDirection::Out,
                balanceAfter: Money::ofYen(505_000),
            ),
        ];
    }
}
