<?php
require_once("../dbc/dbc.php");
// POSTメソッドでリクエストした値を取得
$name = $_POST['name'];
$multiple = $_POST['multiple'];
$archive = $_POST['archive'];

// データ追加用SQL
// 値はバインドさせる
$sql = "INSERT INTO work(name, multiple, archive) VALUES(?, ?, ?)";
// SQLをセット
$dbc = dbc();
$stmt = $dbc->prepare($sql);
// SQLを実行
$stmt->execute(array($name, $multiple, $archive));
// var_dump($dbh);

// 先ほど追加したデータを取得
// idはlastInsertId()で取得できる
$last_id = $dbc->lastInsertId();
// データ追加用SQL
// 値はバインドさせる
$sql = "SELECT id, name, multiple, archive FROM work WHERE id = ?";
// SQLをセット
$stmt = (dbc()->prepare($sql));
// SQLを実行
$stmt->execute(array($last_id));

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
        'name'    => $row['name'],
        'multiple'  => $row['multiple'],
        'archive' => $row['archive']
    );
}

// ヘッダーを指定することによりjsonの動作を安定させる
header('Content-type: application/json');
// htmlへ渡す配列$productListをjsonに変換する
echo json_encode($productList);