# bin/

CLI ツール置き場.

## compare-report.php — 現新比較レポート

本番DBスナップショットと新ドメインの集計値を並べた Markdown レポートを生成.

### セットアップ (一度だけ)

```bash
# 1. ダンプ展開 (この .sql は .gitignore で除外されている)
unzip -o db20260207.sql.zip -d tests/Golden/data/

# 2. golden DB を作成
docker compose exec db sh -c "mysql -urucaro -prucaro -e 'CREATE DATABASE IF NOT EXISTS rucaro_golden'"

# 3. ダンプロード (USE rucaro; を rucaro_golden に置換してからロード)
sed 's/USE `rucaro`/USE `rucaro_golden`/' tests/Golden/data/db20260207.sql \
  | docker compose exec -T db mysql -urucaro -prucaro rucaro_golden
```

### レポート生成

```bash
docker compose exec -T -e GOLDEN_DB_HOST=db -e GOLDEN_DB_PORT=3306 \
  app php bin/compare-report.php > report-$(date +%Y%m%d).md
```

### 出力例

```
| 事業体 | 期 | 仕訳件数 | TB借方=貸方 | 本番DB 純利益 | 新ドメイン 純利益 | 差額 |
|---:|---:|---:|:---:|---:|---:|---:|
| 1 | 14 | 208 | ✓ | 632,158 円 | 632,158 円 | 0 円 |
| 1 | 15 | 190 | ✓ | 1,081,242 円 | 1,081,242 円 | 0 円 |
...
```

### 詳細テスト (PHPUnit)

```bash
docker compose exec -T -e GOLDEN_DB_HOST=db -e GOLDEN_DB_PORT=3306 \
  -e GOLDEN_DB_NAME=rucaro_golden -e GOLDEN_DB_USER=rucaro -e GOLDEN_DB_PASS=rucaro \
  app vendor/bin/phpunit --testsuite=golden
```

### 結果の見方

- **TB借方=貸方 ✓** = 新ドメインの試算表で借方合計と貸方合計が一致
- **TB借方=貸方 ✗** = 不一致. 仕訳に登場する科目が `accountingFSJpn` の AccountTree に未定義 (本番DB側の FS 設定不整合)
- **差額 0 円** = 新ドメインで再計算した当期純利益が本番DB保存値と完全一致
- **差額 ≠ 0** = `docs/ai/06_known_issues.md` G-9-1 を参照
