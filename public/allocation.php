<?php date_default_timezone_set('Asia/Tokyo');(isset($_GET['ym']))? $title = "割り当て(".date('Y/m/d',  strtotime($_GET['ym'])).")" : $title = "割り当て(".date('Y/m/d').")" ; require_once "../component/head_component.php" ?>
<link rel="stylesheet" href="../src/css/allocation.css" />
<link rel="stylesheet" href="../src/css/select.css" />
<?php $title = "割り当て"; require_once "../component/sidebar_component.php"; ?>

<div class="date-form">
  <div class="icon-left">
    <a href="?ym=<?php echo $prev; ?>">
      <i class="fas fa-arrow-alt-circle-left fa-2x"></i>
    </a>
  </div>
  <label>
    <input type="date" id="date" value="<?php echo $ym ?>" />
  </label>
  <div class="icon-right">
    <a href="?ym=<?php echo $next; ?>">
      <i class="fas fa-arrow-alt-circle-right fa-2x"></i>
    </a>
  </div>
  <span style="position: relative;"><a href="login.php" class="login-button blue-button">シャッフル</a></span>
</div>

<div class="allocation-table" style="margin:50px 40px">
  <div class="allocation-head" style="display:flex;justify-content: space-between;flex-wrap:wrap;">
    <h3>担当</h3>
    <div class="allocation-head-right">
      <button class="state-btn" data-target="allocation-remove">担当全解除</button>
      <button class="md-btn" data-target="modal-select">メンバー追加</button>
    </div>
  </div>
  <div id="allocation-form" style="display:flex;"></div>
</div>
<div class="support-table" style="margin:0 40px">
  <h3>サポート(未選択)</h3>
  <ul id="null-member-list"></ul>
</div>

<?php require_once "../public/modal/modal-select.php" ?>
<script src="../src/js/ajax.js"></script>
<script defer src="../src/js/modal.js"></script>
<script src="../src/js/date.js"></script>
