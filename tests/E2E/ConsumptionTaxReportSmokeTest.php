<?php

declare(strict_types=1);

namespace Rucaro\Tests\E2E;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\ConsumptionTax\CalculateConsumptionTaxUseCase;
use Rucaro\Application\ConsumptionTax\GenerateConsumptionTaxReportUseCase;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCalculationMethod;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryCode;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriod;
use Rucaro\Domain\ConsumptionTax\Service\ConsumptionTaxCalculatorFactory;
use Rucaro\Domain\ConsumptionTax\TaxableTransaction;
use Rucaro\Infrastructure\ConsumptionTax\DompdfConsumptionTaxReportGenerator;
use Rucaro\Tests\Support\Fake\InMemoryConsumptionTaxPeriodRepository;
use Rucaro\Tests\Support\Fake\InMemoryTaxableTransactionQuery;

/**
 * Smoke test for the Phase 6 Wave 6-F port.
 *
 * Wires UseCase + PDF generator against in-memory adapters and confirms
 * the resulting PDF renders 消費税申告書イメージ.
 *
 * When RUCARO_E2E_PDF_OUT is set, the PDF is also written there so the
 * operator can eyeball the output. Example:
 *   RUCARO_E2E_PDF_OUT=/c/Users/yusuk/OneDrive/デスクトップ/rucaro-out \
 *     php vendor/bin/phpunit --group e2e
 */
#[CoversNothing]
#[Group('e2e')]
final class ConsumptionTaxReportSmokeTest extends TestCase
{
    public function testConsumptionTaxReportPdfRoundTrip(): void
    {
        $now = new DateTimeImmutable('2026-04-21T12:00:00Z', new DateTimeZone('UTC'));
        $period = new ConsumptionTaxPeriod(
            id: '01HAAAAAAAAAAAAAAAAAAAAAB0',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAB1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAB2',
            periodFrom: new DateTimeImmutable('2026-04-01'),
            periodTo: new DateTimeImmutable('2027-03-31'),
            calculationMethod: ConsumptionTaxCalculationMethod::Principle,
            simplifiedBusinessCategory: null,
            isInterim: false,
            settlementStatus: 'pending',
            settledAt: null,
            createdAt: $now,
            updatedAt: $now,
        );
        $periods = new InMemoryConsumptionTaxPeriodRepository();
        $periods->save($period);

        // Seeded scenario: 売上 1,600,000 / 仕入 200,000 / 販管費 17,000
        $transactions = new InMemoryTaxableTransactionQuery([
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-05-15'),
                categoryCode: ConsumptionTaxCategoryCode::TaxableSales,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '1600000.0000',
                taxAmount: '160000.0000',
            ),
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-05-20'),
                categoryCode: ConsumptionTaxCategoryCode::TaxablePurchase,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '200000.0000',
                taxAmount: '20000.0000',
            ),
            new TaxableTransaction(
                bookedOn: new DateTimeImmutable('2026-05-25'),
                categoryCode: ConsumptionTaxCategoryCode::TaxablePurchase,
                ratePercent: '10.00',
                isReduced: false,
                amountExcludingTax: '17000.0000',
                taxAmount: '1700.0000',
            ),
        ]);

        $calculate = new CalculateConsumptionTaxUseCase(
            $periods,
            $transactions,
            new ConsumptionTaxCalculatorFactory(),
        );
        $report = new GenerateConsumptionTaxReportUseCase($calculate);
        $settlement = $report->execute($period->id);

        self::assertSame('160000.0000', $settlement->outputTax);
        self::assertSame('21700.0000', $settlement->deductibleInputTax);
        self::assertSame('138300.0000', $settlement->netTaxPayable);

        $repoRoot = dirname(__DIR__, 2);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'consumption_tax';
        $compileDir  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'smarty_consumption_tax_e2e';
        $fontDir     = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
        if (!is_dir($compileDir)) {
            @mkdir($compileDir, 0775, true);
        }

        $generator = new DompdfConsumptionTaxReportGenerator(
            templateDir: $templateDir,
            compileDir: $compileDir,
            fontDir: $fontDir,
        );
        $pdf = $generator->render($settlement);

        self::assertNotSame('', $pdf);
        self::assertStringStartsWith('%PDF-', $pdf);

        $outDir = (string) (getenv('RUCARO_E2E_PDF_OUT') ?: '');
        if ($outDir !== '' && is_dir($outDir)) {
            file_put_contents(
                rtrim($outDir, DIRECTORY_SEPARATOR . '/') . DIRECTORY_SEPARATOR . 'consumption-tax-report.pdf',
                $pdf,
            );
        }
    }
}
