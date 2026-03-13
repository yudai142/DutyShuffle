#!/bin/bash
echo "=== リセット日程テスト ==="

echo -e "\n1. 現在保存されているリセット日程を取得"
curl -s -X POST http://localhost:8080/classes/ajax.php \
  -d "type=get_reset_dates" \
  -H "Content-Type: application/x-www-form-urlencoded" | jq .

echo -e "\n2. 新しいリセット日程を保存"
curl -s -X POST http://localhost:8080/classes/ajax.php \
  -d "type=save_reset_dates&dates=%5B%222026-04-01%22%2C%222026-05-15%22%5D" \
  -H "Content-Type: application/x-www-form-urlencoded" | jq .

echo -e "\n3. 更新後のリセット日程を取得"
curl -s -X POST http://localhost:8080/classes/ajax.php \
  -d "type=get_reset_dates" \
  -H "Content-Type: application/x-www-form-urlencoded" | jq .

echo -e "\n4. データベースで直接確認"
docker-compose exec -T postgres psql -U duty_shuffle -d duty_shuffle -c "SELECT id, reset_date FROM shuffle_option;"
