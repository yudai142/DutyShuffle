<?php
require_once("../dbc/dbc.php");
header('Content-Type: application/json; charset=utf-8');


try{
  switch($_REQUEST['type']){
    case 'join_member':
      $date = date('Y-m-d',  strtotime($_REQUEST['day']));
      $sql = "SELECT member.id ,last_name, first_name FROM history, member WHERE day=? AND member.id = history.member_id group by member_id ORDER BY member.kana_name ASC";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($date)))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $productList[] = array(
          'id'    => $row['id'],
          'last_name' => $row['last_name'],
          'first_name'  => $row['first_name'],
        );
      }
      echo json_encode($productList);
      exit;
    case 'join_work':
      $sql = "SELECT id, name FROM work WHERE archive=0";
      if (!($stmt = dbc()->query($sql))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      foreach($stmt as $row) {
        $productList[] = array(
          'id'    => $row['id'],
          'name'  => $row['name']
        );
      }
      echo json_encode($productList);
      exit;
    case 'member_select_list':
      $date = date('Y-m-d',  strtotime($_REQUEST['day']));
      $sql = "SELECT id, last_name, first_name FROM member WHERE archive=0 ORDER BY kana_name ASC";
      $sql2 = "SELECT member_id FROM history WHERE day=? group by member_id";
      $checked = [];
      
      if (!($stmt = dbc()->query($sql))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $stmt2 = dbc()->prepare($sql2);
      if (!($stmt2->execute((array($date))))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      foreach( $stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $checked[] = $row['member_id'];
      }
      foreach($stmt as $row) {
        $productList[] = array(
          'id'    => $row['id'],
          'last_name'  => $row['last_name'],
          'first_name' => $row['first_name'],
          'checked' => (in_array($row['id'], $checked)) ? "checked" : ""
        );
      }
      echo json_encode($productList);
      exit;
    case 'member_select_definition':
      $date = date('Y-m-d',  strtotime($_REQUEST['day']));
      $select = $_REQUEST['select'];
      $sql = "SELECT member_id FROM history WHERE day=? group by member_id";
      $stmt = dbc()->prepare($sql);
      $stmt->execute(array($date));
      $selected = [];
      foreach( $stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $selected[] = $row['member_id'];
      }
      $selected_result = array_diff($selected, $select);
      $select_result = array_diff($select, $selected);
      // $productList[] = $select;
      
      foreach($selected_result as $row) {
        $sql = "DELETE FROM history WHERE day=? AND member_id=?";
        $stmt = dbc()->prepare($sql);
        $stmt->execute(array($date, $row));
      }
      
      foreach($select_result as $row) {
        $sql = "INSERT INTO history(day, member_id) VALUES(?, ?)";
        $stmt = dbc()->prepare($sql);
        $stmt->execute(array($date, $row));
      }
      
      $sql2 = "SELECT history.member_id ,last_name, first_name FROM history, member WHERE day=? AND member.id = history.member_id group by member.id ORDER BY member.kana_name ASC";
      $stmt2 = dbc()->prepare($sql2);
      $stmt2->execute(array($date));
      foreach($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $productList[] = array(
          'id'    => $row['member_id'],
          'last_name' => $row['last_name'],
          'first_name'  => $row['first_name'],
        );
      }
      echo json_encode($productList);
      exit;
    case 'member_list':
      $sql = "SELECT * FROM member";
      if (!($stmt = dbc()->query($sql))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      foreach($stmt as $row) {
        $productList[] = array(
          'id'    => $row['id'],
          'last_name'  => $row['last_name'],
          'first_name' => $row['first_name']
        );
      }
      echo json_encode($productList);
      exit;
    case 'work_list':
      $sql = "SELECT * FROM work";
      if (!($stmt = dbc()->query($sql))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      foreach($stmt as $row) {
        $productList[] = array(
          'id'    => $row['id'],
          'name'  => $row['name']
        );
      }
      echo json_encode($productList);
      exit;
    case 'member_add':
      if(!isset($_POST['last_name']) || empty($_POST['last_name'])) $err[] = "姓";
      if(!isset($_POST['first_name']) || empty($_POST['first_name'])) $err[] = "名";
      if(!isset($_POST['kana_name']) || empty($_POST['kana_name']) || !(preg_match("/^[ぁ-んー]+$/u", $_POST['kana_name']))) $err[] = "ふりがな";
      if(count($err) != 0) {
        echo json_encode(array("err" => implode('と', $err)."が不正です"));
        exit;
      }
      $sql = "INSERT INTO member(last_name, first_name, kana_name, archive) VALUES(?, ?, ?, ?)";
      $dbc = dbc();
      $stmt = $dbc->prepare($sql);
      if (!($stmt->execute(array($_POST['last_name'], $_POST['first_name'], $_POST['kana_name'], $_POST['archive'])))) {
        echo json_encode(array("err" => "データが正しく保存されませんでした"));
        exit;
      }
      $last_id = $dbc->lastInsertId();
      $sql = "SELECT id, last_name, first_name, kana_name, archive FROM member WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($last_id)))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $productList[] = array(
        'id'    => $row['id'],
        'last_name'  => $row['last_name'],
        'first_name' => $row['first_name'],
        'kana_name' => $row['kana_name'],
        'archive' => $row['archive']
      );
      echo json_encode($productList);
      exit;
    case 'work_add':
      $err = [];
      if(!isset($_POST['name']) || empty($_POST['name'])) $err[] = "名前";
      if(!isset($_POST['multiple']) || empty($_POST['multiple']) || $_POST['multiple'] <= 0) $err[] = "参加人数";
      if(count($err) != 0) {
        echo json_encode(array("err" => implode('と', $err)."が不正です"));
        exit;
      }
      $sql = "INSERT INTO work(name, multiple, archive) VALUES(?, ?, ?)";
      $dbc = dbc();
      $stmt = $dbc->prepare($sql);
      if (!($stmt->execute(array($_POST['name'], $_POST['multiple'], $_POST['archive'])))) {
        echo json_encode(array("err" => "データが正しく保存されませんでした"));
        exit;
      }
      $last_id = $dbc->lastInsertId();
      $sql = "SELECT id, name, multiple, archive FROM work WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($last_id)))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $productList[] = array(
        'id'    => $row['id'],
        'name'    => $row['name'],
        'multiple'  => $row['multiple'],
        'archive' => $row['archive']
      );
      echo json_encode($productList);
      exit;
    case 'member_edit':
      $sql = "SELECT id, last_name, first_name, kana_name, archive FROM member WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($_REQUEST['id'])))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $productList[] = array(
        'id'    => $row['id'],
        'last_name'  => $row['last_name'],
        'first_name' => $row['first_name'],
        'kana_name' => $row['kana_name'],
        'archive' => $row['archive']
      );
      echo json_encode($productList);
      exit;
    case 'work_edit':
      $sql = "SELECT id, name, multiple, archive FROM work WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($_REQUEST['id'])))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $productList[] = array(
        'id'    => $row['id'],
        'name'    => $row['name'],
        'multiple'  => $row['multiple'],
        'archive' => $row['archive']
      );
      echo json_encode($productList);
      exit;
    case 'member_update':
      if(!isset($_POST['last_name']) || empty($_POST['last_name'])) $err[] = "姓";
      if(!isset($_POST['first_name']) || empty($_POST['first_name'])) $err[] = "名";
      if(!isset($_POST['kana_name']) || empty($_POST['kana_name']) || !(preg_match("/^[ぁ-んー]+$/u", $_POST['kana_name']))) $err[] = "ふりがな";
      if(count($err) != 0) {
        echo json_encode(array("err" => implode('と', $err)."が不正です"));
        exit;
      }
      $sql = "UPDATE member SET last_name = ?, first_name = ?, kana_name = ?, archive = ? WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($_POST['last_name'], $_POST['first_name'], $_POST['kana_name'], $_POST['archive'], $_POST['id'])))) {
        echo json_encode(array("err" => "データが正しく更新されませんでした"));
        exit;
      }
      $sql = "SELECT id, last_name, first_name, kana_name, archive FROM member WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($_POST['id'])))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $productList[] = array(
        'id'    => $row['id'],
        'last_name'  => $row['last_name'],
        'first_name' => $row['first_name'],
        'kana_name' => $row['kana_name'],
        'archive' => $row['archive']
      );
      echo json_encode($productList);
      exit;
    case 'work_update':
      $err = [];
      if(!isset($_POST['name']) || empty($_POST['name'])) $err[] = "名前";
      if(!isset($_POST['multiple']) || empty($_POST['multiple']) || $_POST['multiple'] <= 0) $err[] = "参加人数";
      if(count($err) != 0) {
        echo json_encode(array("err" => implode('と', $err)."が不正です"));
        exit;
      }
      $sql = "UPDATE work SET name = ?, multiple = ?, archive = ? WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($_POST['name'], $_POST['multiple'], $_POST['archive'], $_POST['id'])))) {
        echo json_encode(array("err" => "データが正しく更新されませんでした"));
        exit;
      }
      $sql = "SELECT id, name, multiple, archive FROM work WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($_POST['id'])))){
        echo json_encode(array("err" => "データの取得に失敗しました"));
        exit;
      }
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $productList[] = array(
        'id'    => $row['id'],
        'name'    => $row['name'],
        'multiple'  => $row['multiple'],
        'archive' => $row['archive']
      );
      echo json_encode($productList);
      exit;
  };
}catch(PDOException $e){
  exit($e->getMessage());
}
