<?php date_default_timezone_set('Asia/Tokyo');(isset($_GET['ym']))? $title = "トップ(".date('Y/m/d',  strtotime($_GET['ym'])).")" : $title = "トップ(".date('Y/m/d').")" ; require_once "../component/head_component.php" ?>
<link rel="stylesheet" href="../src/css/top.css" />
<link rel="stylesheet" href="../src/css/select.css" />
<?php $title = "トップ"; require_once "../component/sidebar_component.php"; ?>
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
  <?php if(isset($member)):?>
    <span><a href="logout.php" class="logout-button red-button">ログアウト</a></span>
    <span><a href="list.php" class="list-button gray-button">予約リスト</a></span>
  <?php else:?>
    <span style="position: relative;"><a href="login.php" class="login-button blue-button">割り当てへ</a></span>
  <?php endif;?>
</div>

<div class="join_work">
  <ul id="join_work" style="display:flex;margin-bottom:30px;"></ul>
</div><!-- /.work_list -->
<div class="join_member">
  <button class="md-btn" data-target="modal-select">参加</button>
  <ul id="join_member" style="display: flex;flex-flow: column;"></ul>
</div><!-- /.member_list -->
<?php require_once "../public/modal/modal-select.php" ?>
<script src="../src/js/ajax.js"></script>
<script defer src="../src/js/modal.js"></script>
<script src="../src/js/date.js"></script>
