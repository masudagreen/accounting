# ADR-022: 最小構成の Web UI 再構築（Phase 7-1 基盤）

- ステータス: **承認（2026-04-21）**
- 決定者: ユーザ
- 関連: [ADR-001](ADR-001-directory-layout.md), [ADR-005](ADR-005-layered-architecture.md), [ADR-006](ADR-006-ports-and-adapters.md), [ADR-020](ADR-020-legacy-ui-retirement.md)

---

## 1. 文脈 (Context)

ADR-020 で旧 UI (`back/**`) を正式退役し、以降は `src/**` + `public/api/v1/*` の REST API のみを運用経路とした。一方で以下の制約が残った：

1. **税務判断は AI だけに任せられない**: 仕訳の科目判定、固定資産の耐用年数、消費税区分等は、最終的に人間（経理担当・税理士）が確認する UI が必須。REST API + JSON だけでは目視確認のオーバーヘッドが大きい。
2. **Claude 連携だけではカバーできない業務フロー**: AI で 80% は自動化できても、残り 20%（例：月次締め時の異例仕訳、期末調整の試算表レビュー）は人間が対話的に操作する画面が必要。
3. **Phase 6 で生成した PDF は「出力物」で「編集画面」ではない**: 12 種類の帳票 PDF は視覚検証済みだが、日常業務（仕訳入力・承認・修正）の UI は空白のまま。
4. **旧 UI 復活はリスク**: 旧 PHP 5 時代の設計（グローバル state、Smarty 3、Blowfish 暗号）を DocumentRoot 内に戻すのは退行。

以上より、新アーキテクチャ（Hexagonal + Bearer token + Smarty 5）の上に、**薄い server-side rendered UI** を構築する。

---

## 2. 決定 (Decision)

### 2.1 基本方針

- **新 UI は `/ui/*` に配置**: 既存 REST API (`/api/v1/*`) と同居するが、ルートは完全分離
- **server-side rendering (SSR) + Smarty 5 + Bootstrap 5**: SPA は採用しない（スコープ・保守コスト・SEO 不要の観点）
- **UI は REST API を内部で叩かず、UseCase を DI コンテナから直接解決**: 同一プロセスなので HTTP ラウンドトリップ不要
- **認証はセッション + Bearer token ブリッジ**: ログイン時に発行される Bearer token を `$_SESSION` に保持し、UI middleware が session 経由で取り出して API token リポジトリで検証
- **Bootstrap 5 は CDN 経由**: ローカル運用前提、依存物を追加しない

### 2.2 ルーティング二本立て

```
public/.htaccess
  /api/v1/*  -> public/api/v1/index.php  -> ApiKernel   -> JsonResponse
  /ui/*      -> public/ui/index.php      -> WebKernel   -> HtmlResponse
  その他      -> public/index.php         -> Kernel      -> 後方互換
```

`ApiKernel` と `WebKernel` は独立した Kernel。共通 DI コンテナ (`ContainerBootstrap::build()`) を共有するので UseCase 実装は一つだが、HTTP 入出力層は分離されている。

### 2.3 セッション方式

```
[ログイン POST /ui/login]
  ↓
LoginUseCase.execute()  -- ApiKernel と同一
  ↓
ApiToken 発行（DB 保存）+ plaintext 返却
  ↓
SessionStore::setUser()  -- $_SESSION に plaintext + user_id + token_id + display_name + email
  ↓
/ui/dashboard へリダイレクト
```

```
[以降のリクエスト GET /ui/*]
  ↓
AuthenticateSession::authenticate()
  ↓
$_SESSION から plaintext を取り出し SHA-256 → ApiTokenRepository::findByHash()
  ↓
isActive() 検証 + userId 整合性チェック
  ↓
OK: 認証済み / NG: session 破棄 + /ui/login へリダイレクト
```

セッション cookie は `rucaro_ui_sid` という専用名にして API と混在しないようにし、`HttpOnly` + `SameSite=Lax` を強制。HTTPS 配信時は `Secure` も付与。

### 2.4 CSRF

- `CsrfTokenManager` がフォーム ID ごとに 32 バイト乱数をセッション保存
- `<form>` の hidden `<input name="_csrf">` に埋め込み、POST 時に `validateToken()` で定数時間比較
- One-shot（使用後は即破棄）
- 有効期限 1 時間
- 代表フォーム: `ui_login`, `ui_logout`, `ui_entity_switch`

### 2.5 Flash メッセージ

- `FlashMessageBag` でリダイレクト後の一度きりのメッセージを実装
- `success` / `error` / `info` / `warning` の 4 種類、Bootstrap alert にマップ
- `consume()` で drain した時点で session からも消える

### 2.6 共通レイアウト

`storage/templates/ui/layout.html.tpl`:
- `<html lang="ja">`, UTF-8
- Bootstrap 5 CSS / JS (CDN)
- Bootstrap Icons (CDN)
- `font-family: system-ui, "Hiragino Sans", "Yu Gothic UI", "Meiryo", sans-serif` で日本語表示
- navbar（アプリ名 + entity セレクタ + fiscal_term + ユーザ名 + ログアウト）
- sidebar（仕訳一覧 / 元帳 / PL / BS）
- `{block name="content"}` で差し込み
- フッター: `Rucaro Accounting Web v1 — Phase 7-1 Rev.1`

---

## 3. 代替案と却下理由

| 代替案 | 却下理由 |
|---|---|
| SPA (React / Vue) + `/api/v1/*` 直接叩き | 認証方式を別系統（BFF または token in localStorage）で再設計する必要、依存増、SEO 不要の業務 UI で SPA のメリットが薄い |
| 旧 Smarty 3 テンプレート再利用 | Smarty 5 との API 非互換、旧 UI 設計思想（グローバル state）が漏れる |
| Bootstrap をローカル配置 | vendor 肥大化、CDN で十分（社内運用前提） |
| UI から REST API を HTTP で叩く | 同一プロセスで不要なラウンドトリップ、セッション ↔ Bearer token 変換も複雑化 |
| 認証を UI 独自の password session に切替 | API token と二重管理になる、revoke 不整合のリスク |
| `/ui/*` を `/app/*` に配置 | `ui/` のほうがレイヤー責務が明快（API ではない ≒ UI） |

---

## 4. 実装（Phase 7-1 スコープ）

### 4.1 追加ファイル

- `public/ui/index.php` — Web UI front controller
- `public/.htaccess` — rewrite 1 行追加 (`^ui(/.*)?$ ui/index.php`)
- `src/Http/WebKernel.php` — UI kernel、独自ルータ
- `src/Http/Middleware/AuthenticateSession.php` — session → Bearer token ブリッジ
- `src/Http/Response/HtmlResponse.php` — HTML / リダイレクト response
- `src/Http/Controller/Ui/LoginController.php` — GET/POST /ui/login
- `src/Http/Controller/Ui/LogoutController.php` — POST /ui/logout（token revoke 付き）
- `src/Http/Controller/Ui/DashboardController.php` — GET /ui/dashboard
- `src/Http/Controller/Ui/EntitySwitchController.php` — POST /ui/entity/switch
- `src/Support/Web/SessionStore.php` — `$_SESSION` ラッパ
- `src/Support/Web/CsrfTokenManager.php` — CSRF
- `src/Support/Web/FlashMessageBag.php` — flash メッセージ
- `src/Support/Web/SmartyViewRenderer.php` — Smarty 5 薄ラッパ
- `storage/templates/ui/layout.html.tpl` — 共通レイアウト
- `storage/templates/ui/login.html.tpl` — ログインフォーム
- `storage/templates/ui/dashboard.html.tpl` — ダッシュボード
- `storage/templates/ui/_components/{navbar,sidebar,flash,csrf}.tpl` — コンポーネント部品

### 4.2 変更ファイル

- `src/Support/Container/ContainerBootstrap.php` — 上記 7 サービス + 4 Controller を追加登録

### 4.3 テスト

- `tests/Unit/Support/Web/CsrfTokenManagerTest.php` — 9 ケース
- `tests/Unit/Support/Web/FlashMessageBagTest.php` — 4 ケース
- `tests/Unit/Support/Web/SessionStoreTest.php` — 5 ケース
- `tests/Unit/Http/Middleware/AuthenticateSessionTest.php` — 5 ケース
- `tests/Unit/Http/Response/HtmlResponseTest.php` — 4 ケース
- `tests/Unit/Http/WebKernelTest.php` — 2 ケース

### 4.4 スモーク確認

- `curl http://localhost:8080/ui/login` で HTML 取得、「ログイン」「Rucaro」「メールアドレス」等の日本語テキストが含まれる
- cookie jar を使って POST /ui/login → GET /ui/dashboard で「ダッシュボード」「ようこそ」等が含まれる

---

## 5. Phase 7-2 / 7-3 への引継事項

後続 Phase は以下をそのまま利用：

| 資産 | 場所 | 用途 |
|---|---|---|
| 共通レイアウト | `storage/templates/ui/layout.html.tpl` | `{extends}` で継承して `{block name="content"}` に新ページを差し込む |
| Sidebar の `active_nav` | `_components/sidebar.tpl` | 子テンプレートで `{assign var="active_nav" value="journals"}` 等を設定 |
| Flash メッセージ | `FlashMessageBag`, `_components/flash.tpl` | リダイレクト前に `addSuccess()` / `addError()` を呼ぶだけ |
| CSRF | `CsrfTokenManager::generateToken($formId)` + `validateToken($formId, $submitted)` | フォーム ID は `ui_*` で統一、ページ側で生成してテンプレに渡す |
| Entity / fiscal_term 選択状態 | `SessionStore::getSelectedEntity()`, `getSelectedFiscalTerm()` | Phase 7-2/7-3 の各 Controller が先頭で呼び出す |
| Session キー定数 | `SessionStore::KEY_*` | session のキー文字列は直接触らず定数経由 |
| 認証保証 | `WebKernel` が `public => false` なルートに対し自動で `AuthenticateSession::authenticate()` を呼ぶ | 新 Controller 追加時は WebKernel `$routes` 配列に追記するだけ |
| DI 配線 | `ContainerBootstrap` の Phase 7-1 セクション末尾 | 新 Controller は同じパターンで追加 |

Phase 7-2 スコープ（予定、本 ADR では実装しない）:
- `/ui/journals` — 一覧 / 作成 / 編集 / 削除 / 承認
- `/ui/ledger` — 勘定科目別 T/B 閲覧

Phase 7-3 スコープ（予定）:
- `/ui/pl` — 損益計算書
- `/ui/bs` — 貸借対照表

---

## 6. セキュリティ考慮

| リスク | 対策 |
|---|---|
| XSS | Smarty の `setEscapeHtml(true)` で全出力を自動エスケープ、明示的に `\|escape` を付与 |
| CSRF | Per-form token + one-shot + 1h TTL + `hash_equals` |
| セッション固定化 | ログアウト時に `session_destroy()` + cookie 無効化 |
| セッション hijack | `HttpOnly` + `SameSite=Lax` + HTTPS 時 `Secure` |
| Token 漏洩 | Plaintext は session のみ（DB は SHA-256 hash のみ、ADR-003 を継承） |
| Open redirect | `/ui/entity/switch` の Referer 検証で `/ui/` プレフィックス必須 |
| 認可ミス | Controller 側で `SessionStore::getUserId()` を必ず所有権チェックに渡す（例: `ListEntitiesUseCase` は `ownerUserId` でスコープ） |

---

## 7. 結果 (Consequences)

### Pros

- 税務判断が必要な業務（仕訳の最終確認、消費税区分レビュー等）を人間が UI で操作できるようになった
- REST API / UI 二重管理を回避（同一 UseCase を両者が共有）
- Phase 7-2 / 7-3 の追加ページは layout + session + CSRF + flash + sidebar がすでに揃っているので、Controller + template 2 点追加で完結する
- 旧 UI を復活させず、新アーキテクチャの延長線上でモダン Web UI を実現

### Cons

- PHP session を有効化するので stateless 運用はできなくなる（UI プロセスに限定）
- CDN 経由の Bootstrap 依存（オフライン環境では要対応、将来 vendor 配置オプションを追加可能）
- SSR なので非常に重い UX（巨大リアルタイム更新等）には不向き。その場合は別途 Phase で SPA 化を検討

---

## 8. 関連リンク

- [ADR-020 旧 UI 退役](ADR-020-legacy-ui-retirement.md)
- `src/Http/WebKernel.php`
- `src/Http/Middleware/AuthenticateSession.php`
- `src/Support/Web/SessionStore.php`
- `src/Support/Web/CsrfTokenManager.php`
- `storage/templates/ui/layout.html.tpl`
- OpenAPI `/api/v1/auth/login` — UI 側の LoginUseCase と同じコードパス
