FROM php:8.2-apache

# PDO MySQL・mysqli拡張機能をインストール
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Apacheのmod_rewriteを有効化（.htaccess使用時）
RUN a2enmod rewrite