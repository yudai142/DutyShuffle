#!/bin/bash
# PostgreSQL に接続して reset_date を確認
docker-compose exec -T postgres psql -U duty_shuffle -d duty_shuffle -c "SELECT id, reset_date FROM shuffle_option;"
