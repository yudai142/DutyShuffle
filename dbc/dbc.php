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
}else if($env){
  //For Docker
  require_once '../env.php';
  // ini_set('display_errors', true);
  function dbc(){
    $host = getenv('DB_HOST');
    $db = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASSWORD');
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
}else{
  //For Heroku
  function dbc(){
    try {
      $url = parse_url(getenv('CLEARDB_DATABASE_URL'));
      $dsn = sprintf('mysql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1),";charset=utf8mb4");
      $pdo = new PDO($dsn, $url['user'], $url['pass'], [
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
