<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Import\LegacyImport;

/**
 * Classify legacy `idAccountTitle` strings into
 * `(category, normal_side)` for the new `account_titles` schema.
 *
 * The source of truth is the JSON trees stored in
 * `accountingFSJpn.jsonJgaapAccountTitleBS / jsonJgaapAccountTitlePL`.
 * Rather than parse that at runtime (hundreds of nodes, recursive shape,
 * duplicated per-term) we bake in a static mapping derived from a single
 * inspection pass of those trees — this covers every code present in the
 * 1613 legacy journals we are importing and gives us a stable, testable
 * classifier.
 *
 * Unknown codes default to ('expense', 'debit'), which matches the most
 * common legacy usage and keeps the journal balance check working.
 */
final class AccountTitleClassifier
{
    /**
     * camelCase legacy code => [category, normal_side, japanese_label]
     *
     * @var array<string, array{0:string, 1:string, 2:string}>
     */
    private const MAP = [
        // --- Assets (normal: debit) ---
        'cash'                        => ['asset', 'debit', '現金'],
        'prettyCash'                  => ['asset', 'debit', '小口現金'],
        'ordinaryDeposit'             => ['asset', 'debit', '普通預金'],
        'checkingAccounts'            => ['asset', 'debit', '当座預金'],
        'fixedDeposit'                => ['asset', 'debit', '定期預金'],
        'depositAtNotice'             => ['asset', 'debit', '通知預金'],
        'accountsReceivable'          => ['asset', 'debit', '売掛金'],
        'notesReceivable'             => ['asset', 'debit', '受取手形'],
        'securities'                  => ['asset', 'debit', '有価証券'],
        'merchandise'                 => ['asset', 'debit', '商品'],
        'prepaidExpenses'             => ['asset', 'debit', '前払費用'],
        'accruedIncome'               => ['asset', 'debit', '未収収益'],
        'accruedRevenue'              => ['asset', 'debit', '未収入金'],
        'advancesAccount'             => ['asset', 'debit', '前渡金'],
        'advances'                    => ['asset', 'debit', '仮払金'],
        'shortTermLoansReceivable'    => ['asset', 'debit', '短期貸付金'],

        // --- Liabilities (normal: credit) ---
        'accruedExpenses'             => ['liability', 'credit', '未払費用'],
        'shortTermLoansPayable'       => ['liability', 'credit', '短期借入金'],
        'depositePayable'             => ['liability', 'credit', '預り金'],
        'corporationTaxesPayable'     => ['liability', 'credit', '未払法人税'],
        'consumptionTaxesRepayable'   => ['liability', 'credit', '未払消費税'],

        // --- Equity (normal: credit) ---
        'contribution'                => ['equity', 'credit', '元入金'],

        // --- Revenue (normal: credit) ---
        'netSales'                    => ['revenue', 'credit', '売上高'],
        'miscellaneousIncome'         => ['revenue', 'credit', '雑収入'],
        'interestAndDiscountReceived' => ['revenue', 'credit', '受取利息'],

        // --- Expenses (normal: debit) ---
        'conferenceExpense'           => ['expense', 'debit', '会議費'],
        'suppliesExpenses'            => ['expense', 'debit', '消耗品費'],
        'correspondenceExpenses'      => ['expense', 'debit', '通信費'],
        'transportationExpenses'      => ['expense', 'debit', '旅費交通費'],
        'entertainmentExpenses'       => ['expense', 'debit', '接待交際費'],
        'directorsCompensations'      => ['expense', 'debit', '役員報酬'],
        'legalWelfareExpenses'        => ['expense', 'debit', '法定福利費'],
        'welfareExpenses'             => ['expense', 'debit', '福利厚生費'],
        'insuranceExpenses'           => ['expense', 'debit', '保険料'],
        'miscellaneousExpenses'       => ['expense', 'debit', '雑費'],
        'badMiscellaneousExpenses'    => ['expense', 'debit', '雑損失'],
        'taxesAndDues'                => ['expense', 'debit', '租税公課'],
        'rents'                       => ['expense', 'debit', '地代家賃'],
        'booksExpense'                => ['expense', 'debit', '新聞図書費'],
        'commissionPaid'              => ['expense', 'debit', '支払手数料'],
        'repair'                      => ['expense', 'debit', '修繕費'],
        'waterPowerExpenses'          => ['expense', 'debit', '水道光熱費'],
        'corporateInhabitantAndEnterpriseTax'
                                      => ['expense', 'debit', '法人税等'],

        // --- Special marker (legacy uses 'else' in idAccountTitleContra) ---
        // Not classified as a real account title here; callers skip it.
    ];

    /**
     * Classify a legacy code. Returns
     * [category, normal_side, japanese_label_or_original].
     *
     * @return array{0:string, 1:string, 2:string}
     */
    public static function classify(string $legacyCode): array
    {
        if (isset(self::MAP[$legacyCode])) {
            return self::MAP[$legacyCode];
        }
        // Unknown — safest default for journal balance math.
        return ['expense', 'debit', $legacyCode];
    }

    public static function isKnown(string $legacyCode): bool
    {
        return isset(self::MAP[$legacyCode]);
    }

    /**
     * @return list<string>
     */
    public static function knownCodes(): array
    {
        return array_keys(self::MAP);
    }
}
