<?php

declare(strict_types=1);

namespace App\Domain\Banks;

/**
 * 銀行コード → BankStatementImporter のマッピングレジストリ.
 *
 * 依存性注入により、上位レイヤーから各銀行アダプタを登録する.
 * 銀行コードは任意の文字列 (例: 'japannet', 'japanpost' 等) とする.
 */
final class BankAdapterRegistry
{
    /** @var array<string, BankStatementImporter> */
    private array $adapters = [];

    /**
     * 銀行アダプタを登録する. 同じコードを上書き登録できる.
     */
    public function register(string $bankCode, BankStatementImporter $adapter): void
    {
        $this->adapters[$bankCode] = $adapter;
    }

    /**
     * 銀行コードに対応するアダプタを返す. 未登録の場合は null.
     */
    public function find(string $bankCode): ?BankStatementImporter
    {
        return $this->adapters[$bankCode] ?? null;
    }
}
