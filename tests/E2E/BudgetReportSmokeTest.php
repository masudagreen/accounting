<?php

declare(strict_types=1);

namespace Rucaro\Tests\E2E;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\Budget\AnalyzeBudgetVarianceInput;
use Rucaro\Application\Budget\AnalyzeBudgetVarianceUseCase;
use Rucaro\Application\Budget\ApproveBudgetUseCase;
use Rucaro\Application\Budget\BudgetLineItemInput;
use Rucaro\Application\Budget\CreateBudgetInput;
use Rucaro\Application\Budget\CreateBudgetUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Domain\Budget\BudgetStatus;
use Rucaro\Infrastructure\Budget\DompdfBudgetGenerator;
use Rucaro\Infrastructure\Budget\DompdfBudgetVarianceGenerator;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemoryBudgetRepository;
use Rucaro\Tests\Unit\Application\TrialBalance\InMemoryTrialBalanceQuery;
use Rucaro\Tests\Unit\Application\TrialBalance\InMemoryTrialBalanceSnapshotRepository;

/**
 * Smoke test for the Phase 6 Wave 6-G Budget port.
 *
 * Wires the full create → approve → variance pipeline against in-memory
 * adapters and confirms both 予算書.pdf and 予実対比.pdf render.
 *
 * Writes them to `RUCARO_E2E_PDF_OUT` when set so operators can eyeball
 * the output:
 *   RUCARO_E2E_PDF_OUT=/c/Users/yusuk/OneDrive/デスクトップ/rucaro-out \
 *     php vendor/bin/phpunit --group e2e
 */
#[CoversNothing]
#[Group('e2e')]
final class BudgetReportSmokeTest extends TestCase
{
    private const ENTITY_ID = '01HAAAAAAAAAAAAAAAAAAAAAA1';
    private const FISCAL_ID = '01HAAAAAAAAAAAAAAAAAAAAAA2';
    private const USER_ID   = '01HAAAAAAAAAAAAAAAAAAAAAA3';
    private const SALES_ID  = '01HAAAAAAAAAAAAAAAAAAAAAC0';
    private const COGS_ID   = '01HAAAAAAAAAAAAAAAAAAAAAC1';
    private const SGA_ID    = '01HAAAAAAAAAAAAAAAAAAAAAC2';

    public function testBudgetAndVariancePdfRoundTrip(): void
    {
        $clock = new FrozenClock('2026-09-30T00:00:00.000Z');
        $repo = new InMemoryBudgetRepository();
        $ulids = new UlidGenerator(new FrozenClock('2026-04-01T00:00:00.000Z'));

        // 1. Create a 2026 demo budget: 売上 150万 × 12 / 仕入 30万 × 12 / 販管費 5万 × 12
        $createUc = new CreateBudgetUseCase($repo, $ulids, new FrozenClock('2026-04-01T00:00:00.000Z'));
        $budget = $createUc->execute(new CreateBudgetInput(
            entityId: self::ENTITY_ID,
            fiscalTermId: self::FISCAL_ID,
            name: 'Demo Plan 2026',
            notes: '年間予算: 売上 1,800万 / 仕入 360万 / 販管費 60万',
            lineItems: [
                new BudgetLineItemInput(
                    accountTitleId: self::SALES_ID,
                    subAccountTitleId: null,
                    sortOrder: 0,
                    monthlyAmounts: array_fill(0, 12, '1500000.0000'),
                    memo: '売上',
                ),
                new BudgetLineItemInput(
                    accountTitleId: self::COGS_ID,
                    subAccountTitleId: null,
                    sortOrder: 1,
                    monthlyAmounts: array_fill(0, 12, '300000.0000'),
                    memo: '仕入',
                ),
                new BudgetLineItemInput(
                    accountTitleId: self::SGA_ID,
                    subAccountTitleId: null,
                    sortOrder: 2,
                    monthlyAmounts: array_fill(0, 12, '50000.0000'),
                    memo: '販管費',
                ),
            ],
            createdBy: self::USER_ID,
        ))->budget;

        // 2. Approve it.
        $approveUc = new ApproveBudgetUseCase($repo, $clock);
        $approved = $approveUc->execute($budget->id, self::USER_ID)->budget;
        self::assertSame(BudgetStatus::Approved, $approved->status);

        // 3. Feed six months of realistic actuals (slightly over on仕入, under on売上).
        $query = new InMemoryTrialBalanceQuery();
        $this->pushLine($query, self::SALES_ID, '4000', '売上',   'revenue', 'credit', 'credit', '8500000.0000');
        $this->pushLine($query, self::COGS_ID,  '5000', '仕入',   'expense', 'debit',  'debit',  '1900000.0000');
        $this->pushLine($query, self::SGA_ID,   '5500', '販管費', 'expense', 'debit',  'debit',  '310000.0000');

        $analyzeUc = new AnalyzeBudgetVarianceUseCase(
            budgets: $repo,
            trialBalance: new QueryTrialBalanceUseCase(
                $query,
                new InMemoryTrialBalanceSnapshotRepository(),
                $clock,
            ),
            clock: $clock,
        );
        $analysis = $analyzeUc->execute(new AnalyzeBudgetVarianceInput(
            budgetId: $approved->id,
            fiscalTermStartDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            asOf: new DateTimeImmutable('2026-09-30', new DateTimeZone('UTC')),
        ));
        self::assertNotEmpty($analysis->rows);

        // 4. Render PDFs.
        $repoRoot = dirname(__DIR__, 2);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'budget';
        $compileDir  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'smarty_budget_e2e';
        $fontDir     = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
        if (!is_dir($compileDir)) {
            @mkdir($compileDir, 0775, true);
        }

        $budgetGenerator = new DompdfBudgetGenerator(
            templateDir: $templateDir,
            compileDir: $compileDir,
            fontDir: $fontDir,
        );
        $varianceGenerator = new DompdfBudgetVarianceGenerator(
            templateDir: $templateDir,
            compileDir: $compileDir,
            fontDir: $fontDir,
        );

        $budgetPdf = $budgetGenerator->render($approved);
        $variancePdf = $varianceGenerator->render($analysis);

        self::assertStringStartsWith('%PDF-', $budgetPdf);
        self::assertStringStartsWith('%PDF-', $variancePdf);

        $outDir = (string) (getenv('RUCARO_E2E_PDF_OUT') ?: '');
        if ($outDir !== '' && is_dir($outDir)) {
            $base = rtrim($outDir, DIRECTORY_SEPARATOR . '/');
            file_put_contents($base . DIRECTORY_SEPARATOR . 'budget.pdf', $budgetPdf);
            file_put_contents($base . DIRECTORY_SEPARATOR . 'budget-variance.pdf', $variancePdf);
        }
    }

    private function pushLine(
        InMemoryTrialBalanceQuery $query,
        string $accountId,
        string $code,
        string $name,
        string $category,
        string $normalSide,
        string $side,
        string $amount,
    ): void {
        $query->addLine(
            entityId: self::ENTITY_ID,
            fiscalTermId: self::FISCAL_ID,
            date: new DateTimeImmutable('2026-09-15T00:00:00Z'),
            accountId: $accountId,
            accountCode: $code,
            accountName: $name,
            category: $category,
            normalSide: $normalSide,
            side: $side,
            amount: $amount,
        );
    }
}
