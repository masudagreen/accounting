<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Infrastructure\FinancialStatement;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\FinancialStatement\FinancialStatement;
use Rucaro\Domain\FinancialStatement\FinancialStatementKind;
use Rucaro\Domain\FinancialStatement\FinancialStatementLine;
use Rucaro\Domain\FinancialStatement\Section;
use Rucaro\Infrastructure\FinancialStatement\DompdfFinancialStatementGenerator;

#[CoversClass(DompdfFinancialStatementGenerator::class)]
final class DompdfFinancialStatementGeneratorTest extends TestCase
{
    private string $compileDir;

    protected function setUp(): void
    {
        $this->compileDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR
            . 'rucaro-fs-smarty-' . bin2hex(random_bytes(4));
        if (!is_dir($this->compileDir)) {
            mkdir($this->compileDir, 0775, true);
        }
    }

    protected function tearDown(): void
    {
        if (is_dir($this->compileDir)) {
            $this->rmrf($this->compileDir);
        }
    }

    public function testRenderHtmlProducesJapaneseLabels(): void
    {
        $generator = $this->makeGenerator();
        $html = $generator->renderHtml($this->sampleBs());

        self::assertStringContainsString('貸借対照表', $html);
        self::assertStringContainsString('資産の部', $html);
        self::assertStringContainsString('現金', $html);
    }

    public function testRenderProducesPdfWithPdfHeader(): void
    {
        $generator = $this->makeGenerator();
        $pdf = $generator->render($this->sampleBs());

        self::assertNotSame('', $pdf);
        // dompdf output always starts with the 4-byte PDF magic.
        self::assertSame('%PDF', substr($pdf, 0, 4));
    }

    public function testRenderAllKindUsesCombinedTemplate(): void
    {
        $generator = $this->makeGenerator();
        $fs = $this->sampleAll();
        $html = $generator->renderHtml($fs);

        self::assertStringContainsString('貸借対照表', $html);
        self::assertStringContainsString('損益計算書', $html);
        self::assertStringContainsString('キャッシュフロー計算書', $html);

        $pdf = $generator->render($fs);
        self::assertSame('%PDF', substr($pdf, 0, 4));
    }

    private function makeGenerator(): DompdfFinancialStatementGenerator
    {
        $repoRoot = dirname(__DIR__, 4);
        $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage'
            . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'fs';
        $fontDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
        return new DompdfFinancialStatementGenerator(
            templateDir: $templateDir,
            compileDir: $this->compileDir,
            fontDir: $fontDir,
        );
    }

    private function sampleBs(): FinancialStatement
    {
        $assets = Section::fromLines(Section::CODE_ASSETS, '資産の部', [
            FinancialStatementLine::ofAccount('acc-cash', '101', '現金', '1000'),
            FinancialStatementLine::ofAccount('acc-ar', '110', '売掛金', '500'),
        ]);
        $liabilities = Section::fromLines(Section::CODE_LIABILITIES, '負債の部', [
            FinancialStatementLine::ofAccount('acc-ap', '201', '買掛金', '400'),
        ]);
        $equity = Section::fromLines(Section::CODE_EQUITY, '純資産の部', [
            FinancialStatementLine::ofAccount('acc-eq', '301', '資本金', '1000'),
            FinancialStatementLine::ofAccount('net-income', '__ni', '利益剰余金（当期純利益）', '100'),
        ]);

        return new FinancialStatement(
            entityId: 'ENT',
            fiscalTermId: 'TRM',
            kind: FinancialStatementKind::BalanceSheet,
            fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2026-04-30', new DateTimeZone('UTC')),
            currencyCode: 'JPY',
            bs: [
                Section::CODE_ASSETS      => $assets,
                Section::CODE_LIABILITIES => $liabilities,
                Section::CODE_EQUITY      => $equity,
            ],
            pl: [],
            cs: [],
            totals: [
                'net_income'        => '100.0000',
                'total_assets'      => '1500.0000',
                'total_liabilities' => '400.0000',
                'total_equity'      => '1100.0000',
            ],
            generatedAt: new DateTimeImmutable('2026-04-30T00:00:00.000000Z', new DateTimeZone('UTC')),
        );
    }

    private function sampleAll(): FinancialStatement
    {
        $bs = $this->sampleBs();

        $revenue = Section::fromLines(Section::CODE_REVENUE, '収益', [
            FinancialStatementLine::ofAccount('acc-sales', '401', '売上', '2000'),
        ]);
        $expenses = Section::fromLines(Section::CODE_EXPENSES, '費用', [
            FinancialStatementLine::ofAccount('acc-cost', '501', '仕入', '1900'),
        ]);
        $operating = Section::fromLines(Section::CODE_OPERATING_CF, '営業CF（簡易）', [
            FinancialStatementLine::ofAccount('cf-ni', '__cf_ni', '当期純利益', '100'),
        ]);

        return new FinancialStatement(
            entityId: $bs->entityId,
            fiscalTermId: $bs->fiscalTermId,
            kind: FinancialStatementKind::All,
            fromDate: $bs->fromDate,
            toDate: $bs->toDate,
            currencyCode: $bs->currencyCode,
            bs: $bs->bs,
            pl: [
                Section::CODE_REVENUE  => $revenue,
                Section::CODE_EXPENSES => $expenses,
            ],
            cs: [
                Section::CODE_OPERATING_CF => $operating,
                Section::CODE_INVESTING_CF => Section::fromLines(Section::CODE_INVESTING_CF, '投資CF', []),
                Section::CODE_FINANCING_CF => Section::fromLines(Section::CODE_FINANCING_CF, '財務CF', []),
            ],
            totals: $bs->totals + [
                'total_revenue'  => '2000.0000',
                'total_expenses' => '1900.0000',
            ],
            generatedAt: $bs->generatedAt,
        );
    }

    private function rmrf(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }
        $entries = scandir($path);
        if ($entries === false) {
            return;
        }
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $full = $path . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($full)) {
                $this->rmrf($full);
            } else {
                @unlink($full);
            }
        }
        @rmdir($path);
    }
}
