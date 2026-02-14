<?php
$filename = '../env.php';
$env = getenv();
if (file_exists($filename)){
  //For Locale
  require_once '../env.php';
  // ini_set('display_errors', true);
  function dbc(){
    $host = DB_HOST;
    $db = DB_NAME;
    $user = DB_USER;
    $pass = DB_PASS;
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    try {
      $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
      ]);
      // echo 'Mysql接続に成功しました';
      // echo '<br>';
      return $pdo;
    } catch(PDOException $e) {
      exit($e->getMessage());
    }
  }
  // echo "接続テスト"
  // echo connect();
}
// else if($env){
//   //For Docker
//   require_once '../env.php';
//   // ini_set('display_errors', true);
//   function dbc(){
//     $host = getenv('DB_HOST');
//     $db = getenv('DB_NAME');
//     $user = getenv('DB_USER');
//     $pass = getenv('DB_PASSWORD');
//     $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
//     try {
//       $pdo = new PDO($dsn, $user, $pass, [
//         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
//       ]);
//       // echo 'Mysql接続に成功しました';
//       // echo '<br>';
//       return $pdo;
//     } catch(PDOException $e) {
//       exit($e->getMessage());
//     }
//   }
//   // echo "接続テスト"
//   // echo connect();
// }
else{
  //For Render.com PostgreSQL
  function dbc(){
    try {
      $databaseUrl = getenv('DATABASE_URL');
      if ($databaseUrl) {
        // DATABASE_URL形式: postgresql://user:password@host:port/database
        $url = parse_url($databaseUrl);
        $host = $url['host'];
        $port = $url['port'] ?? 5432;
        $db = ltrim($url['path'], '/');
        $user = $url['user'];
        $pass = $url['pass'];
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
}
?>
