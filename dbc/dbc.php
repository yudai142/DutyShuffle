<?php
function dbc(){
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
      $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    } else {
      // フォールバック: 環境変数から
      $host = getenv('DB_HOST') ?: 'localhost';
      $port = getenv('DB_PORT') ?: 5432;
      $db = getenv('DB_NAME') ?: 'duty_shuffle';
      $user = getenv('DB_USER') ?: 'postgres';
      $pass = getenv('DB_PASS') ?: '';
      $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    }
    
    $pdo = new PDO($dsn, $user, $pass, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    return $pdo;
  } catch(PDOException $e) {
    exit($e->getMessage());
  }
}
?>
