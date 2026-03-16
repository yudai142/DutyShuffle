<?php
/**
 * Database Connection Manager with Connection Pooling & Fallback
 * Supports pgBouncer connection pooling for Neon DB with automatic fallback
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
      
      // Neon環境の検出
      $isNeon = strpos($host, 'neon.tech') !== false;
      
      // 接続試行リスト（フォールバック対応）
      $connectionAttempts = array();
      
      // 1. pgBouncer接続を試みる（Neon環境のみ）
      if ($isNeon && getenv('APP_ENV') === 'production') {
        $pgbouncerHost = preg_replace('/\.neon\.tech$/', '-pooler.neon.tech', $host);
        $connectionAttempts[] = array(
          'host' => $pgbouncerHost,
          'port' => 6432,
          'description' => 'pgBouncer pooler'
        );
      }
      
      // 2. 直接Neon接続（フォールバック）
      $connectionAttempts[] = array(
        'host' => $host,
        'port' => $port,
        'description' => 'Direct Neon connection'
      );
      
      $lastError = null;
      
      foreach ($connectionAttempts as $attempt) {
        try {
          // SSL設定を含むDSN生成
          $dsn = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;connect_timeout=5;sslmode=require",
            $attempt['host'],
            $attempt['port'],
            $db
          );
          
          $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // 永続接続を無効にして接続の安定性を優先
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 30,
          ]);
          
          // 接続成功時のセットアップ
          $pdo->exec("SET statement_timeout = 30000");
          $pdo->exec("SET lock_timeout = 10000");
          
          return $pdo;
          
        } catch (PDOException $e) {
          $lastError = $e;
          // 次の接続試行へ
          continue;
        }
      }
      
      // 全ての接続試行が失敗
      throw $lastError ?? new Exception('No connection attempts available');
      
    } else {
      // フォールバック: 環境変数から個別設定
      $host = getenv('DB_HOST') ?: 'localhost';
      $port = getenv('DB_PORT') ?: 5432;
      $db = getenv('DB_NAME') ?: 'duty_shuffle';
      $user = getenv('DB_USER') ?: 'postgres';
      $pass = getenv('DB_PASS') ?: '';
      
      $dsn = "pgsql:host=$host;port=$port;dbname=$db;connect_timeout=5;sslmode=prefer";
      
      $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_PERSISTENT => false,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 30,
      ]);
      
      $pdo->exec("SET statement_timeout = 30000");
      $pdo->exec("SET lock_timeout = 10000");
      
      return $pdo;
    }
    
  } catch(PDOException $e) {
    $debug = getenv('APP_DEBUG') === 'true';
    $errorMsg = 'Database connection failed';
    
    if ($debug) {
      $errorMsg .= ': ' . $e->getMessage();
      error_log('DB Connection Error: ' . $e->getMessage());
    }
    
    exit($errorMsg);
  }
}
?>
