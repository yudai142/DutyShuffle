# Render.com デプロイガイド

## 前提条件
- GitHub アカウント
- Render.com アカウント
- Git がインストール済み

## デプロイ手順

### 1. リポジトリの準備
```bash
cd /Users/yudai/Desktop/DutyShuffle
git add .
git commit -m "Render.com deployment setup"
git push origin main
```

### 2. Render.com で PostgreSQL を作成
1. [Render.com ダッシュボード](https://dashboard.render.com) にアクセス
2. **New +** → **PostgreSQL** を選択
3. 以下を設定：
   - **Name**: `duty-shuffle-db`
   - **Database**: `duty_shuffle`
   - **Region**: `Oregon` または最寄りの地域
   - **Plan**: Free
4. **Create Database** をクリック
5. **Connection** タブから **External Database URL** をコピー
   - 形式: `postgresql://user:password@hostname:port/dbname`

### 3. Render.com で Web Service を作成
1. **New +** → **Web Service** を選択
2. GitHub リポジトリを選択
3. 以下を設定：
   - **Name**: `duty-shuffle`
   - **Runtime**: `Docker`
   - **Build Command**: (空で可)
   - **Start Command**: `/usr/local/bin/start.sh`
   - **Dockerfile**: `Dockerfile.prod`

### 4. 環境変数を設定
**Environment** セクションで以下を追加：

```
DATABASE_URL = [Step 2 でコピーした PostgreSQL URL]
APP_ENV = production
APP_DEBUG = false
```

例：
```
DATABASE_URL = postgresql://user:mypassword@postgresql-123.render.com:5432/duty_shuffle
APP_ENV = production
APP_DEBUG = false
```

### 5. デプロイ実行
1. **Deploy** をクリック
2. デプロイログを確認（3-5分待機）

### 6. マイグレーション実行
デプロイ完了後、以下のいずれかの方法でマイグレーション実行：

**方法1: Render Shell を使用（推奨）**
```bash
# Render ダッシュボード → Service → Shell を開く
npx prisma migrate deploy --preview-feature
```

**方法2: ローカルから実行**
```bash
# ローカル環境で実行
export DATABASE_URL="postgresql://user:password@hostname:port/dbname"
npx prisma migrate deploy --preview-feature
```

### 7. アプリケーションアクセス
- デプロイ後、自動的に URL が生成されます
- 例: `https://duty-shuffle.onrender.com`

## トラブルシューティング

### エラー: `Connection refused`
- PostgreSQL の **External Database URL** が正しく設定されているか確認
- Render で PostgreSQL が起動しているか確認

### エラー: `permission denied` (Dockerfile)
```bash
chmod +x scripts/migrate.sh
git add scripts/migrate.sh
git push origin main
```

### ログの確認
Render ダッシュボード → Service → **Logs** タブで確認

### データベースリセット
```bash
npx prisma migrate reset  # ⚠️ 本番環境では実行禁止！
```

## ローカル開発環境での動作確認
```bash
# MySQL で開発
docker-compose up -d
npm install
npx prisma generate

# または PostgreSQL でテスト
export DATABASE_URL="postgresql://user:password@localhost:5432/duty_shuffle"
npx prisma migrate deploy --preview-feature
```

## 主な変更点

1. **Dockerfile.prod**: 本番環境向けのマルチステージビルド
2. **Prisma スキーマ**: MySQL → PostgreSQL に切り替え
3. **dbc.php**: PostgreSQL 接続対応
4. **render.yaml**: Render デプロイ設定

## 注意事項

- Free プランは月30時間の制限あり
- PostgreSQL Free プランはストレージ制限あり
- メール認証が必要な場合あり
- 本番環境ではより高いプランの使用を推奨
