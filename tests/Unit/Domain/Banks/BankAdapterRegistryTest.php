<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Banks;

use App\Domain\Banks\BankAdapterRegistry;
use App\Domain\Banks\BankStatementImporter;
use App\Domain\Banks\Stub\DryRunBankAdapter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(BankAdapterRegistry::class)]
#[CoversClass(DryRunBankAdapter::class)]
final class BankAdapterRegistryTest extends TestCase
{
    #[Test]
    public function DryRunBankAdapterを登録して銀行コードで取得できる(): void
    {
        $registry = new BankAdapterRegistry();
        $adapter = new DryRunBankAdapter();

        $registry->register('dry-run', $adapter);

        $retrieved = $registry->find('dry-run');
        self::assertSame($adapter, $retrieved);
    }

    #[Test]
    public function 未登録の銀行コードはnullを返す(): void
    {
        $registry = new BankAdapterRegistry();

        $result = $registry->find('non-existent-bank');

        self::assertNull($result);
    }

    #[Test]
    public function 複数の銀行アダプタを登録して各自取得できる(): void
    {
        $registry = new BankAdapterRegistry();
        $adapterA = new DryRunBankAdapter();
        $adapterB = new DryRunBankAdapter();

        $registry->register('bank-a', $adapterA);
        $registry->register('bank-b', $adapterB);

        self::assertSame($adapterA, $registry->find('bank-a'));
        self::assertSame($adapterB, $registry->find('bank-b'));
    }

    #[Test]
    public function 同じ銀行コードを上書き登録できる(): void
    {
        $registry = new BankAdapterRegistry();
        $adapterOld = new DryRunBankAdapter();
        $adapterNew = new DryRunBankAdapter();

        $registry->register('bank-x', $adapterOld);
        $registry->register('bank-x', $adapterNew);

        self::assertSame($adapterNew, $registry->find('bank-x'));
    }

    #[Test]
    public function DryRunBankAdapterは固定のBankStatementリストを返す(): void
    {
        $adapter = new DryRunBankAdapter();
        $statements = $adapter->importStatements('');

        self::assertNotEmpty($statements);
        foreach ($statements as $stmt) {
            self::assertTrue($stmt->amount()->isPositive());
        }
    }

    #[Test]
    public function DryRunBankAdapterはBankStatementImporterインターフェースを実装している(): void
    {
        $adapter = new DryRunBankAdapter();

        self::assertInstanceOf(BankStatementImporter::class, $adapter);
    }

    #[Test]
    public function DryRunBankAdapterのimportStatementsはrawDataを無視して固定リストを返す(): void
    {
        $adapter = new DryRunBankAdapter();

        $statementsA = $adapter->importStatements('any-data');
        $statementsB = $adapter->importStatements('');

        self::assertCount(count($statementsA), $statementsB);
    }
}
