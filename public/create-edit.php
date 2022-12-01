<?php $title = "登録・編集"; require_once "../component/head_component.php" ?>
<link rel="stylesheet" href="../src/css/create-edit.css" />
<link rel="stylesheet" href="../src/css/member-create.css" />
<link rel="stylesheet" href="../src/css/work-create.css" />
<?php require_once "../component/sidebar_component.php"; ?>
<div class="tabs">
  <input id="all" type="radio" name="tab_item" checked>
  <label class="tab_item" for="all">メンバー</label>
  <input id="programming" type="radio" name="tab_item">
  <label class="tab_item" for="programming">作業</label>
  
  <div class="tab_content" id="all_content">
    <div class="tab_content_description">
      <select name="member_view">
        <option value="false">有効のみ表示</option>
        <option value="true">全て表示</option>
      </select>
      <button class="md-btn" data-target="modal-member">メンバー登録</button>
      <ul id="member_show_result">
      </ul>
    </div>
  </div>
  <div class="tab_content" id="programming_content">
    <div class="tab_content_description">
      <!-- レコード全件取得 -->
      <select name="work_view">
        <option value="false">有効のみ表示</option>
        <option value="true">全て表示</option>
      </select>
      <button class="md-btn" data-target="modal-work">作業登録</button>
      <!-- 追加したレコードも即時反映される -->
      <ul id="work_show_result">
      </ul>
    </div>
  </div>
  
</div>






<?php require_once "../public/modal/modal-member.php" ?>
<?php require_once "../public/modal/modal-work.php" ?>
<script src="../src/js/ajax.js"></script>
<script defer src="../src/js/modal.js"></script>
