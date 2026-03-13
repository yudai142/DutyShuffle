<?php
require_once("../dbc/dbc.php");
header('Content-Type: application/json; charset=utf-8');


try{
  switch($_REQUEST['type']){
    case 'allocation_list':
      $date = date('Y-m-d',  strtotime($_REQUEST['date']));
      
      $sql = "SELECT history.id,family_name, given_name ,work_id FROM history, member WHERE date=? AND member.id = history.member_id";
      $sql2 = "SELECT id, name, archive FROM work";
      $sql3 = "SELECT work_id FROM off_work WHERE date=?";

      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($date)))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }

      if (!($stmt2 = dbc()->query($sql2))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }

      $stmt3 = dbc()->prepare($sql3);
      if (!($stmt3->execute(array($date)))) {
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
          'family_name' => $row['family_name'],
          'given_name'  => $row['given_name'],
          'work_id'    => $row['work_id'],
        );
      }

      $work_id_list = array_unique($work_id_list);
      $off_works = $stmt3->fetchAll(PDO::FETCH_COLUMN);

      foreach($work_list as $row) {
        if(in_array($row['id'], $work_id_list) || $row['archive'] == false){
          $status = (in_array($row['id'], $off_works))? "0" : "1";
          $productList[0][] = array(
            'id'    => $row['id'],
            'name'  => $row['name'],
            'status'  => $status
          );
        }
      }
      if(!isset($productList)){
        echo json_encode(null);
        exit;
      }
      echo json_encode($productList);
      exit;
    case 'join_member':
      $date = date('Y-m-d', strtotime($_REQUEST['date']));
      $sql = "SELECT DISTINCT ON (member_id) history.id, family_name, given_name, work_id FROM history, member WHERE date=? AND member.id = history.member_id ORDER BY member_id, member.kana_name ASC";
      $sql2 = "SELECT id ,name FROM work";

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
      foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if($row['work_id'] != null){
          $row['work_id'] = $work_list[array_search($row['work_id'], array_column($work_list, 'id'))]["name"];
        }
        $productList[] = array(
          'history_id'    => $row['id'],
          'family_name' => $row['family_name'],
          'given_name'  => $row['given_name'],
          'work_name'  => $row['work_id']
        );
      }
      if(!isset($productList)){
        echo json_encode(null);
        exit;
      }
      echo json_encode($productList);
      exit;
    case 'join_work':
      $date = date('Y-m-d', strtotime($_REQUEST['date']));
      $sql = "SELECT id, name FROM work WHERE archive=false";
      if (!($stmt = dbc()->query($sql))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $sql2 = "SELECT work_id FROM off_work WHERE date=?";
      $stmt2 = dbc()->prepare($sql2);
      if (!($stmt2->execute(array($date)))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $off_works = $stmt2->fetchAll(PDO::FETCH_COLUMN);
      foreach($stmt as $row) {
        $status = (in_array($row['id'], $off_works))? "0" : "1";
        $productList[] = array(
          'id'    => $row['id'],
          'name'  => $row['name'],
          'status' => $status
        );
      }
      if(!isset($productList)){
        echo json_encode(null);
        exit;
      }
      echo json_encode($productList);
      exit;
    case 'member_select_list':
      $date = date('Y-m-d',  strtotime($_REQUEST['date']));
      $sql = "SELECT id, family_name, given_name, archive FROM member ORDER BY kana_name ASC";
      $sql2 = "SELECT member_id FROM history WHERE date=? group by member_id";
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
        if(in_array($row['id'], $checked) || $row['archive'] == false){
          $productList[] = array(
            'id'    => $row['id'],
            'family_name'  => $row['family_name'],
            'given_name' => $row['given_name'],
            'checked' => (in_array($row['id'], $checked)) ? "checked" : ""
          );
        }
      }
      if(!isset($productList)){
        echo json_encode(null);
        exit;
      }
      echo json_encode($productList);
      exit;
    case 'member_select_check':
      $date = date('Y-m-d',  strtotime($_REQUEST['date']));
      $select = (isset($_REQUEST['select'])) ? $_REQUEST['select'] : [];
      $sql = "SELECT DISTINCT ON (member_id) member_id, work_id, family_name, given_name FROM history, member WHERE date=? AND member.id = history.member_id ORDER BY member_id, member.kana_name ASC";
      $stmt = dbc()->prepare($sql);
      $stmt->execute(array($date));
      $productList = [];
      foreach( $stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if($row['work_id'] != null && !in_array($row['id'], $select)){
          $productList[] = array(
            'id'    => $row['member_id'],
            'family_name' => $row['family_name'],
            'given_name'  => $row['given_name'],
          );
        }
      }
      if(count($productList) == 0){$productList[] = array("err" => "OK");}
      echo json_encode($productList);
      exit;
    case 'member_select_definition':
      $date = date('Y-m-d',  strtotime($_REQUEST['date']));
      $select = (isset($_REQUEST['select'])) ? $_REQUEST['select'] : [];
      $sql = "SELECT member_id FROM history WHERE date=? group by member_id";
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
        $sql = "DELETE FROM history WHERE date=? AND member_id=?";
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute(array($date, $row)))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
      }
      
      foreach($select_result as $row) {
        $sql = "INSERT INTO history(date, member_id) VALUES(?, ?)";
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute(array($date, $row)))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
      }
      echo json_encode("select");
      exit;
    case 'member_select_work':
      $date = date('Y-m-d',  strtotime($_REQUEST['date']));
      
      $sql2 = "SELECT id, name FROM work";
      if (!($stmt2 = dbc()->query($sql2))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $sql3 = "SELECT work_id FROM off_work WHERE date=? AND work_id=?";
      $stmt3 = dbc()->prepare($sql3);
      if (!($stmt3->execute(array($date, $_REQUEST['work_id'])))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $work_list = $stmt2->fetchAll(PDO::FETCH_ASSOC);
      $productList[0] = $work_list[array_search($_REQUEST['work_id'], array_column($work_list, 'id'))];
      $productList[0]["status"] = ($stmt3->fetchAll(PDO::FETCH_COLUMN)==null)? "0":"1";
      
      $sql = "SELECT history.id, work_id, family_name, given_name FROM history, member WHERE date=? AND member.id = history.member_id ORDER BY kana_name ASC";
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
          'family_name' => $row['family_name'],
          'given_name'  => $row['given_name'],
          'work_name'    => $row['work_id'],
        );
      }
      if(!isset($productList)){
        echo json_encode(null);
        exit;
      }
      echo json_encode($productList);
      exit;
    case 'member_select_work_definition':
      $date = date('Y-m-d',  strtotime($_REQUEST['date']));
      $select = (isset($_REQUEST['select'])) ? $_REQUEST['select'] : [];
      $sql = "SELECT id, member_id, work_id FROM history WHERE date=?";
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
        $sql = "INSERT INTO history(date, member_id) VALUES(?, ?)";
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
      $select = isset($_REQUEST['select']) ? $_REQUEST['select'] : '0';
      if($select == "1"){
        $sql = "SELECT * FROM member WHERE archive = false ORDER BY kana_name ASC";
      }else if($select == "2"){
        $sql = "SELECT * FROM member WHERE archive = true ORDER BY kana_name ASC";
      }else{
        $sql = "SELECT * FROM member ORDER BY kana_name ASC";
      }
      if (!($stmt = dbc()->query($sql))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $productList = [];
      foreach($stmt as $row) {
        $productList[] = array(
          'id'    => $row['id'],
          'family_name'  => $row['family_name'],
          'given_name' => $row['given_name']
        );
      }
      if(count($productList) == 0){
        echo json_encode(null);
        exit;
      }
      echo json_encode($productList);
      exit;
    case 'work_list':
      $select = isset($_REQUEST['select']) ? $_REQUEST['select'] : '0';
      if($select == "1"){
        $sql = "SELECT * FROM work WHERE archive = false ORDER BY id ASC";
      }else if($select == "2"){
        $sql = "SELECT * FROM work WHERE archive = true ORDER BY id ASC";
      }else{
        $sql = "SELECT * FROM work ORDER BY id ASC";
      }
      if (!($stmt = dbc()->query($sql))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $productList = [];
      foreach($stmt as $row) {
        $productList[] = array(
          'id'    => $row['id'],
          'name'  => $row['name']
        );
      }
      if(count($productList) == 0){
        echo json_encode(null);
        exit;
      }
      echo json_encode($productList);
      exit;
    case 'member_add':
      $err = [];
      if(!isset($_POST['family_name']) || empty($_POST['family_name'])) $err[] = "姓";
      if(!isset($_POST['given_name']) || empty($_POST['given_name'])) $err[] = "名";
      if(!isset($_POST['kana_name']) || empty($_POST['kana_name']) || !(preg_match("/^[ぁ-んー]+$/u", $_POST['kana_name']))) $err[] = "ふりがな";
      if(count($err) != 0) {
        echo json_encode(array("err" => implode('と', $err)."が不正です"));
        exit;
      }
      $sql = "INSERT INTO member(family_name, given_name, kana_name, archive) VALUES(?, ?, ?, ?)";
      $dbc = dbc();
      $stmt = $dbc->prepare($sql);
      if (!($stmt->execute(array($_POST['family_name'], $_POST['given_name'], $_POST['kana_name'], $_POST['archive'])))) {
        echo json_encode(array("err" => "データが正しく保存されませんでした"));
        exit;
      }
      $last_id = $dbc->lastInsertId();
      $sql = "SELECT id, family_name, given_name, kana_name, archive FROM member WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($last_id)))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $productList[] = array(
        'id'    => $row['id'],
        'family_name'  => $row['family_name'],
        'given_name' => $row['given_name'],
        'kana_name' => $row['kana_name'],
        'archive' => $row['archive']
      );
      if(!isset($productList)){
        echo json_encode(null);
        exit;
      }
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
      $sql = "INSERT INTO work(name, multiple, archive, is_above) VALUES(?, ?, ?, ?)";
      $dbc = dbc();
      $stmt = $dbc->prepare($sql);
      if (!($stmt->execute(array($_POST['name'], $_POST['multiple'], $_POST['archive'], $_POST['isAbove'])))) {
        echo json_encode(array("err" => "データが正しく保存されませんでした"));
        exit;
      }
      $last_id = $dbc->lastInsertId();
      $sql = "SELECT id, name, multiple, archive, is_above FROM work WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($last_id)))) {
        echo json_encode(array("err" => "データを��得できませんでした"));
        exit;
      }
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $productList[] = array(
        'id'    => $row['id'],
        'name'    => $row['name'],
        'multiple'  => $row['multiple'],
        'archive' => $row['archive'],
        'isAbove' => $row['is_above']
      );
      if(!isset($productList)){
        echo json_encode(null);
        exit;
      }
      echo json_encode($productList);
      exit;
    case 'member_edit':
      $sql = "SELECT id, family_name, given_name, kana_name, archive FROM member WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($_REQUEST['id'])))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $productList[] = array(
        'id'    => $row['id'],
        'family_name'  => $row['family_name'],
        'given_name' => $row['given_name'],
        'kana_name' => $row['kana_name'],
        'archive' => $row['archive']
      );
      echo json_encode($productList);
      exit;
    case 'work_edit':
      $sql = "SELECT id, name, multiple, archive, is_above FROM work WHERE id = ?";
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
        'archive' => $row['archive'],
        'isAbove' => $row['is_above']
      );
      echo json_encode($productList);
      exit;
    case 'member_update':
      $err = [];
      if(!isset($_POST['family_name']) || empty($_POST['family_name'])) $err[] = "姓";
      if(!isset($_POST['given_name']) || empty($_POST['given_name'])) $err[] = "名";
      if(!isset($_POST['kana_name']) || empty($_POST['kana_name']) || !(preg_match("/^[ぁ-んー]+$/u", $_POST['kana_name']))) $err[] = "ふりがな";
      if(count($err) != 0) {
        echo json_encode(array("err" => implode('と', $err)."が不正です"));
        exit;
      }
      $sql = "UPDATE member SET family_name = ?, given_name = ?, kana_name = ?, archive = ? WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($_POST['family_name'], $_POST['given_name'], $_POST['kana_name'], $_POST['archive'], $_POST['id'])))) {
        echo json_encode(array("err" => "データが正しく更新されませんでした"));
        exit;
      }
      $sql = "SELECT id, family_name, given_name, kana_name, archive FROM member WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($_POST['id'])))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $productList[] = array(
        'id'    => $row['id'],
        'family_name'  => $row['family_name'],
        'given_name' => $row['given_name'],
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
      $sql = "UPDATE work SET name = ?, multiple = ?, archive = ?, is_above = ? WHERE id = ?";
      $stmt = dbc()->prepare($sql);
      if (!($stmt->execute(array($_POST['name'], $_POST['multiple'], $_POST['archive'], $_POST['isAbove'], $_POST['id'])))) {
        echo json_encode(array("err" => "データが正しく更新されませんでした"));
        exit;
      }
      $sql = "SELECT id, name, multiple, archive, is_above FROM work WHERE id = ?";
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
        'archive' => $row['archive'],
        'isAbove' => $row['is_above']
      );
      echo json_encode($productList);
      exit;
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
        if($row['id'] != $history['work_id'] && $row['archive'] == false){
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
      if(isset($_REQUEST["select_work"], $_REQUEST["history_id"], $_REQUEST['date'], $_REQUEST["select_work"])){
        $date = date('Y-m-d',  strtotime($_REQUEST['date']));
        if($_REQUEST["select_work"] == "0"){$_REQUEST["select_work"] = null;}
        if($_REQUEST["check-copy"] == "0"){
          $sql = "UPDATE history SET work_id=? WHERE id=?";
          $arr = array($_REQUEST["select_work"], $_REQUEST["history_id"]);
        }else{
          $stmt2 = dbc()->prepare("SELECT member_id FROM history WHERE id=?");
          if (!($stmt2->execute(array($_REQUEST["history_id"])))) {
            echo json_encode(array("err" => "処理が正しく実行されませんでした"));
            exit;
          }
          $sql = "INSERT INTO history(date, member_id, work_id) VALUES(?, ?, ?)";
          $arr = array($_REQUEST["date"], $stmt2->fetchAll(PDO::FETCH_COLUMN)[0], $_REQUEST["select_work"]);
        }
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute($arr))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
      }else{
        echo json_encode(array("err" => "入力情報が不正です"));
        exit;
      }
      echo json_encode("work");
      exit;
    case 'work-change':
      if(isset($_REQUEST["work_id"], $_REQUEST["date"])){
        $date = date('Y-m-d',  strtotime($_REQUEST['date']));
        $sql = "SELECT id FROM off_work WHERE date=? AND work_id=?";
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute(array($date, $_REQUEST["work_id"])))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
        if($stmt->fetch(PDO::FETCH_COLUMN) == 0){
          $sql2 = "INSERT INTO off_work(date, work_id) VALUES(?, ?)";
          $status = "1";
        }else{
          $sql2 = "DELETE FROM off_work WHERE date=? AND work_id=?";
          $status = "0";
        }
        $stmt2 = dbc()->prepare($sql2);
        if (!($stmt2->execute(array($date, $_REQUEST["work_id"])))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
      }else{
        echo json_encode(array("err" => "入力情報が不正です"));
        exit;
      }
      echo json_encode($status);
      exit;
    // case 'find_member':
    //   if(isset($_REQUEST["history_id"])){
    //     $sql = "SELECT family_name, given_name ,work_id FROM history, member WHERE history.id=? AND member.id = history.member_id";
    //     $stmt = dbc()->prepare($sql);
    //     if (!($stmt->execute(array($_REQUEST["history_id"])))) {
    //       echo json_encode(array("err" => "処理が正しく実行されませんでした"));
    //       exit;
    //     }
    //     $member_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //     $sql2 = "SELECT name FROM work WHERE id=?";
    //     $stmt2 = dbc()->prepare($sql2);
    //     if (!($stmt2->execute(array($member_data[0]["work_id"])))) {
    //       echo json_encode(array("err" => "データを取得できませんでした"));
    //       exit;
    //     }
        
    //     $work_data = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    //     $productList = array(
    //       'member_name'  => $member_data[0]["family_name"].$member_data[0]["given_name"],
    //       'work_name'  => $work_data[0]["name"],
    //     );
    //   }else{
    //     echo json_encode(array("err" => "入力情報が不正です"));
    //     exit;
    //   }
    //   echo json_encode($productList);
    //   exit;
    case 'join_member_remove':
      if(isset($_REQUEST["history_id"])){
        $sql = "DELETE FROM history WHERE id=?";
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute(array($_REQUEST["history_id"])))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
      }else{
        echo json_encode(array("err" => "入力情報が不正です"));
        exit;
      }
      echo json_encode("member");
      exit;
    case 'allocation-remove':
      if(isset($_REQUEST["date"])){
        $date = date('Y-m-d', strtotime($_REQUEST['date']));
        $sql = "UPDATE history SET work_id=null WHERE date=?";
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute(array($date)))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
      }else{
        echo json_encode(array("err" => "入力情報が不正です"));
        exit;
      }
      echo json_encode("member");
      exit;
    case 'option_list':
      $sql = "SELECT id ,family_name, given_name, archive FROM member ORDER BY member.kana_name ASC";
      $sql2 = "SELECT id ,name, archive FROM work";
      $sql3 = "SELECT id ,member_id, work_id, status FROM member_option";

      if (!($stmt = dbc()->query($sql))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }

      if (!($stmt2 = dbc()->query($sql2))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }

      if (!($stmt3 = dbc()->query($sql3))) {
        echo json_encode(array("err" => "データを取得できませんでした"));
        exit;
      }

      $productList[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $productList[] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
      $productList[] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode($productList);
      exit;
    case 'add-member_option':
      if(is_numeric($_REQUEST["work_id"]) && is_numeric($_REQUEST["member_id"]) && is_numeric($_REQUEST["status"])){
        $sql = "INSERT INTO member_option(work_id, member_id, status) VALUES(?, ?, ?)";
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute(array($_REQUEST["work_id"], $_REQUEST["member_id"], $_REQUEST["status"])))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
        echo json_encode(array("success" => true));
      }else{
        echo json_encode(array("err" => "入力情報が不正です"));
      }
      exit;
    case 'confirm-member_option':
      if(is_numeric($_REQUEST["option_id"])){
        $sql = "SELECT member_option.id, family_name, given_name, name, status FROM member_option, work, member WHERE member_option.id=? AND member.id=member_option.member_id AND work.id=member_option.work_id";
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute(array($_REQUEST["option_id"])))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
      }else{
        echo json_encode(array("err" => "入力情報が不正です"));
        exit;
      }
      echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
      exit;
    case 'delete-member_option':
      if(is_numeric($_REQUEST["option_id"])){
        $sql = "DELETE FROM member_option WHERE id=?";
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute(array($_REQUEST["option_id"])))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
      }else{
        echo json_encode(array("err" => "入力情報が不正です"));
        exit;
      }
      echo json_encode("option");
      exit;
    case "update-member_option":
      if(is_numeric($_REQUEST["work_id"]) && is_numeric($_REQUEST["member_id"]) && is_numeric($_REQUEST["option_id"])){
        $sql = "UPDATE member_option SET work_id=?, member_id=?  WHERE id=?";
        $stmt = dbc()->prepare($sql);
        if (!($stmt->execute(array($_REQUEST["work_id"], $_REQUEST["member_id"], $_REQUEST["option_id"])))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
        $sql2 = "SELECT member_option.id, family_name, given_name, name, status FROM member_option, work, member WHERE member_option.id=? AND member.id=member_option.member_id AND work.id=member_option.work_id";
        $stmt2 = dbc()->prepare($sql2);
        if (!($stmt2->execute(array($_REQUEST["option_id"])))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
        $update = ($stmt2->fetch(PDO::FETCH_ASSOC));
        $status = ($update["status"] == 0)?"固定":"除外";
        if($_REQUEST["change_tag"] == "works"){
          $update_message = $update["family_name"].$update["given_name"]."さんの".$status."作業を".$update["name"]."に変更しました";
        }else{
          $update_message = $update["name"]."の".$status."対象を".$update["family_name"].$update["given_name"]."さんに変更しました";
        }
      }else{
        echo json_encode(array("err" => "入力情報が不正です"));
        exit;
      }
      echo json_encode($update_message);
      exit;
    case 'shuffle':
      if(isset($_REQUEST["date"])){
        $date = date('Y-m-d', strtotime($_REQUEST['date']));
        $sql = "SELECT id, multiple, is_above FROM work WHERE archive=false";
        $sql2 = "SELECT id, member_id, work_id FROM history WHERE date=?";
        $sql3 = "SELECT work_id FROM off_work WHERE date=?";
        
        if (!($stmt = dbc()->query($sql))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
        $stmt2 = dbc()->prepare($sql2);
        if (!($stmt2->execute(array($date)))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
        $stmt3 = dbc()->prepare($sql3);
        if (!($stmt3->execute(array($date)))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
        
        // MemberOptionテーブルから固定（status=0）と除外（status=1）のメンバーを取得
        $sql_option = "SELECT member_id, work_id, status FROM member_option";
        $stmt_option = dbc()->query($sql_option);
        $fixed_members = []; // 固定メンバー：[work_id => [member_id, ...]]
        $excluded_members = []; // 除外メンバー：[work_id => [member_id, ...]]
        
        if ($stmt_option) {
          foreach($stmt_option->fetchAll(PDO::FETCH_ASSOC) as $option_row) {
            if ($option_row['status'] == 0) {
              // status=0: 固定割り当て
              if (!isset($fixed_members[$option_row['work_id']])) {
                $fixed_members[$option_row['work_id']] = [];
              }
              $fixed_members[$option_row['work_id']][] = $option_row['member_id'];
            } else if ($option_row['status'] == 1) {
              // status=1: 除外
              if (!isset($excluded_members[$option_row['work_id']])) {
                $excluded_members[$option_row['work_id']] = [];
              }
              $excluded_members[$option_row['work_id']][] = $option_row['member_id'];
            }
          }
        }
        
        // Worksheetテーブルからinterval、weekUse、weekを取得
        $sql_worksheet = "SELECT interval, week_use, week FROM worksheet LIMIT 1";
        $stmt_worksheet = dbc()->query($sql_worksheet);
        $interval = 0;
        $week_use = false;
        $week = 0;
        if ($stmt_worksheet) {
          $worksheet_row = $stmt_worksheet->fetch(PDO::FETCH_ASSOC);
          if ($worksheet_row && isset($worksheet_row['interval'])) {
            $interval = intval($worksheet_row['interval']);
            $week_use = $worksheet_row['week_use'];
            $week = intval($worksheet_row['week']);
          }
        }
        
        // 過去interval日間のメンバー作業履歴を取得
        $recent_member_works = []; // [member_id => [work_id, ...]]
        if ($interval > 0) {
          $start_date = date('Y-m-d', strtotime("-{$interval} days", strtotime($date)));
          
          // weekUseがtrueの場合、曜日に基づいてintervalカウントをリセット判定
          if ($week_use) {
            // シャッフル日以前の最も近いweekカラムの曜日を見つける
            $found_date = false;
            for ($i = 0; $i < 7; $i++) {
              $check_date = date('Y-m-d', strtotime("-{$i} days", strtotime($date)));
              $check_day_of_week = intval(date('w', strtotime($check_date)));
              
              if ($check_day_of_week == $week) {
                $start_date = $check_date;
                $found_date = true;
                break;
              }
            }
            
            // 見つからない場合はデフォルトの過去interval日を使用
            if (!$found_date) {
              $start_date = date('Y-m-d', strtotime("-{$interval} days", strtotime($date)));
            }
          } else {
            // weekUseがfalseの場合、ShuffleOptionテーブルを参照
            // 現在日付以前のreset_dateの中から最も新しいものを基準にする
            $sql_reset_dates = "SELECT reset_date FROM shuffle_option WHERE reset_date <= ? ORDER BY reset_date DESC LIMIT 1";
            $stmt_reset_dates = dbc()->prepare($sql_reset_dates);
            
            if ($stmt_reset_dates->execute(array($date))) {
              $reset_date_row = $stmt_reset_dates->fetch(PDO::FETCH_ASSOC);
              if ($reset_date_row && !empty($reset_date_row['reset_date'])) {
                // reset_dateが存在する場合、その日付をstart_dateに設定
                $reset_date = $reset_date_row['reset_date'];
                $start_date = $reset_date;
              }
            }
          }
          
          $sql_recent = "SELECT DISTINCT member_id, work_id FROM history WHERE date BETWEEN ? AND ? AND work_id IS NOT NULL";
          $stmt_recent = dbc()->prepare($sql_recent);
          if ($stmt_recent->execute(array($start_date, $date))) {
            foreach($stmt_recent->fetchAll(PDO::FETCH_ASSOC) as $recent_row) {
              if (!isset($recent_member_works[$recent_row['member_id']])) {
                $recent_member_works[$recent_row['member_id']] = [];
              }
              if (!in_array($recent_row['work_id'], $recent_member_works[$recent_row['member_id']])) {
                $recent_member_works[$recent_row['member_id']][] = $recent_row['work_id'];
              }
            }
          }
        }
        
        $off_works = $stmt3->fetchAll(PDO::FETCH_COLUMN);
        $work_list = [];
        $work_limits = []; // 作業ごとの割り当て上限
        $work_assignment_count = []; // 作業ごとの割り当て数
        $work_info = []; // 作業情報を保存
        
        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
          // off_workテーブルで指定日付にOFF状態の作業は除外
          if(!in_array($row["id"], $off_works)){
            $work_info[$row["id"]] = $row;
            $work_assignment_count[$row["id"]] = 0;
            // is_above を参照して割り当て方法を決定
            if($row["is_above"]){ // 以上：最低でもmultipleの数割り当て
              $work_limits[$row["id"]] = -1; // 無制限（-1で表す）
              for($i = 0; $i < $row["multiple"]; $i++){
                $work_list[] = $row["id"];
              };
            } else { // 以下：multipleの数以下割り当て
              $work_limits[$row["id"]] = $row["multiple"];
              $work_list[] = $row["id"];
            }
          }
        }

        $history_list = [];
        $history_members = []; // member_idを保存
        foreach($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
          $history_list[] = $row["id"];
          $history_members[$row["id"]] = $row["member_id"];
        }
        
        // メンバー数がwork_list数より多い場合、is_above=trueの作業で拡張
        if(count($history_list) > count($work_list)) {
          $additional_needed = count($history_list) - count($work_list);
          $work_ids = array_keys($work_info);
          $idx = 0;
          while($additional_needed > 0) {
            $work_id = $work_ids[$idx % count($work_ids)];
            if($work_info[$work_id]["is_above"]){ // is_above=trueのみ追加可
              $work_list[] = $work_id;
              $additional_needed--;
            }
            $idx++;
          }
        }
        
        shuffle($work_list);
        shuffle($history_list);
        $work_stock = $work_list;

        $column_data = "";
        $work_index = 0;
        $fixed_assigned = []; // 固定により既に割り当てられたhistory_id
        
        // 先に固定メンバーを割り当てる (status=0)
        foreach($history_list as $history_id) {
          $member_id = $history_members[$history_id];
          
          // 各workについて、このメンバーが固定割り当て対象かチェック
          foreach($fixed_members as $work_id => $member_ids) {
            if (in_array($member_id, $member_ids)) {
              // 除外メンバーでないか、過去interval日間に同じ作業に割り当てられていないかチェック
              $is_excluded_member = isset($excluded_members[$work_id]) && in_array($member_id, $excluded_members[$work_id]);
              $is_recent_work = isset($recent_member_works[$member_id]) && in_array($work_id, $recent_member_works[$member_id]);
              
              // 割り当て可能な場合のみ実行
              if (!$is_excluded_member && !$is_recent_work) {
                $column_data = $column_data . "WHEN {$history_id} THEN {$work_id} ";
                $work_assignment_count[$work_id]++;
                $fixed_assigned[$history_id] = true;
              }
              break;
            }
          }
        }
        
        // 固定で割り当てられなかったメンバーを均等に振り分ける
        foreach($history_list as $row) {
          if (isset($fixed_assigned[$row])) {
            // 既に固定で割り当てられた
            continue;
          }
          
          $member_id = $history_members[$row];
          
          if(count($work_list) != 0){
            // 除外メンバーを避けて割り当て先を探す
            $assigned_work = null;
            $attempts = 0;
            
            while ($assigned_work === null && $attempts < count($work_list)) {
              $candidate_work = $work_list[$work_index % count($work_list)];
              
              // 除外リストにこのメンバーがいないかチェック
              $is_excluded_member = isset($excluded_members[$candidate_work]) && in_array($member_id, $excluded_members[$candidate_work]);
              
              // 過去interval日間に同じ作業に割り当てられたかチェック
              $is_recent_work = isset($recent_member_works[$member_id]) && in_array($candidate_work, $recent_member_works[$member_id]);
              
              if (!$is_excluded_member && !$is_recent_work) {
                // 上限チェック：is_above = false の場合、multiple の数を超えないようにする
                if($work_limits[$candidate_work] == -1 || $work_assignment_count[$candidate_work] < $work_limits[$candidate_work]){
                  $assigned_work = $candidate_work;
                  $work_assignment_count[$candidate_work]++;
                }
              }
              
              $work_index++;
              $attempts++;
            }
            
            if ($assigned_work !== null) {
              $column_data = $column_data . "WHEN {$row} THEN {$assigned_work} ";
            } else {
              // 割り当て可能な作業がない場合は担当なし
              $column_data = $column_data . "WHEN {$row} THEN null ";
            }
          }else{
            $column_data = $column_data . "WHEN {$row} THEN null ";
          }
        }
        $ids = implode(",", $history_list);
        $sql3 = "UPDATE history SET work_id = CASE id ".$column_data." END WHERE id IN (".$ids.")";
        if (!(dbc()->query($sql3))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
      }else{
        echo json_encode(array("err" => "入力情報が不正です"));
        exit;
      }
      echo json_encode("shuffle");
      exit;
    case 'get_interval':
      $sql = "SELECT interval FROM worksheet LIMIT 1";
      $stmt = dbc()->query($sql);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        echo json_encode(array("interval" => $row['interval']));
      } else {
        echo json_encode(array("interval" => 0));
      }
      exit;
    case 'get_week_use':
      $sql = "SELECT week_use FROM worksheet LIMIT 1";
      $stmt = dbc()->query($sql);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        echo json_encode(array("weekUse" => $row['week_use']));
      } else {
        echo json_encode(array("weekUse" => false));
      }
      exit;
    case 'get_week':
      $sql = "SELECT week FROM worksheet LIMIT 1";
      $stmt = dbc()->query($sql);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        echo json_encode(array("week" => $row['week']));
      } else {
        echo json_encode(array("week" => 0));
      }
      exit;
    case 'save_interval':
      if (!isset($_POST['interval']) || !is_numeric($_POST['interval'])) {
        echo json_encode(array("err" => "不正なデータです"));
        exit;
      }
      $interval = intval($_POST['interval']);
      
      try {
        $dbc = dbc();
        // 既存データを確認
        $sql = "SELECT COUNT(*) as cnt FROM worksheet";
        $stmt = $dbc->query($sql);
        if ($stmt === false) {
          // テーブルが存在しない場合は作成
          $createSql = "CREATE TABLE IF NOT EXISTS worksheet (id INT PRIMARY KEY DEFAULT 1, interval INT DEFAULT 0)";
          $dbc->exec($createSql);
        }
        
        // 再度カウント取得
        $stmt = $dbc->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row['cnt'] > 0) {
          // データが存在する場合は更新
          $sql = "UPDATE worksheet SET interval = ? WHERE id = 1";
          $stmt = $dbc->prepare($sql);
          $stmt->execute(array($interval));
        } else {
          // データが存在しない場合は挿入
          $sql = "INSERT INTO worksheet (id, interval) VALUES (1, ?)";
          $stmt = $dbc->prepare($sql);
          $stmt->execute(array($interval));
        }
        
        echo json_encode(array("success" => true));
      } catch (PDOException $e) {
        echo json_encode(array("err" => "エラー: " . $e->getMessage()));
      }
      exit;
    case 'save_week_use':
      if (!isset($_POST['weekUse'])) {
        echo json_encode(array("err" => "不正なデータです"));
        exit;
      }
      $weekUse = intval($_POST['weekUse']);
      
      try {
        $dbc = dbc();
        
        // worksheetテーブルからデータ取得（エラーハンドリング）
        try {
          $sql = "SELECT COUNT(*) as cnt FROM worksheet";
          $stmt = $dbc->query($sql);
          if ($stmt === false) {
            throw new Exception("クエリ実行失敗");
          }
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          $recordCount = $row['cnt'] ?? 0;
        } catch (Exception $e) {
          // テーブルが存在しない場合
          $recordCount = 0;
        }
        
        if ($recordCount > 0) {
          // データが存在する場合は更新
          $sql = "UPDATE worksheet SET week_use = ? WHERE id = 1";
          $stmt = $dbc->prepare($sql);
          $stmt->execute(array($weekUse));
        } else {
          // データが存在しない場合は挿入
          $sql = "INSERT INTO worksheet (id, week_use) VALUES (1, ?)";
          $stmt = $dbc->prepare($sql);
          $stmt->execute(array($weekUse));
        }
        
        echo json_encode(array("success" => true));
      } catch (PDOException $e) {
        echo json_encode(array("err" => "エラー: " . $e->getMessage()));
      }
      exit;
    case 'save_week':
      if (!isset($_POST['week'])) {
        echo json_encode(array("err" => "不正なデータです"));
        exit;
      }
      $week = intval($_POST['week']);
      
      try {
        $dbc = dbc();
        
        // worksheetテーブルからデータ取得（エラーハンドリング）
        try {
          $sql = "SELECT COUNT(*) as cnt FROM worksheet";
          $stmt = $dbc->query($sql);
          if ($stmt === false) {
            throw new Exception("クエリ実行失敗");
          }
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          $recordCount = $row['cnt'] ?? 0;
        } catch (Exception $e) {
          // テーブルが存在しない場合
          $recordCount = 0;
        }
        
        if ($recordCount > 0) {
          // データが存在する場合は更新
          $sql = "UPDATE worksheet SET week = ? WHERE id = 1";
          $stmt = $dbc->prepare($sql);
          $stmt->execute(array($week));
        } else {
          // データが存在しない場合は挿入
          $sql = "INSERT INTO worksheet (id, week) VALUES (1, ?)";
          $stmt = $dbc->prepare($sql);
          $stmt->execute(array($week));
        }
        
        echo json_encode(array("success" => true));
      } catch (PDOException $e) {
        echo json_encode(array("err" => "エラー: " . $e->getMessage()));
      }
      exit;
    case 'get_reset_dates':
      $sql = "SELECT reset_date FROM shuffle_option ORDER BY reset_date ASC";
      $stmt = dbc()->query($sql);
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $dates = [];
      if ($rows) {
        foreach ($rows as $row) {
          if ($row['reset_date']) {
            $dates[] = $row['reset_date'];
          }
        }
      }
      echo json_encode(array("dates" => $dates));
      exit;
    case 'save_reset_dates':
      if (!isset($_POST['dates'])) {
        echo json_encode(array("err" => "不正なデータです"));
        exit;
      }
      $datesJson = $_POST['dates'];
      
      // JSON デコードして検証
      $dates = json_decode($datesJson, true);
      if (!is_array($dates)) {
        echo json_encode(array("err" => "日付形式が不正です"));
        exit;
      }
      
      try {
        $dbc = dbc();
        
        // テーブル存在確認
        $sql = "SELECT COUNT(*) as cnt FROM shuffle_option";
        $stmt = $dbc->query($sql);
        if ($stmt === false) {
          // テーブルが存在しない場合は作成
          $createSql = "CREATE TABLE IF NOT EXISTS shuffle_option (id INT AUTO_INCREMENT PRIMARY KEY, reset_date DATE)";
          $dbc->exec($createSql);
        }
        
        // 既存のリセット日程をすべて削除
        $sql = "DELETE FROM shuffle_option";
        $stmt = $dbc->prepare($sql);
        $stmt->execute();
        
        // 各日付を別々のレコードとして挿入
        $sql = "INSERT INTO shuffle_option (reset_date) VALUES (?)";
        $stmt = $dbc->prepare($sql);
        
        foreach ($dates as $date) {
          // 日付フォーマットの検証
          if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            echo json_encode(array("err" => "日付形式が不正です: " . $date));
            exit;
          }
          
          $stmt->execute(array($date));
        }
        
        echo json_encode(array("success" => true));
      } catch (PDOException $e) {
        echo json_encode(array("err" => "エラー: " . $e->getMessage()));
      }
      exit;
  };
}catch(PDOException $e){
  echo json_encode(array("err" => "データベースエラーが発生しました: " . $e->getMessage()));
  exit;
}
