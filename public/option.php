<?php $title = "オプション"; require_once "../component/head_component.php" ?>
<link rel="stylesheet" href="../src/css/option.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<?php require_once "../component/sidebar_component.php"; ?>

<div class="shuffle_option-form">
  <div class="shuffle_option-content">
    <p>作業が被らない期間：<input type="number" id="interval_input" class="input-tx sm" value="0" min="0">日</p>
    <div id="reset_date_row" class="reset-date-row">
      <p>被らない期間のリセット日程：<label class="btn yellow" id="reset_date_clear_btn" for="datepicker">選択</label><input type="text" id="datepicker" style="width:0;height:0;border:none;padding:0;margin:0;"></p>
    </div>
    
    <div id="option_result" style="color:orange;"></div>
  </div>
  <span class="btn green" id="interval_save_btn">変更</span>
</div>

<div class="tabs">
  <input id="all" type="radio" name="tab_item" checked>
  <label class="tab_item" for="all">固定</label>
  <input id="programming" type="radio" name="tab_item">
  <label class="tab_item" for="programming">除外</label>
  <div class="tab_option" id="all_content">
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
  <div class="tab_option" id="programming_content">
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
</div>

<script src="../src/js/ajax.js"></script>
