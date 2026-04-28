<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Report\CorporateFinancialStatements;

use App\Application\Dto\ProfitAndLossDto;
use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Report\CorporateFinancialStatements\CorporatePlData;
use App\Domain\Report\CorporateFinancialStatements\CorporatePlRenderer;
use App\Domain\Report\ReportFormat;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 法人 損益計算書 Renderer のユニットテスト.
 */
final class CorporatePlRendererTest extends TestCase
{
    private CorporatePlRenderer $renderer;
    private FiscalPeriod $period;
    private ProfitAndLossDto $pl;

    protected function setUp(): void
    {
        $this->renderer = new CorporatePlRenderer();
        $this->period   = FiscalPeriod::of(2024, 4, 12, 1);
        $this->pl       = new ProfitAndLossDto(
            sales: 50_000_000,
            costOfSales: 30_000_000,
            grossProfit: 20_000_000,
            sellingAndAdmin: 10_000_000,
            operatingIncome: 10_000_000,
            nonOperatingIncome: 500_000,
            nonOperatingExpenses: 200_000,
            ordinaryIncome: 10_300_000,
            extraordinaryIncome: 0,
            extraordinaryLosses: 0,
            incomeBeforeTax: 10_300_000,
            tax: 3_090_000,
            netIncome: 7_210_000,
        );
    }

    #[Test]
    public function renderReturnsNonEmptyHtml(): void
    {
        $data = new CorporatePlData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            pl: $this->pl,
        );

        $html = $this->renderer->render($data);

        self::assertNotEmpty($html);
        self::assertGreaterThan(100, strlen($html));
    }

    #[Test]
    public function renderContainsCompanyName(): void
    {
        $data = new CorporatePlData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            pl: $this->pl,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('株式会社テスト', $html);
    }

    #[Test]
    public function renderContainsSalesAmount(): void
    {
        $data = new CorporatePlData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            pl: $this->pl,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('50,000,000', $html);
    }

    #[Test]
    public function renderContainsGrossProfit(): void
    {
        $data = new CorporatePlData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            pl: $this->pl,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('20,000,000', $html);
    }

    #[Test]
    public function renderContainsOperatingIncome(): void
    {
        $data = new CorporatePlData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            pl: $this->pl,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('10,000,000', $html);
    }

    #[Test]
    public function renderContainsOrdinaryIncome(): void
    {
        $data = new CorporatePlData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            pl: $this->pl,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('10,300,000', $html);
    }

    #[Test]
    public function renderContainsNetIncome(): void
    {
        $data = new CorporatePlData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            pl: $this->pl,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('7,210,000', $html);
    }

    #[Test]
    public function renderContainsRequiredSectionLabels(): void
    {
        $data = new CorporatePlData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            pl: $this->pl,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('売上高', $html);
        self::assertStringContainsString('売上総利益', $html);
        self::assertStringContainsString('営業利益', $html);
        self::assertStringContainsString('経常利益', $html);
        self::assertStringContainsString('当期純利益', $html);
    }

    #[Test]
    public function renderContainsTaxLine(): void
    {
        $data = new CorporatePlData(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            pl: $this->pl,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('法人税', $html);
        self::assertStringContainsString('3,090,000', $html);
    }

    #[Test]
    public function renderEscapesSpecialChars(): void
    {
        $data = new CorporatePlData(
            companyName: '<b>危険</b>株式会社',
            fiscalPeriod: $this->period,
            pl: $this->pl,
        );

        $html = $this->renderer->render($data);

        self::assertStringNotContainsString('<b>危険</b>', $html);
        self::assertStringContainsString('&lt;b&gt;', $html);
    }

    #[Test]
    public function formatReturnsHtml(): void
    {
        self::assertSame(ReportFormat::Html, $this->renderer->format());
    }
}
