<?php

declare(strict_types=1);

namespace Rucaro\Application\FinancialStatement\Simplified;

use DateTimeZone;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCaseInput;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCaseInput;
use Rucaro\Domain\AccountTitle\AccountTitle;
use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;
use Rucaro\Domain\FinancialStatement\FinancialStatement;
use Rucaro\Domain\FinancialStatement\FinancialStatementLine;
use Rucaro\Domain\FinancialStatement\Section;
use Rucaro\Domain\TrialBalance\TrialBalanceRow;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Clock\SystemClock;
use Rucaro\Support\Decimal\Decimal;

/**
 * Transitional simplified builder that groups trial-balance rows by
 * {@see AccountTitle::category} (asset / liability / equity / revenue /
 * expense) and emits three (BS: assets/liabilities/equity) + two (PL:
 * revenue/expenses) sections. Kept around so entities without
 * `account_title_fs_mappings` rows still get a usable decimal-correct
 * statement.
 *
 * Ported (unchanged) from the pre-6-A `GenerateFinancialStatementUseCase`.
 * Once every seeded entity has FS mappings configured, this class and the
 * dispatching fallback can be retired.
 */
final readonly class SimplifiedGenerateFinancialStatementUseCase
{
    public function __construct(
        private QueryTrialBalanceUseCase $trialBalance,
        private AccountTitleRepositoryInterface $accounts,
        private ClockInterface $clock = new SystemClock(),
    ) {
    }

    public function execute(GenerateFinancialStatementUseCaseInput $input): FinancialStatement
    {
        $generatedAt = $this->clock->getCurrentTime()->setTimezone(new DateTimeZone('UTC'));
        $tb = $this->trialBalance->execute(new QueryTrialBalanceUseCaseInput(
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            fiscalTermStartDate: $input->fromDate,
            asOf: $input->asOf,
            currencyCode: $input->currencyCode,
        ));

        $accountIndex = $this->indexAccounts($input->entityId);
        $rows = $this->enrichRows($tb->rows, $accountIndex);

        $netIncome = $this->computeNetIncome($rows);

        $bs = $input->kind->includesBalanceSheet()
            ? $this->buildBalanceSheet($rows, $netIncome)
            : [];
        $pl = $input->kind->includesProfitAndLoss()
            ? $this->buildProfitAndLoss($rows)
            : [];
        $cs = $input->kind->includesCashFlow()
            ? $this->buildCashFlow($rows, $netIncome)
            : [];

        $totals = $this->buildTotals($bs, $pl, $netIncome);

        return new FinancialStatement(
            entityId: $input->entityId,
            fiscalTermId: $input->fiscalTermId,
            kind: $input->kind,
            fromDate: $input->fromDate,
            toDate: $input->asOf,
            currencyCode: $input->currencyCode,
            bs: $bs,
            pl: $pl,
            cs: $cs,
            totals: $totals,
            generatedAt: $generatedAt,
        );
    }

    /**
     * @return array<string, AccountTitle>
     */
    private function indexAccounts(string $entityId): array
    {
        $byId = [];
        foreach ($this->accounts->findAllByEntity($entityId) as $account) {
            $byId[$account->id] = $account;
        }
        return $byId;
    }

    /**
     * @param list<TrialBalanceRow>       $rows
     * @param array<string, AccountTitle> $accountIndex
     * @return list<TrialBalanceRow>
     */
    private function enrichRows(array $rows, array $accountIndex): array
    {
        $out = [];
        foreach ($rows as $row) {
            if ($row->accountCategory !== '') {
                $out[] = $row;
                continue;
            }
            $account = $accountIndex[$row->accountTitleId] ?? null;
            if ($account === null) {
                $out[] = $row;
                continue;
            }
            $out[] = TrialBalanceRow::compute(
                accountTitleId: $row->accountTitleId,
                accountTitleCode: $row->accountTitleCode !== '' ? $row->accountTitleCode : $account->code,
                accountTitleName: $row->accountTitleName !== '' ? $row->accountTitleName : $account->name,
                accountCategory: $account->category,
                normalSide: $account->normalSide,
                debitTotal: $row->debitTotal,
                creditTotal: $row->creditTotal,
                lineCount: $row->lineCount,
            );
        }
        return $out;
    }

    /**
     * @param list<TrialBalanceRow> $rows
     */
    private function computeNetIncome(array $rows): string
    {
        $revenue = '0.0000';
        $expense = '0.0000';
        foreach ($rows as $row) {
            if ($row->accountCategory === AccountTitle::CATEGORIES[3]) { // revenue
                $revenue = Decimal::add($revenue, $row->balance);
            } elseif ($row->accountCategory === AccountTitle::CATEGORIES[4]) { // expense
                $expense = Decimal::add($expense, $row->balance);
            }
        }
        return self::sub($revenue, $expense);
    }

    /**
     * @param list<TrialBalanceRow> $rows
     * @return array<string, Section>
     */
    private function buildBalanceSheet(array $rows, string $netIncome): array
    {
        $assetLines = [];
        $liabilityLines = [];
        $equityLines = [];
        foreach ($rows as $row) {
            $line = FinancialStatementLine::ofAccount(
                accountTitleId: $row->accountTitleId,
                accountTitleCode: $row->accountTitleCode,
                label: $row->accountTitleName !== '' ? $row->accountTitleName : $row->accountTitleCode,
                amount: $row->balance,
                depth: 1,
            );
            switch ($row->accountCategory) {
                case 'asset':
                    $assetLines[] = $line;
                    break;
                case 'liability':
                    $liabilityLines[] = $line;
                    break;
                case 'equity':
                    $equityLines[] = $line;
                    break;
            }
        }
        $equityLines[] = FinancialStatementLine::ofAccount(
            accountTitleId: 'net-income',
            accountTitleCode: '__ni',
            label: '利益剰余金（当期純利益）',
            amount: $netIncome,
            depth: 1,
        );

        return [
            Section::CODE_ASSETS      => Section::fromLines(Section::CODE_ASSETS, '資産の部', $assetLines),
            Section::CODE_LIABILITIES => Section::fromLines(Section::CODE_LIABILITIES, '負債の部', $liabilityLines),
            Section::CODE_EQUITY      => Section::fromLines(Section::CODE_EQUITY, '純資産の部', $equityLines),
        ];
    }

    /**
     * @param list<TrialBalanceRow> $rows
     * @return array<string, Section>
     */
    private function buildProfitAndLoss(array $rows): array
    {
        $revenueLines = [];
        $expenseLines = [];
        foreach ($rows as $row) {
            if ($row->accountCategory !== 'revenue' && $row->accountCategory !== 'expense') {
                continue;
            }
            $line = FinancialStatementLine::ofAccount(
                accountTitleId: $row->accountTitleId,
                accountTitleCode: $row->accountTitleCode,
                label: $row->accountTitleName !== '' ? $row->accountTitleName : $row->accountTitleCode,
                amount: $row->balance,
                depth: 1,
            );
            if ($row->accountCategory === 'revenue') {
                $revenueLines[] = $line;
            } else {
                $expenseLines[] = $line;
            }
        }

        return [
            Section::CODE_REVENUE  => Section::fromLines(Section::CODE_REVENUE, '収益', $revenueLines),
            Section::CODE_EXPENSES => Section::fromLines(Section::CODE_EXPENSES, '費用', $expenseLines),
        ];
    }

    /**
     * @param list<TrialBalanceRow> $rows
     * @return array<string, Section>
     */
    private function buildCashFlow(array $rows, string $netIncome): array
    {
        $cashDelta = '0.0000';
        foreach ($rows as $row) {
            if ($row->accountCategory !== 'asset') {
                continue;
            }
            if (!str_starts_with($row->accountTitleCode, '11')) {
                continue;
            }
            $cashDelta = Decimal::add($cashDelta, $row->balance);
        }

        $operatingLines = [
            FinancialStatementLine::ofAccount(
                accountTitleId: 'cf-ni',
                accountTitleCode: '__cf_ni',
                label: '当期純利益',
                amount: $netIncome,
                depth: 1,
            ),
            FinancialStatementLine::ofAccount(
                accountTitleId: 'cf-cash',
                accountTitleCode: '__cf_cash',
                label: '現預金の増減',
                amount: $cashDelta,
                depth: 1,
            ),
        ];

        return [
            Section::CODE_OPERATING_CF => Section::fromLines(Section::CODE_OPERATING_CF, '営業CF（簡易）', $operatingLines),
            Section::CODE_INVESTING_CF => Section::fromLines(Section::CODE_INVESTING_CF, '投資CF', []),
            Section::CODE_FINANCING_CF => Section::fromLines(Section::CODE_FINANCING_CF, '財務CF', []),
        ];
    }

    /**
     * @param array<string, Section> $bs
     * @param array<string, Section> $pl
     * @return array<string, string>
     */
    private function buildTotals(array $bs, array $pl, string $netIncome): array
    {
        $totals = [
            'net_income' => Decimal::normalize($netIncome),
        ];
        if (isset($bs[Section::CODE_ASSETS])) {
            $totals['total_assets'] = $bs[Section::CODE_ASSETS]->subtotal;
        }
        if (isset($bs[Section::CODE_LIABILITIES])) {
            $totals['total_liabilities'] = $bs[Section::CODE_LIABILITIES]->subtotal;
        }
        if (isset($bs[Section::CODE_EQUITY])) {
            $totals['total_equity'] = $bs[Section::CODE_EQUITY]->subtotal;
        }
        if (isset($pl[Section::CODE_REVENUE])) {
            $totals['total_revenue'] = $pl[Section::CODE_REVENUE]->subtotal;
        }
        if (isset($pl[Section::CODE_EXPENSES])) {
            $totals['total_expenses'] = $pl[Section::CODE_EXPENSES]->subtotal;
        }
        return $totals;
    }

    private static function sub(string $a, string $b): string
    {
        if (function_exists('bcsub')) {
            /** @var string */
            return bcsub($a, $b, Decimal::SCALE);
        }
        $negated = str_starts_with($b, '-') ? substr($b, 1) : ('-' . $b);
        return Decimal::add($a, $negated);
    }
}
