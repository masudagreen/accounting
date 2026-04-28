<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Report\CorporateFinancialStatements;

use App\Domain\FinancialStatement\EquitySection;
use App\Domain\FinancialStatement\StatementOfEquity;
use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Money\Money;
use App\Domain\Report\CorporateFinancialStatements\CorporateSsData;
use App\Domain\Report\CorporateFinancialStatements\CorporateSsRenderer;
use App\Domain\Report\ReportFormat;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 法人 株主資本等変動計算書 Renderer のユニットテスト.
 */
final class CorporateSsRendererTest extends TestCase
{
    private CorporateSsRenderer $renderer;
    private FiscalPeriod $period;
    private StatementOfEquity $equity;

    protected function setUp(): void
    {
        $this->renderer = new CorporateSsRenderer();
        $this->period   = FiscalPeriod::of(2024, 4, 12, 1);

        $this->equity = new StatementOfEquity(
            openingBalances: [
                EquitySection::CapitalStock->value     => Money::ofYen(10_000_000),
                EquitySection::RetainedEarnings->value => Money::ofYen(5_000_000),
            ],
            changes: [],
            closingBalances: [
                EquitySection::CapitalStock->value     => Money::ofYen(10_000_000),
                EquitySection::RetainedEarnings->value => Money::ofYen(12_210_000),
            ],
            totalEquityOpening: Money::ofYen(15_000_000),
            totalChange: Money::ofYen(7_210_000),
            totalEquityClosing: Money::ofYen(22_210_000),
        );
    }

    #[Test]
    public function renderReturnsNonEmptyHtml(): void
    {
        $data = new CorporateSsData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            equity: $this->equity,
        );

        $html = $this->renderer->render($data);

        self::assertNotEmpty($html);
        self::assertGreaterThan(100, strlen($html));
    }

    #[Test]
    public function renderContainsCompanyName(): void
    {
        $data = new CorporateSsData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            equity: $this->equity,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('株式会社テスト', $html);
    }

    #[Test]
    public function renderContainsStatementTitle(): void
    {
        $data = new CorporateSsData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            equity: $this->equity,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('株主資本等変動計算書', $html);
    }

    #[Test]
    public function renderContainsOpeningBalance(): void
    {
        $data = new CorporateSsData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            equity: $this->equity,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('15,000,000', $html);
    }

    #[Test]
    public function renderContainsClosingBalance(): void
    {
        $data = new CorporateSsData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            equity: $this->equity,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('22,210,000', $html);
    }

    #[Test]
    public function renderContainsRequiredLabels(): void
    {
        $data = new CorporateSsData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            equity: $this->equity,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('当期首残高', $html);
        self::assertStringContainsString('当期変動額', $html);
        self::assertStringContainsString('当期末残高', $html);
        self::assertStringContainsString('資本金', $html);
        self::assertStringContainsString('利益剰余金', $html);
    }

    #[Test]
    public function renderContainsTotalChange(): void
    {
        $data = new CorporateSsData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            equity: $this->equity,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('7,210,000', $html);
    }

    #[Test]
    public function formatReturnsHtml(): void
    {
        self::assertSame(ReportFormat::Html, $this->renderer->format());
    }
}
