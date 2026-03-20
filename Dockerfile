FROM php:8.2-apache

# PostgreSQL開発ファイルをインストール
RUN apt-get update && apt-get install -y libpq-dev curl && rm -rf /var/lib/apt/lists/*

# PDO PostgreSQL拡張機能をインストール
RUN docker-php-ext-install pdo pdo_pgsql

# Node.js 18 インストール
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    rm -rf /var/lib/apt/lists/*

# Apacheのmod_rewriteとmod_headersを有効化
RUN a2enmod rewrite headers

# ワークディレクトリを設定
WORKDIR /var/www/html

# すべてのソースコードをコピー
COPY . .

# Apache設定ファイルをコピー
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# デフォルトサイトを有効化
RUN a2ensite 000-default

# 依存関係をインストール
RUN npm install || true

# スタートアップスクリプトをコピー
COPY scripts/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Apacheのポート設定
ENV PORT=80
EXPOSE 80

# ヘルスチェック設定
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/top.php || exit 1

# エントリーポイント設定
ENTRYPOINT ["/usr/local/bin/start.sh"]