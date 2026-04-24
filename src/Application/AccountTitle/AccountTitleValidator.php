<?php

declare(strict_types=1);

namespace Rucaro\Application\AccountTitle;

use Rucaro\Domain\AccountTitle\AccountTitle;

/**
 * Shared validation helper for Create / Update account-title use cases.
 *
 * Pure static methods so the validator can be unit-tested without spinning
 * up a container or repository fake.
 */
final class AccountTitleValidator
{
    public const CODE_MAX_LENGTH = 16;
    public const NAME_MAX_LENGTH = 128;

    /**
     * @return array<string, list<string>>
     */
    public static function validate(string $code, string $name, string $category, string $normalSide): array
    {
        /** @var array<string, list<string>> $errors */
        $errors = [];
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

        if (!in_array($category, AccountTitle::CATEGORIES, true)) {
            $errors['category'][] = '分類が不正です。';
        }
        if (!in_array($normalSide, AccountTitle::NORMAL_SIDES, true)) {
            $errors['normal_side'][] = '貸借区分が不正です。';
        }

        return $errors;
    }
}
