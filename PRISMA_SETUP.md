# Prisma セットアップガイド（Render.com デプロイ用）

## 📋 概要

このドキュメントは、「Error: Unknown binaryTarget debian-openssl-3.5.x」エラーを解決し、Render.comで正常にデプロイするためのセットアップガイドです。

---

## 🔧 実施済みの修正内容

### 1. package.json の更新
- **旧設定**：`@prisma/client: latest`, `prisma: ^4.6.1` (バージョン不統一)
- **新設定**：`@prisma/client: ^5.20.0`, `prisma: ^5.20.0` (統一・最新安定版)

**理由**：
- Prisma 5.0.0+ で `debian-openssl-3.0.x` サポートが追加
- Render.comの Debian 環境と互換性確保

### 2. prisma/schema.prisma の修正
```prisma
# 旧設定（エラー原因）
binaryTargets = ["native", "debian-openssl-1.1.x", "debian-openssl-3.5.x"]

# 新設定（Render.com環境推奨）
binaryTargets = ["native", "debian-openssl-3.0.x"]
```

**理由**：
- `debian-openssl-3.5.x` は Prisma が未サポート
- `debian-openssl-3.0.x` が Render.comの標準 Debian イメージに対応

### 3. Dockerfile.prod の最適化
- マルチステージビルドで Prisma バイナリを Alpine Linux で最適化
- ランタイムで Debian Slim イメージを使用
- スタートアップスクリプトで Prisma クライアント再生成

### 4. render.yaml の拡張
- `buildCommand` 追加：ビルド時の `npm install` と `prisma generate`
- `preDeployCommand` 追加：デプロイ前の `prisma migrate deploy`
- 環境変数の完全化

---

## 🚀 デプロイ前チェックリスト

### ステップ 1: ローカル環境のセットアップ

```bash
# 1. 依存関係を新バージョンでクリーンインストール
rm -rf node_modules package-lock.json
npm install

# 出力例：
# npm notice created a lockfile as package-lock.json
# added 300 packages, and audited 301 packages
# ✓ Everything looks good!
```

### ステップ 2: Prisma バイナリ生成テスト

```bash
# Prisma クライアント生成
npx prisma generate

# 出力例：
# ✔ Generated Prisma Client (v5.20.0) to ./node_modules/@prisma/client
# ✔ Generated 2 types for generated Prisma Client
```

**エラーが出た場合**：
```bash
# キャッシュをクリアして再試行
rm -rf node_modules/.prisma node_modules/@prisma
npm install
npx prisma generate
```

### ステップ 3: データベース接続テスト

```bash
# .env ファイルにローカル TEST_DATABASE_URL を設定
# 例：
# DATABASE_URL="postgresql://user:password@localhost:5432/duty_shuffle_test"

# Prisma インステンス初期化テスト
node -e "const { PrismaClient } = require('@prisma/client'); const prisma = new PrismaClient(); console.log('✓ Prisma Client initialized'); process.exit(0);"

# 出力例：
# ✓ Prisma Client initialized
```

### ステップ 4: マイグレーション状態確認

```bash
# 現在のマイグレーション状態を確認
npx prisma migrate status

# 出力例：
# 3 migrations found in prisma/migrations
# 
# Following migration have not yet been applied:
# 20240113000000_add_feature_x
# 
# Run npx prisma migrate deploy to apply pending migrations.
```

### ステップ 5: スキーマ同期テスト

```bash
# 開発環境でスキーマを同期（推奨：development な .env を使用）
npx prisma db push --skip-generate

# 出力例：
# ✔ Database synced, created 0 tables
# 
# Everything is now in sync.
```

---

## 🐳 Docker イメージのローカルテスト

### Dockerfile.prod ビルドテスト

```bash
# Docker イメージをビルド
docker build -f Dockerfile.prod -t duty-shuffle:latest .

# ビルド出力例：
# [Stage 1/2] FROM node:18-alpine AS prisma-builder
# [Stage 2/7] FROM php:8.2-apache
# Successfully built abc123def456
# Successfully tagged duty-shuffle:latest
```

### イメージ実行テスト

```bash
# コンテナ内でPrismaが正しく動作するか確認
docker run --rm \
  -e DATABASE_URL="postgresql://user:pass@host:5432/testdb" \
  duty-shuffle:latest \
  /bin/bash -c "npx prisma generate && echo '✓ Prisma initialized successfully'"

# 出力例：
# ✔ Generated Prisma Client (v5.20.0) to ./node_modules/@prisma/client
# ✓ Prisma initialized successfully
```

---

## 📡 Render.com デプロイ

### 前提条件

- ✅ Render アカウントが作成されている
- ✅ PostgreSQL データベースが Render に作成されている
- ✅ GitHub に最新のコードがプッシュされている

### デプロイ手順

1. **Git にコミット・プッシュ**

```bash
git add package.json prisma/schema.prisma Dockerfile.prod render.yaml
git commit -m "fix: Update Prisma to v5.20.0 and fix binaryTarget errors"
git push origin main
```

2. **Render Dashboard で確認**
   - https://dashboard.render.com にログイン
   - DutyShuffle サービスのデプロイログを確認
   - デプロイ前コマンド実行状況を確認

3. **ログを確認**

```bash
# デプロイ実行ステップ：
# 1. Building Docker image...
# 2. Running buildCommand: npm install && npx prisma generate
# 3. Running preDeployCommand: npx prisma migrate deploy
# 4. Starting service...
# 5. ✓ Running at https://duty-shuffle.onrender.com
```

---

## ✅ デプロイ後の動作確認

### 1. ヘルスチェック確認

```bash
curl https://duty-shuffle.onrender.com/
# 200 OK レスポンスが返ることを確認
```

### 2. ログ確認

```bash
# Render Dashboard → Logs から確認：
# - Prisma Client generation successful
# - Migration applied successfully
# - Apache/PHP server started
```

### 3. データベース接続確認

PHP から Prisma Client で接続テスト

```php
<?php
require __DIR__ . '/vendor/autoload.php';

// Prisma Client を Node.js 経由で実行してテスト
exec('node -e "const { PrismaClient } = require(\'@prisma/client\'); const prisma = new PrismaClient(); prisma.$disconnect();"', $output, $exit_code);

if ($exit_code === 0) {
    echo "✓ Database connection OK";
} else {
    echo "✗ Database connection failed";
}
?>
```

---

## 🐛 トラブルシューティング

### エラー: "binaryTarget debian-openssl-3.0.x not found"

**原因**: スキーマと実行環境のバイナリターゲットが一致していない

**解決策**:
```bash
# ローカルで確認
npx prisma generate --verbose

# キャッシュをクリア
rm -rf node_modules/.prisma node_modules/@prisma

# 再インストール
npm install
npx prisma generate
```

### エラー: "Cannot find module '@prisma/client'"

**原因**: npm依存関係が正しくインストールされていない

**解決策**:
```bash
# クリーンインストール
rm -rf node_modules package-lock.json
npm install --legacy-peer-deps

# 確認
npx prisma generate
```

### デプロイ時: "Prisma migration failed"

**原因**: DATABASE_URL が正しく設定されていない

**解決策**:
1. Render Dashboard → Environment Variables で DATABASE_URL を確認
2. 接続文字列フォーマット確認：`postgresql://user:password@host:port/dbname`
3. Render PostgreSQL 作成時に自動生成された接続文字列をコピー

### デプロイ時: "Docker build timeout"

**原因**: npm install が時間がかかっている

**解決策**:
```bash
# ローカルでキャッシュを作成
npm ci --prefer-offline --no-audit

# package-lock.json をコミット
git add package-lock.json
git commit -m "Add package-lock.json for faster installs"
git push
```

---

## 📚 参考資料

- [Prisma Deployment Guide](https://www.prisma.io/docs/orm/prisma-client/deployment/traditional/deploy-to-render)
- [Render.com Prisma ORM Tutorial](https://render.com/docs/deploy-prisma-orm)
- [Prisma System Requirements](https://www.prisma.io/docs/orm/reference/system-requirements)
- [Debian OpenSSL Versions](https://wiki.debian.org/OpenSSL)

---

## ❓ よくある質問

**Q: なぜ `debian-openssl-3.0.x` を選んだのか？**
A: Render.com が使用する Debian イメージに標準搭載されている OpenSSL バージョンです。

**Q: Prisma 4.x で動作させられるか？**
A: いいえ。Prisma 5.0.0+ が必須です。`debian-openssl-3.0.x` のサポートは 5.0.0 以降のみです。

**Q: ローカル環境が macOS/Windows の場合はどうするか？**
A: Docker で Debian コンテナを実行してテストするか、`binaryTargets` に複数のターゲットを指定してください：
```prisma
binaryTargets = ["native", "debian-openssl-3.0.x", "darwin", "windows"]
```

---

## 📞 サポート

問題が解決しない場合：

1. [Prisma GitHub Issues](https://github.com/prismaio/prisma/issues) で同様の問題を検索
2. Render.com Community Discord で質問
3. このドキュメントの最後のログをすべて添付して報告

---

**最終更新**: 2026年3月13日
**Prisma バージョン**: 5.20.0
**Node.js**: 18.x
**Docker**: php:8.2-apache, node:18-alpine
