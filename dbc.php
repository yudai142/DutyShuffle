<?php
$filename = './env.php';
if (!file_exists($filename)){
  //For Heroku
  function connect(){
    try {
      $url = parse_url(getenv('DATABASE_URL'));
      $dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1));
      $pdo = new PDO($dsn, $url['user'], $url['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
      ]);
      return $pdo;
    } catch(PDOException $e) {
      exit($e->getMessage());
    }
  }
}else{
  //For Locale
  require_once 'env.php';
  // ini_set('display_errors', true);
  function connect(){
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
?>
