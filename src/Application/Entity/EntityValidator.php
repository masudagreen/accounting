<?php

declare(strict_types=1);

namespace Rucaro\Application\Entity;

final class EntityValidator
{
    public const NAME_MAX_LENGTH = 128;

    /**
     * @return array<string, list<string>>
     */
    public static function validate(string $name, string $nationCode, string $currencyCode, string $fiscalStartMmDd): array
    {
        /** @var array<string, list<string>> $errors */
        $errors = [];
        $nameTrim = trim($name);
        if ($nameTrim === '') {
            $errors['name'][] = '屋号 / 会社名を入力してください。';
        } elseif (mb_strlen($nameTrim) > self::NAME_MAX_LENGTH) {
            $errors['name'][] = '名称は ' . self::NAME_MAX_LENGTH . ' 文字以内で入力してください。';
        }
        if (!preg_match('/^[A-Z]{3}$/', $nationCode)) {
            $errors['nation_code'][] = '国コードは ISO 3166-1 alpha-3 形式 (例: JPN) で入力してください。';
        }
        if (!preg_match('/^[A-Z]{3}$/', $currencyCode)) {
            $errors['currency_code'][] = '通貨コードは ISO 4217 形式 (例: JPY) で入力してください。';
        }
        if (!preg_match('/^\d{4}$/', $fiscalStartMmDd)) {
            $errors['fiscal_start_mmdd'][] = '会計年度開始は MMDD (4桁) で入力してください。';
        } else {
            $mm = (int) substr($fiscalStartMmDd, 0, 2);
            $dd = (int) substr($fiscalStartMmDd, 2, 2);
            if ($mm < 1 || $mm > 12 || $dd < 1 || $dd > 31) {
                $errors['fiscal_start_mmdd'][] = '会計年度開始の月日が不正です。';
            }
        }
        return $errors;
    }
}
