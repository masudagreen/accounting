<?php

declare(strict_types=1);

namespace Rucaro\Application\SubAccountTitle;

final class SubAccountTitleValidator
{
    public const CODE_MAX_LENGTH = 16;
    public const NAME_MAX_LENGTH = 128;

    /**
     * @return array<string, list<string>>
     */
    public static function validate(string $accountTitleId, string $code, string $name): array
    {
        /** @var array<string, list<string>> $errors */
        $errors = [];
        if (trim($accountTitleId) === '') {
            $errors['account_title_id'][] = '親の勘定科目を選択してください。';
        }
        $codeTrim = trim($code);
        if ($codeTrim === '') {
            $errors['code'][] = 'コードを入力してください。';
        } elseif (strlen($codeTrim) > self::CODE_MAX_LENGTH) {
            $errors['code'][] = 'コードは ' . self::CODE_MAX_LENGTH . ' 文字以内で入力してください。';
        } elseif (!preg_match('/^[A-Za-z0-9_\-]+$/', $codeTrim)) {
            $errors['code'][] = 'コードは半角英数字・ハイフン・アンダースコアのみ使用できます。';
        }
        $nameTrim = trim($name);
        if ($nameTrim === '') {
            $errors['name'][] = '名称を入力してください。';
        } elseif (mb_strlen($nameTrim) > self::NAME_MAX_LENGTH) {
            $errors['name'][] = '名称は ' . self::NAME_MAX_LENGTH . ' 文字以内で入力してください。';
        }
        return $errors;
    }
}
