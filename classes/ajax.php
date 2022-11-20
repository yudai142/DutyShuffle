<?php
require_once("../dbc/dbc.php");
header('Content-Type: application/json; charset=utf-8');

try{
  switch($_REQUEST['type']){
    case 'work_list':
      $sql = "SELECT * FROM work";
      $stmt = dbc()->prepare($sql);
      $stmt->execute();
      // あらかじめ配列$productListを作成する
      // 受け取ったデータを配列に代入する
      // 最終的にhtmlへ渡される
      $productList = array();
      // fetchメソッドでSQLの結果を取得
      // 定数をPDO::FETCH_ASSOC:に指定すると連想配列で結果を取得できる
      // 取得したデータを$productListへ代入する
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
          $productList[] = array(
              'id'    => $row['id'],
              'name'  => $row['name'],
              'multiple' => $row['multiple'],
              'archive' => $row['archive']
          );
      }
      echo json_encode($productList);
      exit;
    case 'work_add':
      $sql = "INSERT INTO work(name, multiple, archive) VALUES(?, ?, ?)";
      $dbc = dbc();
      $stmt = $dbc->prepare($sql);
      $stmt->execute(array($_POST['name'], $_POST['multiple'], $_POST['archive']));
      // 先ほど追加したデータを取得
      // idはlastInsertId()で取得できる
      $last_id = $dbc->lastInsertId();
      $sql = "SELECT id, name, multiple, archive FROM work WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      $stmt->execute(array($last_id));

      $productList = array();
      // fetchメソッドでSQLの結果を取得
      // 定数をPDO::FETCH_ASSOC:に指定すると連想配列で結果を取得できる
      // 取得したデータを$productListへ代入する
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
          $productList[] = array(
              'id'    => $row['id'],
              'name'    => $row['name'],
              'multiple'  => $row['multiple'],
              'archive' => $row['archive']
          );
      }
      echo json_encode($productList);
      exit;
  };
}catch(PDOException $e){
  exit($e->getMessage());
}
