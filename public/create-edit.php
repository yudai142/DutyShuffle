<?php require_once "../component/head_component.php" ?>
<link rel="stylesheet" href="../src/css/create-edit.css" />
<link rel="stylesheet" href="../src/css/member-create.css" />
<link rel="stylesheet" href="../src/css/work-create.css" />
<?php $title = "登録・編集"; require_once "../component/sidebar_component.php"; ?>


<!-- レコード全件取得 -->
<!-- 追加したレコードも即時反映される -->
<table border="1" id="all_show_result">
    <tr>
        <th>id</th><th>作業名</th><th>人数</th><th>有効/無効</th>
    </tr>
</table>
<button id="modal-open">作業登録</button>




<?php require_once "../public/work-create.php" ?>
<script src="../src/js/modal.js"></script>
<script src="../src/js/ajax.js"></script>