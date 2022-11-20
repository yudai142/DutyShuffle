<?php
// データベース接続
require_once("../dbc/dbc.php");
// データ取得用SQL
// 値はバインドさせる
$sql = "SELECT * FROM work";
// SQLをセット
$stmt = dbc()->prepare($sql);
// SQLを実行
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

// ヘッダーを指定することによりjsonの動作を安定させる
header('Content-type: application/json');
// htmlへ渡す配列$productListをjsonに変換する
echo json_encode($productList);