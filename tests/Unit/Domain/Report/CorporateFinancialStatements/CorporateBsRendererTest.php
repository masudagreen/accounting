<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Report\CorporateFinancialStatements;

use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Report\CorporateFinancialStatements\CorporateBsData;
use App\Domain\Report\CorporateFinancialStatements\CorporateBsRenderer;
use App\Domain\Report\ReportFormat;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 法人 貸借対照表 Renderer のユニットテスト.
 */
final class CorporateBsRendererTest extends TestCase
{
    private CorporateBsRenderer $renderer;
    private FiscalPeriod $period;

    protected function setUp(): void
    {
        $this->renderer = new CorporateBsRenderer();
        $this->period   = FiscalPeriod::of(2024, 4, 12, 1);
    }

    private function makeData(string $companyName = '株式会社テスト'): CorporateBsData
    {
        return new CorporateBsData(
            companyName: $companyName,
            fiscalPeriod: $this->period,
            currentAssets: 20_000_000,
            fixedAssets: 30_000_000,
            totalAssets: 50_000_000,
            currentLiabilities: 10_000_000,
            fixedLiabilities: 15_000_000,
            totalLiabilities: 25_000_000,
            capitalStock: 10_000_000,
            retainedEarnings: 15_000_000,
            totalEquity: 25_000_000,
        );
    }

    #[Test]
    public function renderReturnsNonEmptyHtml(): void
    {
        $html = $this->renderer->render($this->makeData());

        self::assertNotEmpty($html);
        self::assertGreaterThan(100, strlen($html));
    }

    #[Test]
    public function renderContainsCompanyName(): void
    {
        $html = $this->renderer->render($this->makeData('テスト株式会社'));

        self::assertStringContainsString('テスト株式会社', $html);
    }

    #[Test]
    public function renderContainsTotalAssets(): void
    {
        $html = $this->renderer->render($this->makeData());

        self::assertStringContainsString('50,000,000', $html);
    }

    #[Test]
    public function renderContainsTotalLiabilities(): void
    {
        $html = $this->renderer->render($this->makeData());

        self::assertStringContainsString('25,000,000', $html);
    }

    #[Test]
    public function renderContainsTotalEquity(): void
    {
        $html = $this->renderer->render($this->makeData());

        // 純資産合計 = 25,000,000
        self::assertStringContainsString('25,000,000', $html);
    }

    #[Test]
    public function renderContainsCapitalStock(): void
    {
        $html = $this->renderer->render($this->makeData());

        self::assertStringContainsString('10,000,000', $html);
    }

    #[Test]
    public function renderContainsRequiredSectionLabels(): void
    {
        $html = $this->renderer->render($this->makeData());

        self::assertStringContainsString('資産の部', $html);
        self::assertStringContainsString('負債の部', $html);
        self::assertStringContainsString('純資産の部', $html);
        self::assertStringContainsString('流動資産', $html);
        self::assertStringContainsString('固定資産', $html);
        self::assertStringContainsString('流動負債', $html);
        self::assertStringContainsString('固定負債', $html);
        self::assertStringContainsString('資本金', $html);
    }

    #[Test]
    public function renderContainsBalanceSheetTitle(): void
    {
        $html = $this->renderer->render($this->makeData());

        self::assertStringContainsString('貸借対照表', $html);
    }

    #[Test]
    public function renderEscapesHtmlInCompanyName(): void
    {
        $html = $this->renderer->render($this->makeData('<script>xss</script>'));

        self::assertStringNotContainsString('<script>', $html);
        self::assertStringContainsString('&lt;script&gt;', $html);
    }

    #[Test]
    public function formatReturnsHtml(): void
    {
        self::assertSame(ReportFormat::Html, $this->renderer->format());
    }
}
