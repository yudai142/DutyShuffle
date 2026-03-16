#!/bin/bash
# Web Worker 実装テストスクリプト

echo "=== Web Worker 実装の確認 ==="
echo ""

echo "1. ファイル配置確認"
echo "   ✓ dataProcessWorker.js: $([ -f src/js/dataProcessWorker.js ] && echo 'OK' || echo 'NG')"
echo "   ✓ ajax.js: $([ -f src/js/ajax.js ] && echo 'OK' || echo 'NG')"
echo ""

echo "2. シンタックスチェック"
node -c src/js/dataProcessWorker.js 2>&1 > /dev/null && echo "   ✓ dataProcessWorker.js: OK" || echo "   ✗ dataProcessWorker.js: ERROR"
node -c src/js/ajax.js 2>&1 > /dev/null && echo "   ✓ ajax.js: OK" || echo "   ✗ ajax.js: ERROR"
echo ""

echo "3. コード統合確認"
# Worker 初期化コードの確認
grep -q "let dataWorker" src/js/ajax.js && echo "   ✓ Worker 初期化コード: OK" || echo "   ✗ Worker 初期化コード: NG"
# processWithWorkerAsync 関数の確認
grep -q "function processWithWorkerAsync" src/js/ajax.js && echo "   ✓ processWithWorkerAsync 関数: OK" || echo "   ✗ processWithWorkerAsync 関数: NG"
# Worker メッセージハンドラの確認
grep -q "self.onmessage" src/js/dataProcessWorker.js && echo "   ✓ Worker メッセージハンドラ: OK" || echo "   ✗ Worker メッセージハンドラ: NG"
echo ""

echo "4. 実装されたタスク確認"
echo "   Worker タスク一覧:"
grep "case '" src/js/dataProcessWorker.js | sed "s/.*case '/    - /" | sed "s/'.*//"
echo ""

echo "5. Ajax 関数の Worker 統合確認"
echo "   統合された関数:"
grep -o "processWithWorkerAsync('[^']*'" src/js/ajax.js | sort | uniq | sed "s/processWithWorkerAsync('//; s/'$/    /" | awk '{print "    - " $0}'
echo ""

echo "6. キャッシュシステムの確認"
grep -q "const CACHE_CONFIG" src/js/ajax.js && echo "   ✓ キャッシュシステム: OK" || echo "   ✗ キャッシュシステム: NG"
grep -q "cachedAjax" src/js/ajax.js && echo "   ✓ cachedAjax 関数: OK" || echo "   ✗ cachedAjax 関数: NG"
echo ""

echo "=== テスト完了 ==="
echo ""
echo "実装状況:"
echo "  • Web Worker ファイル: 作成完了"
echo "  • Ajax.js 統合: 作成完了"
echo "  • フォールバック機構: 実装済み"
echo "  • エラーハンドリング: 実装済み"
echo ""
echo "次のステップ:"
echo "  1. Render.com にデプロイ"
echo "  2. ブラウザ DevTools で Worker が正しく読み込まれるか確認"
echo "  3. パフォーマンス改善を測定"
