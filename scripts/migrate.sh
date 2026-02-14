#!/bin/bash

# Render.com PostgreSQL マイグレーションスクリプト
# このスクリプトを実行してスキーマを作成します

echo "Prisma マイグレーション実行中..."

# 既存のマイグレーションディレクトリが無い場合は初期化
if [ ! -d "prisma/migrations" ]; then
  echo "マイグレーションディレクトリを初期化中..."
  npx prisma migrate dev --name init --skip-generate
else
  echo "既存のマイグレーションをデプロイ中..."
  npx prisma migrate deploy --preview-feature
fi

echo "マイグレーション完了！"
echo ""
echo "データベース確認:"
npx prisma studio &
