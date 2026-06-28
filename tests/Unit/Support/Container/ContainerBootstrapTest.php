<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Support\Container;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\Ledger\OpeningBalanceRepositoryInterface;
use Rucaro\Infrastructure\Ledger\PdoOpeningBalanceRepository;
use Rucaro\Infrastructure\Ledger\ZeroOpeningBalanceRepository;
use Rucaro\Support\Container\ContainerBootstrap;

/**
 * Smoke test for {@see ContainerBootstrap}.
 *
 * Phase B-3a: Production must wire {@see PdoOpeningBalanceRepository} as the
 * default {@see OpeningBalanceRepositoryInterface}. Without this binding the
 * trial balance and ledger views silently drop the 期首繰越 of multi-period
 * imports — see RC-8 in `plan/reality-check-report.md`.
 */
#[CoversClass(ContainerBootstrap::class)]
final class ContainerBootstrapTest extends TestCase
{
    public function testOpeningBalanceRepositoryDefaultsToPdoImplementation(): void
    {
        $pdo = $this->newSqliteInMemory();
        $container = ContainerBootstrap::build($pdo);

        $repo = $container->get(OpeningBalanceRepositoryInterface::class);

        self::assertInstanceOf(
            PdoOpeningBalanceRepository::class,
            $repo,
            'OpeningBalanceRepositoryInterface must default to the PDO-backed repository '
            .'so opening_balances rows produced by the importer are reflected in TB / Ledger.',
        );
        self::assertNotInstanceOf(
            ZeroOpeningBalanceRepository::class,
            $repo,
            'ZeroOpeningBalanceRepository would silently drop carry-forward; it is no longer the default.',
        );
    }

    private function newSqliteInMemory(): \PDO
    {
        // No schema needed — the test only checks wiring, not query behaviour.
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}
