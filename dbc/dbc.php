<?php
/**
 * Database Connection Manager with Connection Pooling
 * Supports pgBouncer connection pooling for optimized Render.com + Neon DB deployments
 */
function dbc(){
  static $pdo = null;
  
  // 既存の接続を再利用
  if ($pdo !== null) {
    try {
      // 接続確認（軽量なpingコマンド）
      $pdo->query('SELECT 1');
      return $pdo;
    } catch (PDOException $e) {
      // 接続が切れていた場合はnullにしてリトライ
      $pdo = null;
    }
  }
  
  try {
    $databaseUrl = getenv('DATABASE_URL');
    
    // DATABASE_URLから接続情報を取得
    if ($databaseUrl) {
      $url = parse_url($databaseUrl);
      $host = $url['host'];
      $port = $url['port'] ?? 5432;
      $db = ltrim($url['path'], '/');
      $user = $url['user'];
      $pass = $url['pass'] ?? '';
      
      // pgBouncer接続試行（Neon環境）
      // pgBouncerが利用可能な場合、pooler.neon.techで接続
      $pgbouncerHost = $host;
      if (strpos($host, 'neon.tech') !== false) {
        // pgBouncer用のホスト名に変更（例：ep-xxxxx.neon.tech → ep-xxxxx-pooler.neon.tech）
        $pgbouncerHost = preg_replace('/\.neon\.tech$/', '-pooler.neon.tech', $host);
        $pgbouncerPort = 6432; // pgBouncer デフォルトポート
      } else {
        $pgbouncerPort = $port;
      }
      
      $dsn = "pgsql:host=$pgbouncerHost;port=$pgbouncerPort;dbname=$db;connect_timeout=10";
    } else {
      // フォールバック: 環境変数から
      $host = getenv('DB_HOST') ?: 'localhost';
      $port = getenv('DB_PORT') ?: 5432;
      $db = getenv('DB_NAME') ?: 'duty_shuffle';
      $user = getenv('DB_USER') ?: 'postgres';
      $pass = getenv('DB_PASS') ?: '';
      $dsn = "pgsql:host=$host;port=$port;dbname=$db;connect_timeout=10";
    }
    
    $pdo = new PDO($dsn, $user, $pass, [
      // 重要: エラーモード
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      // デフォルトFETCHモード
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      // 接続プーリング: 永続的接続
      PDO::ATTR_PERSISTENT => true,
      // ステートメント準備のデフォルトモード（クライアント側準備で高速化）
      PDO::ATTR_EMULATE_PREPARES => false,
      // タイムアウト設定（秒単位）
      PDO::ATTR_TIMEOUT => 30,
    ]);
    
    // 接続成功時のセットアップ
    // PostgreSQL固有の最適化設定
    $pdo->exec("SET statement_timeout = 30000");  // 30秒でクエリタイムアウト
    $pdo->exec("SET lock_timeout = 10000");        // 10秒でロックタイムアウト
    
    return $pdo;
  } catch(PDOException $e) {
    exit('DB接続エラー: ' . $e->getMessage());
  }
}
?>
