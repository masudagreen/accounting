# Sprint 13: Shadow Web UI (Phase 1)

## 概要

Shadow Web UI は、既存の legacy app (`index.php` 経由) と **並走** する **読取専用の現新比較 Web UI** です。
同一の `rucaro` データベースを共有し、新ドメイン計算値と旧 DB 保存値を並列表示して差異を可視化します。

---

## 起動方法

```bash
docker compose up -d
```

起動後 http://localhost:8080 でアクセス可能。

---

## URL

| パス | 内容 |
|------|------|
| `http://localhost:8080/compare/` | ホーム (事業体・期セレクタ) |
| `http://localhost:8080/compare/?page=trial-balance&entity=1&period=14` | 試算表比較 |
| `http://localhost:8080/compare/?page=profit-loss&entity=1&period=14` | 損益計算書比較 |
| `http://localhost:8080/compare/?page=balance-sheet&entity=1&period=14` | 貸借対照表比較 |
| `http://localhost:8080/compare/?page=journal-list&entity=1&period=14` | 仕訳一覧 |

---

## 認証

legacy ログイン (`http://localhost:8080/`) を使って `rucaro` にログインしてください。
Shadow UI は legacy の `baseSession` テーブルを参照して認証します。Cookie キー: `id`。

未ログイン状態で `/compare/` にアクセスすると `/` (legacy ログイン画面) にリダイレクトされます。

---

## ファイル構成

```
compare/
├── .htaccess          POST 禁止・セキュリティヘッダー設定
├── index.php          Front controller (認証→ルーティング→レンダリング)
└── assets/
    └── compare.css    スタイルシート

src/Compare/
├── Auth/
│   └── SessionAuthenticator.php   baseSession 参照による認証
├── Page/
│   ├── HomePage.php               事業体・期セレクタ
│   ├── TrialBalancePage.php        試算表現新比較
│   ├── ProfitLossPage.php          損益計算書現新比較
│   ├── BalanceSheetPage.php        貸借対照表現新比較
│   └── JournalListPage.php         仕訳一覧
├── Routing/
│   └── Router.php                 GET ?page=xxx の解決
└── View/
    ├── HtmlHelper.php             エスケープ・フォーマット・レイアウト
    └── NavBuilder.php             ナビゲーションメニュー生成

tests/Unit/Compare/
├── SessionAuthenticatorTest.php
└── Routing/
    └── RouterTest.php

tests/Integration/Compare/
└── PageRenderingIntegrationTest.php
```

---

## 段階的移行ロードマップ

### Phase 1 (現在): Shadow Web UI 構築

- [x] 読取専用の比較 Web UI を `/compare/` に構築
- [x] legacy session 共有による認証
- [x] 試算表・PL・BS・仕訳一覧の現新比較
- [x] 差異の可視化 (差異行を赤背景で表示)
- [x] 不変条件チェック (借方=貸方, 資産=負債+純資産)

### Phase 2 (計画): 新ドメイン精度向上

- 期首残高 (OpeningBalances) を DB から取得して試算表精度を改善
- 月次比較・部門別比較の追加
- 仕訳一覧の詳細表示 (科目名・金額展開)
- 差異レポートの CSV エクスポート

### Phase 3 (計画): 段階的切り替え

- 新ドメインの計算結果を DB に書き込む dual-write を検討
- legacy UI を新 UI に段階的に置換
- legacy 依存コードの廃止

---

## セキュリティ

- 全出力を `htmlspecialchars` でエスケープ
- SQL はプリペアドステートメントのみ使用
- POST リクエストは `.htaccess` でブロック
- 本番データ (顧客名・金額) を `error_log` / `console.log` しない
- 画面上部の赤いリボンで Shadow Mode (Read-Only) であることを明示
