<?php $title = "オプション"; require_once "../component/head_component.php" ?>
<link rel="stylesheet" href="../src/css/option.css" />
<?php require_once "../component/sidebar_component.php"; ?>

<div class="shuffle_option-form">
  <div class="shuffle_option-content">
    <p>作業が被らない期間：<input type="text" class="input-tx sm">日</p>
    <p>シャッフル時の判定開始日：<input type="text" class="input-tx"><span class="btn yellow">リセット</span></p>
  </div>
  <span class="btn green">変更</span>
</div>
<div class="tabs">
  <input id="all" type="radio" name="tab_item" checked>
  <label class="tab_item" for="all">固定</label>
  <input id="programming" type="radio" name="tab_item">
  <label class="tab_item" for="programming">除外</label>
  <div class="tab_content" id="all_content">
    <div class="tab_container">
      <h3>シャッフルの際に設定した担当に必ず割り当てる設定です</h3>
      <div class="option-title">
        <p>作業名</p>
        <p>メンバー</p>
      </div>
      <!-- <div class="option-title">
        <ul class="option-form">
          <li class="button work square">リーダー</li>
          <li class="button member square">立石　達</li>
          <li class="button work">削除</li>
        </ul>
      </div> -->
      <div class="option-list" id="fixed_list">
        <!-- <ul class="option-group"> -->
          <!-- <li class="button work square">リーダー</li> -->
          <!-- <li class="button member square">立石　達</li> -->
          <!-- <li class="button work">削除</li> -->
        <!-- </ul> -->
      </div>
    </div>
  </div>
  <div class="tab_content" id="programming_content">
    <div class="tab_container">
      <h3>シャッフルの際に設定した担当に割り当てない設定です</h3>
      <div class="option-title">
        <p>作業名</p>
        <p>メンバー</p>
      </div>
      <!-- <div class="option-title">
        <ul class="option-form">
          <li class="button work square">リーダー</li>
          <li class="button member square">立石　達</li>
          <li class="button work">削除</li>
        </ul>
      </div> -->
      <div class="option-list" id="exclusion_list">
        <!-- <ul class="option-group">
          <li class="button work square">リーダー</li>
          <li class="button member square">立石　達</li>
          <li class="button work">削除</li>
        </ul> -->
      </div>
    </div>
  </div>

<script src="../src/js/ajax.js"></script>
