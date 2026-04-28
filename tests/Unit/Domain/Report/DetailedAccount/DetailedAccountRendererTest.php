<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Report\DetailedAccount;

use App\Domain\FiscalPeriod\FiscalPeriod;
use App\Domain\Report\DetailedAccount\AccountBreakdownRow;
use App\Domain\Report\DetailedAccount\AccountsPayableBreakdown;
use App\Domain\Report\DetailedAccount\AccountsReceivableBreakdown;
use App\Domain\Report\DetailedAccount\DepositsBreakdown;
use App\Domain\Report\DetailedAccount\DetailedAccountRenderer;
use App\Domain\Report\DetailedAccount\LoanRow;
use App\Domain\Report\DetailedAccount\LoansPayableBreakdown;
use App\Domain\Report\ReportFormat;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 勘定科目内訳明細書 Renderer のユニットテスト.
 */
final class DetailedAccountRendererTest extends TestCase
{
    private DetailedAccountRenderer $renderer;
    private FiscalPeriod $period;

    protected function setUp(): void
    {
        $this->renderer = new DetailedAccountRenderer();
        $this->period   = FiscalPeriod::of(2024, 4, 12, 1);
    }

    // ----------------------------------------------------------------
    // DepositsBreakdown (預貯金等の内訳書)
    // ----------------------------------------------------------------

    #[Test]
    public function renderDepositsReturnsHtmlWithBankName(): void
    {
        $data = new DepositsBreakdown(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            rows: [
                new AccountBreakdownRow(
                    counterpartyName: '東京銀行 渋谷支店',
                    location: '東京都渋谷区',
                    closingBalance: 3_000_000,
                    note: '普通預金',
                ),
                new AccountBreakdownRow(
                    counterpartyName: '大阪銀行 梅田支店',
                    location: '大阪府大阪市',
                    closingBalance: 2_000_000,
                    note: '定期預金',
                ),
            ],
        );

        $html = $this->renderer->renderDeposits($data);

        self::assertNotEmpty($html);
        self::assertStringContainsString('東京銀行 渋谷支店', $html);
        self::assertStringContainsString('大阪銀行 梅田支店', $html);
        self::assertStringContainsString('3,000,000', $html);
        self::assertStringContainsString('2,000,000', $html);
        self::assertStringContainsString('預貯金', $html);
    }

    #[Test]
    public function renderDepositsContainsTotalAmount(): void
    {
        $data = new DepositsBreakdown(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            rows: [
                new AccountBreakdownRow(
                    counterpartyName: '東京銀行',
                    location: '東京都',
                    closingBalance: 3_000_000,
                    note: '',
                ),
                new AccountBreakdownRow(
                    counterpartyName: '大阪銀行',
                    location: '大阪府',
                    closingBalance: 2_000_000,
                    note: '',
                ),
            ],
        );

        $html = $this->renderer->renderDeposits($data);

        self::assertStringContainsString('5,000,000', $html);
    }

    // ----------------------------------------------------------------
    // AccountsReceivableBreakdown (売掛金の内訳書)
    // ----------------------------------------------------------------

    #[Test]
    public function renderReceivablesContainsCounterpartyName(): void
    {
        $data = new AccountsReceivableBreakdown(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            rows: [
                new AccountBreakdownRow(
                    counterpartyName: '得意先A株式会社',
                    location: '東京都千代田区',
                    closingBalance: 1_500_000,
                    note: '',
                ),
            ],
        );

        $html = $this->renderer->renderReceivables($data);

        self::assertNotEmpty($html);
        self::assertStringContainsString('得意先A株式会社', $html);
        self::assertStringContainsString('1,500,000', $html);
        self::assertStringContainsString('売掛金', $html);
    }

    // ----------------------------------------------------------------
    // AccountsPayableBreakdown (買掛金の内訳書)
    // ----------------------------------------------------------------

    #[Test]
    public function renderPayablesContainsSupplierName(): void
    {
        $data = new AccountsPayableBreakdown(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            rows: [
                new AccountBreakdownRow(
                    counterpartyName: '仕入先B株式会社',
                    location: '大阪府大阪市',
                    closingBalance: 800_000,
                    note: '',
                ),
            ],
        );

        $html = $this->renderer->renderPayables($data);

        self::assertNotEmpty($html);
        self::assertStringContainsString('仕入先B株式会社', $html);
        self::assertStringContainsString('800,000', $html);
        self::assertStringContainsString('買掛金', $html);
    }

    // ----------------------------------------------------------------
    // LoansPayableBreakdown (借入金の内訳書)
    // ----------------------------------------------------------------

    #[Test]
    public function renderLoansContainsLenderName(): void
    {
        $data = new LoansPayableBreakdown(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            rows: [
                new LoanRow(
                    lenderName: '日本政策金融公庫',
                    location: '東京都千代田区',
                    closingBalance: 10_000_000,
                    interestPaid: 120_000,
                    interestRate: '1.2',
                ),
            ],
        );

        $html = $this->renderer->renderLoans($data);

        self::assertNotEmpty($html);
        self::assertStringContainsString('日本政策金融公庫', $html);
        self::assertStringContainsString('10,000,000', $html);
        self::assertStringContainsString('120,000', $html);
        self::assertStringContainsString('1.2', $html);
        self::assertStringContainsString('借入金', $html);
    }

    #[Test]
    public function renderDepositsEscapesHtml(): void
    {
        $data = new DepositsBreakdown(
            companyName: '<script>xss</script>',
            fiscalPeriod: $this->period,
            rows: [],
        );

        $html = $this->renderer->renderDeposits($data);

        self::assertStringNotContainsString('<script>', $html);
        self::assertStringContainsString('&lt;script&gt;', $html);
    }

    #[Test]
    public function renderDepositsHandlesEmptyRows(): void
    {
        $data = new DepositsBreakdown(
            companyName: '株式会社テスト',
            fiscalPeriod: $this->period,
            rows: [],
        );

        $html = $this->renderer->renderDeposits($data);

        self::assertNotEmpty($html);
        self::assertStringContainsString('預貯金', $html);
    }

    #[Test]
    public function formatReturnsHtml(): void
    {
        self::assertSame(ReportFormat::Html, $this->renderer->format());
    }
}
