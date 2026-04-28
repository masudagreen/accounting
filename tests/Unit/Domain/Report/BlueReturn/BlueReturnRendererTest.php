<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Report\BlueReturn;

use App\Application\Dto\BalanceSheetDto;
use App\Application\Dto\ProfitAndLossDto;
use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Report\BlueReturn\BlueReturnData;
use App\Domain\Report\BlueReturn\BlueReturnRenderer;
use App\Domain\Report\ReportFormat;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 青色申告決算書 Renderer のユニットテスト.
 */
final class BlueReturnRendererTest extends TestCase
{
    private BlueReturnRenderer $renderer;
    private FiscalPeriod $period;
    private ProfitAndLossDto $pl;
    private BalanceSheetDto $bs;

    protected function setUp(): void
    {
        $this->renderer = new BlueReturnRenderer();
        $this->period   = FiscalPeriod::of(2024, 1, 12, 1);
        $this->pl       = new ProfitAndLossDto(
            sales: 10_000_000,
            costOfSales: 4_000_000,
            grossProfit: 6_000_000,
            sellingAndAdmin: 2_000_000,
            operatingIncome: 4_000_000,
            nonOperatingIncome: 100_000,
            nonOperatingExpenses: 50_000,
            ordinaryIncome: 4_050_000,
            extraordinaryIncome: 0,
            extraordinaryLosses: 0,
            incomeBeforeTax: 4_050_000,
            tax: 0,
            netIncome: 4_050_000,
        );
        $this->bs = new BalanceSheetDto(
            totalAssets: 15_000_000,
            totalLiabilities: 5_000_000,
            totalEquity: 10_000_000,
        );
    }

    #[Test]
    public function renderReturnsNonEmptyHtml(): void
    {
        $data = new BlueReturnData(
            businessName: 'テスト商店',
            fiscalPeriod: $this->period,
            pl: $this->pl,
            bs: $this->bs,
        );

        $html = $this->renderer->render($data);

        self::assertNotEmpty($html);
        self::assertGreaterThan(100, strlen($html));
    }

    #[Test]
    public function renderContainsDocTypeOrHtmlTag(): void
    {
        $data = new BlueReturnData(
            businessName: 'テスト商店',
            fiscalPeriod: $this->period,
            pl: $this->pl,
            bs: $this->bs,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('<html', $html);
    }

    #[Test]
    public function renderContainsBusinessName(): void
    {
        $data = new BlueReturnData(
            businessName: 'テスト商店',
            fiscalPeriod: $this->period,
            pl: $this->pl,
            bs: $this->bs,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('テスト商店', $html);
    }

    #[Test]
    public function renderContainsFiscalPeriodDates(): void
    {
        $data = new BlueReturnData(
            businessName: 'テスト商店',
            fiscalPeriod: $this->period,
            pl: $this->pl,
            bs: $this->bs,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('2024', $html);
    }

    #[Test]
    public function renderContainsSalesAmount(): void
    {
        $data = new BlueReturnData(
            businessName: 'テスト商店',
            fiscalPeriod: $this->period,
            pl: $this->pl,
            bs: $this->bs,
        );

        $html = $this->renderer->render($data);

        // 10,000,000 円 が含まれること
        self::assertStringContainsString('10,000,000', $html);
    }

    #[Test]
    public function renderContainsCostOfSales(): void
    {
        $data = new BlueReturnData(
            businessName: 'テスト商店',
            fiscalPeriod: $this->period,
            pl: $this->pl,
            bs: $this->bs,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('4,000,000', $html);
    }

    #[Test]
    public function renderContainsGrossProfit(): void
    {
        $data = new BlueReturnData(
            businessName: 'テスト商店',
            fiscalPeriod: $this->period,
            pl: $this->pl,
            bs: $this->bs,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('6,000,000', $html);
    }

    #[Test]
    public function renderContainsTotalAssets(): void
    {
        $data = new BlueReturnData(
            businessName: 'テスト商店',
            fiscalPeriod: $this->period,
            pl: $this->pl,
            bs: $this->bs,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('15,000,000', $html);
    }

    #[Test]
    public function renderContainsTotalLiabilities(): void
    {
        $data = new BlueReturnData(
            businessName: 'テスト商店',
            fiscalPeriod: $this->period,
            pl: $this->pl,
            bs: $this->bs,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('5,000,000', $html);
    }

    #[Test]
    public function renderContainsRequiredProfitAndLossLabels(): void
    {
        $data = new BlueReturnData(
            businessName: 'テスト商店',
            fiscalPeriod: $this->period,
            pl: $this->pl,
            bs: $this->bs,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('売上', $html);
        self::assertStringContainsString('売上原価', $html);
        self::assertStringContainsString('経費', $html);
        self::assertStringContainsString('所得金額', $html);
    }

    #[Test]
    public function renderContainsBalanceSheetSection(): void
    {
        $data = new BlueReturnData(
            businessName: 'テスト商店',
            fiscalPeriod: $this->period,
            pl: $this->pl,
            bs: $this->bs,
        );

        $html = $this->renderer->render($data);

        self::assertStringContainsString('貸借対照表', $html);
        self::assertStringContainsString('資産', $html);
        self::assertStringContainsString('負債', $html);
    }

    #[Test]
    public function renderEscapesSpecialCharsInBusinessName(): void
    {
        $data = new BlueReturnData(
            businessName: '<script>alert("xss")</script>',
            fiscalPeriod: $this->period,
            pl: $this->pl,
            bs: $this->bs,
        );

        $html = $this->renderer->render($data);

        self::assertStringNotContainsString('<script>', $html);
        self::assertStringContainsString('&lt;script&gt;', $html);
    }

    #[Test]
    public function renderHandlesNegativeNetIncome(): void
    {
        $pl = new ProfitAndLossDto(
            sales: 1_000_000,
            costOfSales: 3_000_000,
            grossProfit: -2_000_000,
            sellingAndAdmin: 500_000,
            operatingIncome: -2_500_000,
            nonOperatingIncome: 0,
            nonOperatingExpenses: 0,
            ordinaryIncome: -2_500_000,
            extraordinaryIncome: 0,
            extraordinaryLosses: 0,
            incomeBeforeTax: -2_500_000,
            tax: 0,
            netIncome: -2_500_000,
        );
        $data = new BlueReturnData(
            businessName: '赤字商店',
            fiscalPeriod: $this->period,
            pl: $pl,
            bs: $this->bs,
        );

        $html = $this->renderer->render($data);

        self::assertNotEmpty($html);
        self::assertStringContainsString('赤字商店', $html);
    }

    #[Test]
    public function formatReturnsHtml(): void
    {
        self::assertSame(ReportFormat::Html, $this->renderer->format());
    }
}
