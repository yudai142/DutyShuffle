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
        if(in_array($row['id'], $work_id_list) || $row['archive'] == 0){
          $status = (in_array($row['id'], $off_works))? "0" : "1";
          $productList[0][] = array(
            'id'    => $row['id'],
            'name'  => $row['name'],
            'status'  => $status
          );
        }
      }
      
      echo json_encode($productList);
      exit;
    case 'join_member':
      $date = date('Y-m-d', strtotime($_REQUEST['date']));
      $sql = "SELECT history.id ,family_name, given_name, work_id FROM history, member WHERE date=? AND member.id = history.member_id group by member_id ORDER BY member.kana_name ASC";
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
      echo json_encode($productList);
      exit;
    case 'join_work':
      $date = date('Y-m-d', strtotime($_REQUEST['date']));
      $sql = "SELECT id, name FROM work WHERE archive=0";
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
        if(in_array($row['id'], $checked) || $row['archive'] == 0){
          $productList[] = array(
            'id'    => $row['id'],
            'family_name'  => $row['family_name'],
            'given_name' => $row['given_name'],
            'checked' => (in_array($row['id'], $checked)) ? "checked" : ""
          );
        }
      }
      echo json_encode($productList);
      exit;
    case 'member_select_check':
      $date = date('Y-m-d',  strtotime($_REQUEST['date']));
      $select = $_REQUEST['select'];
      $sql = "SELECT member_id work_id family_name given_name FROM history, member WHERE date=? AND member.id = history.member_id group by member_id ORDER BY kana_name ASC";
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
      $select = ($_REQUEST['select']) ? $_REQUEST['select'] : [];
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
      echo json_encode($productList);
      exit;
    case 'member_select_work_definition':
      $date = date('Y-m-d',  strtotime($_REQUEST['date']));
      $select = ($_REQUEST['select']) ? $_REQUEST['select'] : [];
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
          'family_name'  => $row['family_name'],
          'given_name' => $row['given_name']
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
      }else{
        echo json_encode(array("err" => "入力情報が不正です"));
        exit;
      }
      echo json_encode("option");
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
        $sql = "SELECT id, multiple FROM work WHERE archive=0";
        $sql2 = "SELECT id, member_id, work_id FROM history WHERE date=?";
        
        if (!($stmt = dbc()->query($sql))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
        $stmt2 = dbc()->prepare($sql2);
        if (!($stmt2->execute(array($date)))) {
          echo json_encode(array("err" => "処理が正しく実行されませんでした"));
          exit;
        }
        $work_list = [];
        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
          for($i = 0; $i < $row["multiple"]; $i++){
            $work_list[] = $row["id"];
          };
        }

        $history_list = [];
        foreach($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
          $history_list[] = $row["id"];
        }
        shuffle($work_list);
        shuffle($history_list);
        $work_stock = $work_list;

        $column_data = "";
        foreach($history_list as $row) {
          if(count($work_list) != 0){
            $column_data = $column_data . "WHEN {$row} THEN {$work_list[0]} ";
            array_shift($work_list);
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
        
        echo json_encode(array($history_list, $work_stock,$work_list));
        exit;
      }else{
        echo json_encode(array("err" => "入力情報が不正です"));
        exit;
      }
      echo json_encode("shuffle");
      exit;
  };
}catch(PDOException $e){
  exit($e->getMessage());
}
