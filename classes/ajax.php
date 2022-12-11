<?php
require_once("../dbc/dbc.php");
header('Content-Type: application/json; charset=utf-8');


try{
  switch($_REQUEST['type']){
    case 'work_select_list':
      $sql = "SELECT work_id FROM history WHERE id=?";
      $sql2 = "SELECT id, name, archive FROM work";
      
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($_REQUEST["history_id"])))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }

      if (!($stmt2 = dbc()->query($sql2))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      
      $history = $stmt->fetch(PDO::FETCH_ASSOC);
      foreach($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if($row['id'] != $history['work_id'] && $row['archive'] == 0){
          $productList[] = array(
            'id'    => $row['id'],
            'name'  => $row['name']
          );
        }
      }
      if($history['work_id'] != null){
        $productList[] = array(
          'id'    => 0,
          'name'  => "サポート"
        );
      }
      
      echo json_encode($productList);
      exit;
    case 'work_select_definition':
      if(isset($_REQUEST["select_work"], $_REQUEST["history_id"])){
        if($_REQUEST["select_work"] == "0"){$_REQUEST["select_work"] = null;}
        $sql = "UPDATE history SET work_id=? WHERE id=?";
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute(array($_REQUEST["select_work"], $_REQUEST["history_id"])))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
      }else{
        echo json_encode(array("err" => "入力情報が不正です"));
        exit;
      }
      echo json_encode("work");
      exit;
    case 'allocation_list':
      $date = date('Y-m-d',  strtotime($_REQUEST['day']));
      
      $sql = "SELECT history.id,last_name, first_name ,work_id FROM history, member WHERE day=? AND member.id = history.member_id";
      $sql2 = "SELECT id, name, archive FROM work";
      
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($date)))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }

      if (!($stmt2 = dbc()->query($sql2))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      
      $work_list = $stmt2->fetchAll(PDO::FETCH_ASSOC);
      $work_id_list = [];
      
      foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if($row['work_id'] != null){
          $work_id_list[] = $row['work_id'];
        }
        $productList[1][] = array(
          'history_id'    => $row['id'],
          'last_name' => $row['last_name'],
          'first_name'  => $row['first_name'],
          'work_id'    => $row['work_id'],
        );
      }
      $work_id_list = array_unique($work_id_list);

      foreach($work_list as $row) {
        if(in_array($row['id'], $work_id_list) || $row['archive'] == 0){
          $productList[0][] = array(
            'id'    => $row['id'],
            'name'  => $row['name']
          );
        }
      }
      
      echo json_encode($productList);
      exit;
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
      $sql = "SELECT id, last_name, first_name, archive FROM member ORDER BY kana_name ASC";
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
        if(in_array($row['id'], $checked) || $row['archive'] == 0){
          $productList[] = array(
            'id'    => $row['id'],
            'last_name'  => $row['last_name'],
            'first_name' => $row['first_name'],
            'checked' => (in_array($row['id'], $checked)) ? "checked" : ""
          );
        }
      }
      echo json_encode($productList);
      exit;
    case 'member_select_check':
      $date = date('Y-m-d',  strtotime($_REQUEST['day']));
      $select = $_REQUEST['select'];
      $sql = "SELECT member_id work_id last_name first_name FROM history, member WHERE day=? AND member.id = history.member_id group by member_id ORDER BY kana_name ASC";
      $stmt = dbc()->prepare($sql);
      $stmt->execute(array($date));
      $productList = [];
      foreach( $stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if($row['work_id'] != null && !in_array($row['id'], $select)){
          $productList[] = array(
            'id'    => $row['member_id'],
            'last_name' => $row['last_name'],
            'first_name'  => $row['first_name'],
          );
        }
      }
      if(count($productList) == 0){$productList[] = array("err" => "OK");}
      echo json_encode($productList);
      exit;
    case 'member_select_definition':
      $date = date('Y-m-d',  strtotime($_REQUEST['day']));
      $select = ($_REQUEST['select']) ? $_REQUEST['select'] : [];
      $sql = "SELECT member_id FROM history WHERE day=? group by member_id";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($date)))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $selected = [];
      foreach( $stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $selected[] = $row['member_id'];
      }
      $selected_result = array_diff($selected, $select);
      $select_result = array_diff($select, $selected);
      
      foreach($selected_result as $row) {
        $sql = "DELETE FROM history WHERE day=? AND member_id=?";
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute(array($date, $row)))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
      }
      
      foreach($select_result as $row) {
        $sql = "INSERT INTO history(day, member_id) VALUES(?, ?)";
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute(array($date, $row)))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
      }
      echo json_encode("select");
      exit;
    case 'member_select_work':
      $date = date('Y-m-d',  strtotime($_REQUEST['day']));
      
      $sql2 = "SELECT id, name FROM work";
      $stmt2 = dbc()->prepare($sql2);
      if (!($stmt2->execute(array($_REQUEST['work_id'])))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $work_list = $stmt2->fetchAll(PDO::FETCH_ASSOC);
      $productList[0] = $work_list[array_search($_REQUEST['work_id'], array_column($work_list, 'id'))];
      
      $sql = "SELECT history.id, work_id, last_name, first_name FROM history, member WHERE day=? AND member.id = history.member_id ORDER BY kana_name ASC";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($date)))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      foreach( $stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if($row['work_id'] != null){
          $row['work_id'] = $work_list[array_search($row['work_id'], array_column($work_list, 'id'))]["name"];
        }
        $productList[1][] = array(
          'history_id'    => $row['id'],
          'last_name' => $row['last_name'],
          'first_name'  => $row['first_name'],
          'work_name'    => $row['work_id'],
        );
      }
      echo json_encode($productList);
      exit;
    case 'member_select_work_definition':
      $date = date('Y-m-d',  strtotime($_REQUEST['day']));
      $select = ($_REQUEST['select']) ? $_REQUEST['select'] : [];
      $sql = "SELECT id, member_id, work_id FROM history WHERE day=?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($date)))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $selected = [];
      $histories = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach( $histories as $row) {
        if($row["work_id"] == $_REQUEST['work_id']){
          $selected[] = $row['id'];
        }
      }
      
      $selected_result = array_diff($selected, $select);
      $select_result = array_diff($select, $selected);
      
      foreach($selected_result as $row) {
        $sql = "UPDATE history SET work_id=null WHERE id=?";
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute(array($row)))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
      }
      
      foreach($select_result as $row) {
        $sql = "INSERT INTO history(day, member_id) VALUES(?, ?)";
        $sql = "UPDATE history SET work_id=? WHERE id=?";
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute(array($_REQUEST['work_id'], $row)))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
      }
      echo json_encode("work");
      exit;
    case 'member_list':
      if($_REQUEST['select'] == "1"){
        $sql = "SELECT * FROM member WHERE archive = 0";
      }else if($_REQUEST['select'] == "2"){
        $sql = "SELECT * FROM member WHERE archive = 1";
      }else{
        $sql = "SELECT * FROM member";
      }
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
      if($_REQUEST['select'] == "1"){
        $sql = "SELECT * FROM work WHERE archive = 0";
      }else if($_REQUEST['select'] == "2"){
        $sql = "SELECT * FROM work WHERE archive = 1";
      }else{
        $sql = "SELECT * FROM work";
      }
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
