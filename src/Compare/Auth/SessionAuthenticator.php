<?php

declare(strict_types=1);

namespace App\Compare\Auth;

use PDO;

/**
 * legacy の baseSession テーブルを参照してセッションを検証する認証クラス.
 *
 * Cookie キー 'id' に格納された idCookie 値を baseSession に照合し、
 * セッション有効期限内かつ API セッションでないことを確認する.
 *
 * このクラスは DB への書き込みを一切行わない (read-only).
 */
final class SessionAuthenticator
{
    private const string COOKIE_KEY = 'id';
    private const int DEFAULT_MAX_AGE_SECONDS = 90000; // legacy の NUM_SESSION 相当

    /** 認証成功後の idAccount. */
    private ?int $accountId = null;

    public function __construct(
        private readonly PDO $pdo,
        private readonly int $maxAgeSeconds = self::DEFAULT_MAX_AGE_SECONDS,
    ) {
    }

    /**
     * Cookie 配列を受け取り、セッションを検証する.
     *
     * @param array<string, string> $cookies $_COOKIE の内容
     */
    public function authenticate(array $cookies): bool
    {
        $this->accountId = null;

        $idCookie = $cookies[self::COOKIE_KEY] ?? '';
        if ($idCookie === '') {
            return false;
        }

        $row = $this->findSession($idCookie);
        if ($row === null) {
            return false;
        }

        // API セッションは UI 認証に使わない
        if ((int) ($row['flagAPI'] ?? 0) !== 0) {
            return false;
        }

        // 有効期限チェック
        $stampRegister = (int) ($row['stampRegister'] ?? 0);
        $age           = time() - $stampRegister;
        if ($age > $this->maxAgeSeconds) {
            return false;
        }

        $idAccount = (int) ($row['idAccount'] ?? 0);
        if ($idAccount <= 0) {
            return false;
        }

        $this->accountId = $idAccount;
        return true;
    }

    /**
     * 認証成功後に idAccount を返す. 認証前または失敗後は null.
     */
    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    /**
     * baseSession から idCookie に一致する行を取得する.
     *
     * @return array<string, mixed>|null
     */
    private function findSession(string $idCookie): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT stampRegister, ip, idCookie, idAccount, flagAPI
             FROM baseSession
             WHERE idCookie = :idCookie
             LIMIT 1',
        );
        $stmt->execute([':idCookie' => $idCookie]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }
}
