#!/bin/bash
set -e

echo "🚀 Starting application..."

# .env ファイル生成（コンテナ内用）
if [ ! -f ".env" ] || ! grep -q "postgres:5432" .env; then
  echo "🔧 Creating .env for container..."
  cat > .env << EOF
DATABASE_URL="postgresql://duty_shuffle:duty_shuffle@postgres:5432/duty_shuffle"
EOF
fi

# Prismaスキーマをデータベースに同期（マイグレーション不要）
echo "📊 Syncing database schema..."
npx prisma db push --skip-generate 2>&1 || true

# Prisma Client生成
echo "🔨 Generating Prisma Client..."
npx prisma generate 2>&1 || true

# シードデータ実行（詳細ログを表示）
echo "🌱 Running Prisma seed..."
npx prisma db seed 2>&1

# Prisma Studio起動（バックグラウンド）
echo "📊 Starting Prisma Studio..."
npx prisma studio &
STUDIO_PID=$!
sleep 2
echo "✨ Prisma Studio is running at http://localhost:5555"

# PHPサーバー起動
echo "✅ Starting PHP Apache server..."
apache2-foreground
