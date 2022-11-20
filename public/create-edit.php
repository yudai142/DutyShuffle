<?php require_once "../component/head_component.php" ?>
<link rel="stylesheet" href="../src/css/create-edit.css" />
<link rel="stylesheet" href="../src/css/member-create.css" />
<link rel="stylesheet" href="../src/css/work-create.css" />
<?php $title = "登録・編集"; require_once "../component/sidebar_component.php"; ?>
<div class="tabs">
  <input id="all" type="radio" name="tab_item" checked>
  <label class="tab_item" for="all">メンバー</label>
  <input id="programming" type="radio" name="tab_item">
  <label class="tab_item" for="programming">作業</label>
  
  <div class="tab_content" id="all_content">
    <div class="tab_content_description">
      <p class="c-txtsp">プログラミングの内容がここに入ります</p>
    </div>
  </div>
  <div class="tab_content" id="programming_content">
    <div class="tab_content_description">
      <!-- レコード全件取得 -->
      <select name="sex">
        <option value="false">有効のみ表示</option>
        <option value="true">全て表示</option>
      </select>
      <button id="modal-open">作業登録</button>
      <!-- 追加したレコードも即時反映される -->
      <table border="1" id="all_show_result">
          <tr>
              <th>id</th><th>作業名</th><th>人数</th><th>有効/無効</th>
          </tr>
      </table>
    </div>
  </div>
  
</div>






<?php require_once "../public/work-create.php" ?>
<script src="../src/js/modal.js"></script>
<script src="../src/js/ajax.js"></script>