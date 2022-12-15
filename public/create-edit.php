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
    <div class="btns">
      <div class="btn green1 md-btn" data-target="modal-member">メンバー登録</div>
      <select class="btn yellow" id="member_view">
        <option value="0">全て表示</option>
        <option value="1" selected>有効のみ表示</option>
        <option value="2">無効のみ表示</option>
      </select>
    </div>
    <div class="btn-names"> 
      <div class="button-group" id="member_show_result">
        <!-- <div class="button member b-select">aa</div> -->
      </div>
    </div>
    <!-- 
    <div class="tab_content_selects">
      <select id="member_view">
        <option value="0">全て表示</option>
        <option value="1" selected>有効のみ表示</option>
        <option value="2">無効のみ表示</option>
      </select>
      <button class="md-btn" data-target="modal-member">メンバー登録</button>
    </div>
    <div class="tab_content_description">
      <ul id="member_show_result">
      </ul>
    </div> -->
  </div>
  <div class="tab_content" id="programming_content">
    <div class="btns">
      <div class="btn green1 md-btn" data-target="modal-work">作業登録</div>
      <select class="btn yellow" id="work_view">
        <option value="0">全て表示</option>
        <option value="1" selected>有効のみ表示</option>
        <option value="2">無効のみ表示</option>
      </select>
    </div>
    <div class="btn-names"> 
      <div class="button-group" id="work_show_result">
        <!-- <div class="button member b-select w-color">aa</div> -->
      </div>
    </div>

    <!-- <div class="tab_content_selects">
      <select id="work_view">
        <option value="0">全て表示</option>
        <option value="1" selected>有効のみ表示</option>
        <option value="2">無効のみ表示</option>
      </select>
      <button class="md-btn" data-target="modal-work">作業登録</button>
    </div>
    <div class="tab_content_description">
      <ul id="work_show_result">
      </ul>
    </div> -->
  </div>
  
</div>






<?php require_once "../public/modal/modal-member.php" ?>
<?php require_once "../public/modal/modal-work.php" ?>
<script src="../src/js/ajax.js"></script>
<script defer src="../src/js/modal.js"></script>
