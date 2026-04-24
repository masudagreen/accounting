# Phase 1.1.b: 暗号化列の保存フォーマット解析

> 調査者: Explore サブエージェント
> 対象: `C:\Users\yusuk\StudioProjects\accounting\`
> 目的: 旧 Blowfish CBC 暗号化カラムの解析。**新アプリでは再暗号化不要（旧DB温存方針）** だが、必要に応じて旧データを復号してインポートできるよう記録。

---

## 1. 暗号化カラム一覧（サマリ）

| Table.Column | 平文型 | 典型サイズ | Writers | Readers | 非暗号化対応列 |
|---|---|---|---|---|---|
| `accountingFile.strPassword` | ASCII パスワード | 10〜30 bytes | 1 | 1 | なし |
| `accountingLogMail[Nation].strPassword` | ASCII パスワード | 10〜30 bytes | 1 | 1 | なし |
| `accountingBlueSheet[Nation].blobData` | UTF-8 JSON | 500〜5000 bytes | 1 | 1 | なし |
| `accountingLogBanksAccount.blobDetail` | UTF-8 JSON | 1000〜10000 bytes | 1 | 7 | なし |

**暗号化カラムは 4 種類のみ**。ネット銀行連携とメール/ファイル取込のパスワード・認証情報に限定。

---

## 2. カラム別詳細

### 2.1 `accountingFile.strPassword`
- **Writer**: `back/class/else/plugin/accounting/FilePreference.php:925`
- **Reader**: `back/class/else/plugin/accounting/CalcFileImport.php:108`
- **平文**: プレーンテキストパスワード（UTF-8）、10〜30 文字
- **用途**: FTP/IMAP ファイル取込時の認証資格情報
- **SQL 列順**: 3 番目（strHost, strUser の後）

### 2.2 `accountingLogMail[Nation].strPassword`
- **Writer**: `back/class/else/plugin/accounting/jpn/LogPreference.php:685`
- **Reader**: `back/class/else/plugin/accounting/jpn/CalcLogImportMail.php:119`
- **平文**: プレーンテキストパスワード（UTF-8）、10〜30 文字
- **用途**: IMAP メールサーバ認証（メール取込用）
- **注**: テーブル名に国コードが付く（例: `accountingLogMailJpn`）

### 2.3 `accountingBlueSheet[Nation].blobData`
- **Writer**: `back/class/else/plugin/accounting/jpn/2012/public/BlueSheetEditor.php:262`
- **Reader**: `back/class/else/plugin/accounting/jpn/2012/public/BlueSheet.php:255`
- **平文**: UTF-8 JSON（青色申告シートの明細行・会計期間）
- **典型構造**: `{"lineItems": [...], "totals": {...}, "period": ...}`
- **復号後処理**: `json_decode($plaintext, true)`

### 2.4 `accountingLogBanksAccount.blobDetail`
- **Writers**:
  - `back/class/else/plugin/accounting/jpn/BanksAccountEditor.php:206`（INSERT）
  - `back/class/else/plugin/accounting/jpn/BanksAccountEditor.php:455`（UPDATE）
- **Readers**（7 箇所）:
  - `back/class/else/plugin/accounting/jpn/BanksAccount.php:714`
  - `back/class/else/plugin/accounting/jpn/calcBanks/Japannetbank.php:90, 139`
  - `back/class/else/plugin/accounting/jpn/calcBanks/Japanpostbank.php:90, 139`
  - `back/class/else/plugin/accounting/jpn/calcBanks/Jibunbank.php:93, 142`
  - `back/class/else/plugin/accounting/jpn/calcBanks/Sumisinnetbank.php:90, 139`
  - `back/class/else/plugin/accounting/jpn/calcBanks/Surugabank.php:92, 141`
- **平文**: UTF-8 JSON（ネット銀行の資格情報と口座詳細）
- **復号後処理**: `json_decode($plaintext, true)`

---

## 3. 暗号化アルゴリズム仕様

### アルゴリズム
- **Cipher**: Blowfish CBC（`MCRYPT_BLOWFISH` + `MCRYPT_MODE_CBC`）
- **Key**: `substr(md5($masterSecret), 0, 56)` — Blowfish 最大鍵長 56 バイト
- **IV**: `substr(md5($key), 0, 8)` — Blowfish ブロックサイズ 8 バイト、**鍵から決定的に導出**
- **Padding**: Zero padding（PKCS#7 ではない）
- **復号時**: `rtrim($plaintext, "\0")` で末尾 null を除去

### 既知の脆弱性
- **IV が決定的**: 同じ平文が常に同じ暗号文になる（意味的秘匿性なし）。新実装では問題化する前にランダム IV + GCM へ移行推奨。
- **鍵派生が md5**: 計算が速く brute-force に弱い。HKDF-SHA256 へ切替推奨。

---

## 4. PHP 8.3 での互換復号レシピ

mcrypt は PHP 7.2 で削除されたが、OpenSSL の `bf-cbc` で完全互換復号が可能。

```php
<?php
declare(strict_types=1);

/**
 * レガシー Blowfish CBC データの復号
 * mcrypt (MCRYPT_BLOWFISH + MCRYPT_MODE_CBC) と byte-exact 互換
 */
function decryptLegacyData(string $ciphertext, string $masterSecret): string
{
    $key = substr(md5($masterSecret), 0, 56); // Blowfish 鍵最大 56 バイト
    $iv  = substr(md5($key), 0, 8);           // Blowfish ブロック 8 バイト

    $plaintext = openssl_decrypt(
        $ciphertext,
        'bf-cbc',
        $key,
        OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
        $iv
    );

    if ($plaintext === false) {
        throw new RuntimeException('Decryption failed: ' . (openssl_error_string() ?: 'unknown'));
    }

    return rtrim($plaintext, "\0");
}

// 使用例
$masterSecret = trim((string) file_get_contents('back/dat/crypte/data.cgi'));

// パスワード復号
$plainPassword = decryptLegacyData($dbRow['strPassword'], $masterSecret);

// JSON blob 復号
$jsonPlain = decryptLegacyData($dbRow['blobData'], $masterSecret);
$data      = json_decode($jsonPlain, true, 512, JSON_THROW_ON_ERROR);
```

**注意**: PHP 8 + OpenSSL 3.0 環境では Blowfish が "legacy" 扱いで、`openssl.cnf` に `legacy = legacy_sect` を有効化する必要がある場合あり。PHP 公式 Docker イメージ（8.x-apache）なら通常デフォルトで動作。

---

## 5. マスタ鍵ファイル `back/dat/crypte/data.cgi`

### 仕様
- **場所**: `back/dat/crypte/data.cgi`
- **現状**: **ディスク上に存在しない** → 初回暗号化時に自動生成される
- **生成方法**: `hash('sha256', MICROTIMESTAMP . randomPassword())`
- **形式**: 1 行 SHA-256 ハッシュ（64 hex 文字）
- **保存形式**: 平文（ファイルシステム権限に依存）

### 推奨権限
- `0600`（所有者のみ read/write）
- 生成直後に `chmod 600 back/dat/crypte/data.cgi`

### ローテーション
- **現設計にはローテ機構なし**
- 鍵変更時は全暗号化データが読めなくなる
- 新アプリ設計時は鍵バージョニング（v1/v2 プレフィックス）を組み込むこと

### セキュリティ改善案（新アプリ）
- 環境変数 or Secrets Manager から読む
- バージョン管理に含めない（`.gitignore`）
- ファイル所有者アサートを起動時に実施

---

## 6. 暗号文のバイトフォーマット

| 項目 | 値 |
|---|---|
| 構造 | 生バイナリ blob（可変長） |
| DB 格納 | BLOB / LONGBLOB（Base64 なし） |
| Padding | Zero padding（PKCS#7 ではない） |
| IV | 鍵から決定的に導出、メッセージ毎にランダムではない |

---

## 7. 呼出しパターンの均一性

すべての呼出しは以下の 2 パターンのみ。バリエーションなし:

**暗号化（Writers）**:
```php
$encrypted = $classCrypte->setEncrypt(array('data' => $plaintext));
// $encrypted をそのまま DB BLOB 列に格納
```

**復号（Readers）**:
```php
$plaintext = $classCrypte->setDecrypt(array('data' => $blobFromDb));
// JSON の場合: $array = json_decode($plaintext, true);
```

- すべて `array('data' => ...)` 形式
- カスタム鍵なし（全てマスタ鍵）
- 追加オプションなし

---

## 8. 新アプリでの扱い（方針確認）

- **旧DB は温存、再暗号化不要**（Phase 1 確定事項）
- 新アプリは AES-256-GCM + ランダム nonce + HKDF-SHA256 で新規暗号化
- 旧データ参照が必要になったら、上記「互換復号レシピ」で個別復号 → 新 API 経由で新アプリにインポート
- 旧銀行連携データ（`blobDetail`）を新アプリに持ち込むなら、ネット銀行 5 社分の JSON スキーマを逆解析する必要あり（Phase 3 以降の外部連携再設計時に実施）

---

## 9. Summary

- **暗号化カラムは 4 種類のみ**、すべてネット銀行連携とファイル/メール取込の資格情報
- **アルゴリズム**: Blowfish CBC, key=md5(secret)[:56], iv=md5(key)[:8], zero padding
- **互換復号**: PHP 8.3 + OpenSSL `bf-cbc` で byte-exact 再現可
- **マスタ鍵**: `back/dat/crypte/data.cgi`、ローテ機構なし、新アプリでは Secrets Manager へ移行推奨
- **新アプリ**: 再暗号化不要、AES-256-GCM で新規データのみ暗号化
