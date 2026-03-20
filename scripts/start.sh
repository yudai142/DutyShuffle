#!/bin/bash
set -e

echo "🚀 Starting application..."

# Fly.io環境でのDATABASE_URL確認
if [ -z "$DATABASE_URL" ]; then
  echo "⚠️ DATABASE_URL not set, using default..."
  export DATABASE_URL="postgresql://duty_shuffle:duty_shuffle@postgres:5432/duty_shuffle"
fi

# Prismaスキーマをデータベースに同期（マイグレーション）
echo "📊 Running Prisma migrations..."
npx prisma migrate deploy 2>&1 || true

# Prisma Client生成
echo "🔨 Generating Prisma Client..."
npx prisma generate 2>&1 || true

# 本番環境で seedは実行しない
if [ "$NODE_ENV" != "production" ]; then
  # シードデータ実行（詳細ログを表示）
  echo "🌱 Running Prisma seed..."
  npx prisma db seed 2>&1 || true
  
  # Prisma Studio起動（dev環境のみ）
  echo "📊 Starting Prisma Studio..."
  npx prisma studio &
  STUDIO_PID=$!
  sleep 2
  echo "✨ Prisma Studio is running at http://localhost:5555"
fi

# PHPサーバー起動
echo "✅ Starting PHP Apache server..."
exec apache2-foreground
