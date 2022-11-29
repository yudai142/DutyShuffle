<?php require_once "../component/head_component.php" ?>
<link rel="stylesheet" href="../src/css/top.css" />
<?php $title = "トップ"; require_once "../component/sidebar_component.php"; ?>
<div class="date-form">
  <div class="icon-left">
    <a href="?ym=<?php echo $prev; ?>">
      <i class="fas fa-arrow-alt-circle-left fa-2x"></i>
    </a>
  </div>
  <label>
    <input type="date" id="month" value="<?php echo $ym ?>" />
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

