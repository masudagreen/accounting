# ADR-003: 暗号化方針（AES-256-GCM + レガシー互換復号）

- ステータス: 提案中 (2026-04-20)
- 決定者: (ユーザ承認待ち)
- 関連: PLAN.md, ADR-001（新ディレクトリ構成）, ADR-002（新 DB スキーマ方針）
- 参照資料: `docs/phase1/encrypted-columns.md`, `docs/phase1/legacy-schema.md` §5

---

## 1. 文脈

旧 Rucaro Accounting は PHP 5 時代に設計されており、可逆暗号が必要な 4 カラム（`accountingFile.strPassword`, `accountingLogMailJpn.strPassword`, `accountingLogBanksAccount.blobDetail`, `accountingBlueSheetJpn.blobData`）に対して **mcrypt ベースの Blowfish CBC** を採用していた。`docs/phase1/encrypted-columns.md` の解析の結果、以下の設計上の問題が明らかになっている。

1. **mcrypt 依存**: `mcrypt` 拡張は PHP 7.2 で削除されており、PHP 8.3 ではそのままでは動作しない。OpenSSL の `bf-cbc` で byte-exact 復号は可能だが、新実装に持ち込むべき技術ではない。
2. **決定的 IV**: IV が `substr(md5($key), 0, 8)` と鍵から導出されており、メッセージ毎にランダム化されていない。同じ平文が常に同じ暗号文になり意味的秘匿性（semantic security）を欠く。
3. **脆弱な鍵派生**: マスタ鍵 `back/dat/crypte/data.cgi` は `hash('sha256', microtime . randomPassword)` で生成されたあと `substr(md5($masterSecret), 0, 56)` で派生される。md5 は KDF として不適格で、カラムごとの鍵分離もない。
4. **Zero padding**: PKCS#7 ではなく null バイト埋め。平文末尾に `\0` を含むバイナリを格納すると復号時に欠落する（JSON には無害だが、将来バイナリ取り扱いが生じるとバグ源）。
5. **AAD 非対応**: Blowfish CBC は認証付き暗号ではないため、暗号文改ざんを検知できない。ネット銀行資格情報のような高感度データでは致命的。
6. **鍵ローテーション機構なし**: 鍵更新すると既存データが全て読めなくなる。

`legacy-schema.md` §5 で挙げられた 6 列のうち、`baseAccount.strPassword` と `baseLoginPassword.strPassword` は**不可逆ハッシュ（Phase 1.1.b で未確定）** の疑いがあり、本 ADR の可逆暗号の対象外。本 ADR は可逆暗号が必要な残り 4 列、および新規アプリで発生する新しい機密カラム（OAuth refresh token、API key、Claude API 応答中の PII など）を対象とする。

PLAN.md §0 で「ローカル使用前提、過度な強度不要、最低限の健全性は維持」が確定済み。旧 DB は温存し**再暗号化バッチは実施しない**。必要時のみ OpenSSL `bf-cbc` で旧データを個別復号し新 API に import する方針。

---

## 2. 決定

### 2.1 新規暗号化アルゴリズム

- **Cipher**: **AES-256-GCM**
- **Nonce**: 12 バイトを `random_bytes()` で生成（メッセージ毎にランダム）
- **Tag**: 16 バイト（GCM 既定）
- **AAD**: 「テーブル名 + カラム名 + 行主キー」を連結した文字列（§4 参照）
- **PHP 実装**: `openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $nonce, $tag, $aad)` / `openssl_decrypt(..., $tag, $aad)`

### 2.2 鍵派生

- **KDF**: **HKDF-SHA256**（`hash_hkdf('sha256', $masterKey, 32, $info, $salt)`）
- **info**: `"rucaro/accounting/v1/{table}/{column}"` — アプリ名 / バージョン / 列コンテキストを組み込み、列ごとに派生鍵を分離
- **salt**: 空文字列（`''`）で十分。マスタ鍵自体が十分な長さの一様乱数であるため
- **出力長**: 32 バイト（AES-256 鍵長）

### 2.3 マスタ鍵

- **保管場所**: プロジェクトルート `.env` の `APP_ENCRYPTION_KEY`
- **フォーマット**: base64url で encode した 32 バイト（= 43 文字の base64url 文字列）
- **起動時バリデーション**: `ConnectionFactory` と同レイヤーの `CipherFactory` で boot 時に鍵長をチェックし、不正なら `InvalidEncryptionKeyException` を投げて即停止
- **`.gitignore`**: `.env` は必ず Git 管理外。`.env.example` に形式のサンプルのみ記載
- **ファイル権限**: ローカル運用なので `0600` 推奨だが強制はしない

### 2.4 保存フォーマット

```
v2:k1:<base64url(nonce(12) || ciphertext(N) || tag(16))>
```

- 先頭 `v2` は**スキーマバージョン**（v1 は旧 Blowfish。v2 は AES-256-GCM）
- 続く `k1` は**鍵バージョン**。将来の鍵ローテ時に `k2`, `k3` と増やして共存できる
- base64url は URL safe 変種（パディング `=` なし）。DB には `VARBINARY` / `BLOB` ではなく `VARCHAR(768)` など**テキスト列**に格納し、dump / diff / grep がしやすい形で運用する
- バージョンプレフィックスを見て `AesGcmCipher` と `LegacyBlowfishDecryptor` をディスパッチする `VersionedCipher` を上位に置く（§3 参照）

### 2.5 旧データ互換復号

- `Rucaro\Infrastructure\Crypto\LegacyBlowfishDecryptor` を用意
- **`decrypt` のみ**を公開。`encrypt` は実装しない（新規書き込みは新形式のみ）
- マスタシークレットは `LEGACY_ENCRYPTION_SECRET` 環境変数から読み、`.env` では通常コメントアウト。旧 DB 参照時のみ一時的に有効化する運用

### 2.6 鍵ローテーション

- Phase 1 では `k1` 固定。ローテ機構は実装しない
- 将来（Phase 4 以降）必要になった場合、`CipherFactory` が `k1`, `k2` 両方の鍵を保持し、読み込み時は埋め込まれた鍵バージョンでディスパッチ、書き込み時は最新鍵を使う方式に拡張する
- その場合の ADR-004（仮）として別途策定

---

## 3. インターフェース設計

### 3.1 `CipherInterface`

```php
<?php
declare(strict_types=1);

namespace Rucaro\Infrastructure\Crypto;

interface CipherInterface
{
    /**
     * 平文を暗号化し、"v2:k1:<base64url>" 形式のトークンを返す。
     *
     * @param string $plaintext バイナリセーフな平文
     * @param string $aad       Additional Authenticated Data（§4 で定義する形式を推奨）
     * @return string 自己完結した暗号化トークン
     */
    public function encrypt(string $plaintext, string $aad = ''): string;

    /**
     * 暗号化トークンを復号する。バージョンプレフィックスを読み、
     * 対応する実装にディスパッチする。
     *
     * @param string $token  encrypt() の戻り値、または legacy 互換形式
     * @param string $aad    encrypt 時と同じ AAD
     * @return string 復号済み平文
     * @throws CryptoException 改ざん検知、バージョン未対応、鍵不一致など
     */
    public function decrypt(string $token, string $aad = ''): string;
}
```

### 3.2 `AesGcmCipher`

AES-256-GCM 単独の実装。`encrypt` / `decrypt` ともに `v2:k1:...` 形式のみ扱う。

### 3.3 `LegacyBlowfishDecryptor`

旧 Blowfish CBC 専用。`decrypt` のみ公開。

### 3.4 `VersionedCipher`（合成）

バージョン文字列でディスパッチするファサード。アプリケーション層は常にこれを DI 経由で受け取る。

### 3.5 テスト方針

- `tests/Unit/Infrastructure/Crypto/AesGcmCipherTest.php`
  - ラウンドトリップ（encrypt → decrypt）
  - **改ざん検知**: tag を 1 バイト書き換えると `CryptoException`
  - **AAD 不一致**: 暗号化時と復号時で異なる AAD を渡すと `CryptoException`
  - **バージョンプレフィックス誤り**: `v9:k1:...` のような未知バージョンで失敗
- `tests/Unit/Infrastructure/Crypto/LegacyBlowfishDecryptorTest.php`
  - 旧アプリで作成した実データから切り出した **既知平文 / 暗号文ペア**を fixture 化し `tests/fixtures/legacy-crypto/*.bin` に配置、byte-exact 復号を検証
  - 復号後に末尾 `\0` が落ちていることの確認
- `tests/Unit/Infrastructure/Crypto/VersionedCipherTest.php`
  - `v1:...`（旧） / `v2:k1:...`（新）の両フォーマットで正しくディスパッチ

---

## 4. AAD（Additional Authenticated Data）の使い方

AAD はそれ自身は暗号化されないが、tag 計算に組み込まれる。ローカル用途でも **コピペ攻撃（暗号文を別の行にコピーして成り代わり）** への安価で効果的な対策となるため採用する。

### 形式

```
"{table}/{column}/{primaryKey}"
```

例:

- `accountingLogBanksAccount/blobDetail/1042`
- `accountingFile/strPassword/87`

### 利点

- 同じ平文（例: 同一パスワード）が異なる行に格納されていても、異なる AAD のため暗号文を移植できない
- 将来的な audit log で「どのテーブル・どの行の復号か」を AAD から追跡可能

### 注意

- 新規 INSERT 時に主キーが未確定な場合は **2 段階書き込み**（INSERT で仮値 → 確定 PK で UPDATE）または ULID/UUIDv7 を事前生成して AAD に組み込む（ADR-002 の ID 戦略と整合）

---

## 5. サンプル実装

以下 3 クラスは Phase 2 で `src/Infrastructure/Crypto/` に配置する最小骨格。PHP 8.3 の `readonly` プロパティ、型宣言、コンストラクタプロモーションを使用。合計 140 行程度。

### 5.1 `CipherInterface.php`

```php
<?php
declare(strict_types=1);

namespace Rucaro\Infrastructure\Crypto;

interface CipherInterface
{
    public function encrypt(string $plaintext, string $aad = ''): string;
    public function decrypt(string $token, string $aad = ''): string;
}

final class CryptoException extends \RuntimeException {}
```

### 5.2 `AesGcmCipher.php`

```php
<?php
declare(strict_types=1);

namespace Rucaro\Infrastructure\Crypto;

final readonly class AesGcmCipher implements CipherInterface
{
    private const CIPHER       = 'aes-256-gcm';
    private const NONCE_BYTES  = 12;
    private const TAG_BYTES    = 16;
    private const SCHEMA       = 'v2';
    private const KEY_VERSION  = 'k1';
    private const HKDF_INFO    = 'rucaro/accounting/v1';

    public function __construct(
        private string $masterKey, // 32-byte binary
        private string $tableName,
        private string $columnName,
    ) {
        if (strlen($this->masterKey) !== 32) {
            throw new CryptoException('APP_ENCRYPTION_KEY must decode to exactly 32 bytes.');
        }
    }

    public function encrypt(string $plaintext, string $aad = ''): string
    {
        $nonce   = random_bytes(self::NONCE_BYTES);
        $tag     = '';
        $derived = $this->deriveKey();

        $cipher = openssl_encrypt(
            $plaintext, self::CIPHER, $derived,
            OPENSSL_RAW_DATA, $nonce, $tag, $aad, self::TAG_BYTES,
        );
        if ($cipher === false) {
            throw new CryptoException('AES-GCM encryption failed: ' . openssl_error_string());
        }

        $payload = $nonce . $cipher . $tag;
        return sprintf('%s:%s:%s', self::SCHEMA, self::KEY_VERSION, self::base64UrlEncode($payload));
    }

    public function decrypt(string $token, string $aad = ''): string
    {
        $parts = explode(':', $token, 3);
        if (count($parts) !== 3 || $parts[0] !== self::SCHEMA || $parts[1] !== self::KEY_VERSION) {
            throw new CryptoException('Unsupported cipher token format: ' . substr($token, 0, 16));
        }

        $blob = self::base64UrlDecode($parts[2]);
        if (strlen($blob) < self::NONCE_BYTES + self::TAG_BYTES + 1) {
            throw new CryptoException('Cipher payload too short.');
        }

        $nonce = substr($blob, 0, self::NONCE_BYTES);
        $tag   = substr($blob, -self::TAG_BYTES);
        $cipher = substr($blob, self::NONCE_BYTES, -self::TAG_BYTES);

        $plain = openssl_decrypt(
            $cipher, self::CIPHER, $this->deriveKey(),
            OPENSSL_RAW_DATA, $nonce, $tag, $aad,
        );
        if ($plain === false) {
            throw new CryptoException('AES-GCM decryption failed (tamper / wrong key / wrong AAD).');
        }
        return $plain;
    }

    private function deriveKey(): string
    {
        $info = sprintf('%s/%s/%s', self::HKDF_INFO, $this->tableName, $this->columnName);
        return hash_hkdf('sha256', $this->masterKey, 32, $info, '');
    }

    private static function base64UrlEncode(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $s): string
    {
        $pad = strlen($s) % 4;
        if ($pad) { $s .= str_repeat('=', 4 - $pad); }
        $decoded = base64_decode(strtr($s, '-_', '+/'), true);
        if ($decoded === false) {
            throw new CryptoException('Invalid base64url payload.');
        }
        return $decoded;
    }
}
```

### 5.3 `LegacyBlowfishDecryptor.php`

```php
<?php
declare(strict_types=1);

namespace Rucaro\Infrastructure\Crypto;

final readonly class LegacyBlowfishDecryptor
{
    public function __construct(private string $legacyMasterSecret) {}

    /**
     * 旧 mcrypt(MCRYPT_BLOWFISH + MCRYPT_MODE_CBC) と byte-exact 互換。
     * 新規暗号化には使用しない。旧 DB からの参照・インポート専用。
     */
    public function decrypt(string $ciphertext): string
    {
        $key = substr(md5($this->legacyMasterSecret), 0, 56); // 旧実装の鍵派生
        $iv  = substr(md5($key), 0, 8);                        // 決定的 IV

        $plain = openssl_decrypt(
            $ciphertext, 'bf-cbc', $key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv,
        );
        if ($plain === false) {
            throw new CryptoException(
                'Legacy Blowfish decryption failed: ' . (openssl_error_string() ?: 'unknown')
            );
        }
        return rtrim($plain, "\0"); // Zero padding を剥がす
    }
}
```

上記 3 ファイルを合わせて 140 行弱。`VersionedCipher` は Phase 2 実装時に追加する（`v1:` プレフィックス or プレフィックスなしなら Legacy、`v2:` なら AesGcm にディスパッチする 30 行程度の簡易ファサード）。

---

## 6. 鍵生成コマンド

`bin/cowork.php crypto:generate-key` を Symfony Console で提供。base64url でエンコードした 32 バイト鍵を標準出力し、`.env` への書き込み手順を案内する。

```php
<?php
declare(strict_types=1);

namespace Rucaro\Infrastructure\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'crypto:generate-key',
    description: 'Generate a new 32-byte master encryption key (base64url).',
)]
final class GenerateEncryptionKeyCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption('raw', null, InputOption::VALUE_NONE, 'Print raw base64url only (pipeable).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = random_bytes(32);
        $encoded = rtrim(strtr(base64_encode($key), '+/', '-_'), '=');

        if ($input->getOption('raw')) {
            $output->write($encoded);
            return Command::SUCCESS;
        }

        $output->writeln('<info>Generated APP_ENCRYPTION_KEY:</info>');
        $output->writeln($encoded);
        $output->writeln('');
        $output->writeln('Add the following line to your <comment>.env</comment> file:');
        $output->writeln(sprintf('    APP_ENCRYPTION_KEY=%s', $encoded));
        $output->writeln('');
        $output->writeln('<comment>WARNING:</comment> rotating this key invalidates all v2:k1 ciphertexts.');
        $output->writeln('Keep a secure backup before overwriting.');
        return Command::SUCCESS;
    }
}
```

### 運用メモ

1. 初回セットアップ: `php bin/cowork.php crypto:generate-key --raw >> .env` の後、ファイルを開いて `APP_ENCRYPTION_KEY=` を手動で行頭に付ける（`--raw` は値のみ出力するため、`KEY=` プレフィックスを自分で付けたほうが事故が少ない）。
2. バックアップ: `.env` は個人のパスワードマネージャ（Bitwarden / 1Password 等）に保管する。
3. 紛失時の影響: 4 カラムと今後暗号化する列が**全て復号不能**になる。旧 DB は温存されているため、旧アプリの `back/dat/crypte/data.cgi` が残っていれば `LegacyBlowfishDecryptor` 経由で再生は可能。

---

## 7. 結果

### Pros

- **AEAD 採用**で改ざん検知。銀行資格情報などの高感度データでも最低限の健全性を確保
- **ランダム nonce** により意味的秘匿性を獲得。同一パスワードが同一暗号文にならない
- **HKDF-SHA256 + 列別 info** により、1 列の鍵が仮に漏洩しても他列へ影響が及ばない
- **バージョンプレフィックス**により、将来のアルゴリズム更新・鍵ローテを破壊的変更なしに段階導入可能
- **PHP 8.3 標準ライブラリ**（`openssl_*`, `hash_hkdf`, `random_bytes`）のみ。外部 composer 依存ゼロで攻撃面を縮小
- **旧データ互換**: `LegacyBlowfishDecryptor` により旧 DB を温存したまま必要時に参照可能

### Cons

- **鍵管理のコスト増**: `.env` バックアップを怠ると全データ喪失。本設計では鍵ローテ未実装のため、`APP_ENCRYPTION_KEY` の漏洩時に再暗号化バッチが必要になる（Phase 4 以降の課題）
- **AAD 設計の規律**: 呼び出し側が `(table, column, pk)` を一貫して渡す必要があり、実装時に抽象化が甘いと AAD 不一致で復号失敗する事故の余地がある。Repository 層で AAD 組み立てを集中化することで対処
- **base64url テキスト格納**によりバイナリ直接格納より ~33% 容量増。ローカル運用では無視可能
- **GCM の nonce 再利用リスク**: 同じ鍵 + 同じ nonce で暗号化すると機密性が崩壊する。ランダム 12 バイト nonce は `2^48` 回の暗号化までは安全余裕があり、ローカル会計用途では事実上問題にならない

---

## 8. 代替案と却下理由

### 8.1 libsodium `sodium_crypto_secretbox`（XSalsa20-Poly1305）

- **却下理由**: `sodium_crypto_secretbox` は AAD を取れない（AAD 付きが欲しければ `sodium_crypto_aead_xchacha20poly1305_ietf_encrypt` になる）。標準 AES-GCM に比べエコシステム・外部ツール（OpenSSL CLI 互換等）への習熟機会が少なく、チームメンテコストが増す。性能・安全性ともに優れるが、本プロジェクトでは AEAD 機能と標準ツール互換を優先して AES-256-GCM を採用。

### 8.2 旧 `bf-cbc` 継続

- **却下理由**: ローカル用途であっても (a) AEAD なしで改ざん検知不可、(b) 決定的 IV、(c) md5 鍵派生、(d) 64-bit ブロックの Sweet32 脆弱性、といった教育的にも悪例が多い。旧データの**復号互換のみ**残し、新規書き込みには使わない。

### 8.3 AES-256-CBC + HMAC-SHA256（Encrypt-then-MAC 手組み）

- **却下理由**: MAC 付与を手で書くと**順序ミス / constant-time 比較忘れ / 鍵使い回し**などの実装事故が入りやすい。AES-GCM なら一発で AEAD が得られ、OpenSSL 内部で constant-time 比較が保証される。「最低限の健全性を維持」の方針では手組みを避けるほうが合理的。

### 8.4 Laravel `Illuminate\Encryption\Encrypter` をそのまま流用

- **却下理由**: Laravel 本体の依存を引き込むのは過剰。`symfony/console`, `monolog`, `guzzle` などを個別導入する PLAN.md §1.3 の方針と不整合。薄い `CipherInterface` 自前実装のほうが DI しやすく、テストも容易。

---

## 9. 実装チェックリスト（Phase 2 Infrastructure 層）

- [ ] 1. `src/Infrastructure/Crypto/CipherInterface.php` 新規作成
- [ ] 2. `src/Infrastructure/Crypto/CryptoException.php` 新規作成
- [ ] 3. `src/Infrastructure/Crypto/AesGcmCipher.php` 新規作成（§5.2）
- [ ] 4. `src/Infrastructure/Crypto/LegacyBlowfishDecryptor.php` 新規作成（§5.3）
- [ ] 5. `src/Infrastructure/Crypto/VersionedCipher.php` 新規作成（`v1:` / `v2:` 分岐のファサード）
- [ ] 6. `src/Infrastructure/Crypto/CipherFactory.php` 新規作成（`.env` からマスタ鍵読込、起動時バリデーション、テーブル × カラム別に `AesGcmCipher` を生成）
- [ ] 7. `bin/cowork.php` に `crypto:generate-key` コマンドを登録（§6）
- [ ] 8. `.env.example` に `APP_ENCRYPTION_KEY=` / `LEGACY_ENCRYPTION_SECRET=` のプレースホルダ追記
- [ ] 9. `.gitignore` に `.env` が含まれていることを確認（Phase 1 で対応済みなら再確認のみ）
- [ ] 10. `tests/Unit/Infrastructure/Crypto/AesGcmCipherTest.php` 作成（ラウンドトリップ / tag 改ざん / AAD 不一致 / 不正バージョン / 短すぎる payload）
- [ ] 11. `tests/Unit/Infrastructure/Crypto/LegacyBlowfishDecryptorTest.php` 作成（fixture による既知平文 / 暗号文ペア検証）
- [ ] 12. `tests/fixtures/legacy-crypto/` 配下に旧データの既知ペア 3〜5 件を配置（blobDetail 1 件 / strPassword 2 件 / blobData 1 件、個人情報は消去）
- [ ] 13. `tests/Unit/Infrastructure/Crypto/VersionedCipherTest.php` 作成
- [ ] 14. `docs/api/openapi.yaml` または Repository 実装の規約に AAD 形式 `"{table}/{column}/{pk}"` を明文化
- [ ] 15. PHPStan level 6 green、Psalm level 3 green を維持
- [ ] 16. `code-reviewer` エージェントでレビュー、`security-reviewer` でもレビュー通過
- [ ] 17. README / `docs/SETUP.md` に「初回セットアップで `crypto:generate-key` を実行して `.env` に書き込む」手順を追記
- [ ] 18. 将来の鍵ローテ用 ADR-004 のスケルトンを `docs/adr/` に TODO メモとして残す

---

## 10. 参考

- NIST SP 800-38D: GCM の使用条件（nonce 一意性要件）
- RFC 5869: HKDF
- PHP Manual: `openssl_encrypt`, `hash_hkdf`, `random_bytes`
- `docs/phase1/encrypted-columns.md` §3「暗号化アルゴリズム仕様」「既知の脆弱性」
- `docs/phase1/encrypted-columns.md` §4「PHP 8.3 での互換復号レシピ」
- `docs/phase1/legacy-schema.md` §5「暗号化カラム一覧」
