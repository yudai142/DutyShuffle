FROM php:8.2-apache

# PostgreSQL開発ファイルをインストール
RUN apt-get update && apt-get install -y libpq-dev && rm -rf /var/lib/apt/lists/*

# PDO PostgreSQL拡張機能をインストール
RUN docker-php-ext-install pdo pdo_pgsql

# Node.js 18 インストール
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    rm -rf /var/lib/apt/lists/*

# Apacheのmod_rewriteを有効化（.htaccess使用時）
RUN a2enmod rewrite

# ワークディレクトリを設定
WORKDIR /var/www/html

# 依存関係をインストール
COPY package*.json ./
RUN npm install || true

# スタートアップスクリプトをコピー
COPY scripts/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# エントリーポイント設定
ENTRYPOINT ["/usr/local/bin/start.sh"]