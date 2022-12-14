<?php $title = "オプション"; require_once "../component/head_component.php" ?>
<link rel="stylesheet" href="../src/css/option.css" />
<?php require_once "../component/sidebar_component.php"; ?>

<div class="tabs">
  <input id="all" type="radio" name="tab_item" checked>
  <label class="tab_item" for="all">固定</label>
  <input id="programming" type="radio" name="tab_item">
  <label class="tab_item" for="programming">除外</label>
  <div class="tab_content" id="all_content">
    <div class="tab_content_selects">
      <h3>シャッフルの際に設定した担当に必ず割り当てる設定です</h3>
      <ul id="fixed_list"></ul>
    </div>
  </div>
  <div class="tab_content" id="programming_content">
    <div class="tab_content_selects">
      <h3>シャッフルの際に設定した担当に割り当てない設定です</h3>
      <ul id="exclusion_list"></ul>
    </div>
  </div>

<script src="../src/js/ajax.js"></script>
