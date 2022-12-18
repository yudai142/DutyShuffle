<?php date_default_timezone_set('Asia/Tokyo');(isset($_GET['ym']))? $title = "トップ(".date('Y/m/d',  strtotime($_GET['ym'])).")" : $title = "トップ(".date('Y/m/d').")" ; require_once "../component/head_component.php" ?>
<link rel="stylesheet" href="../src/css/top.css" />
<link rel="stylesheet" href="../src/css/select.css" />
<?php $title = "トップ"; require_once "../component/sidebar_component.php"; ?>
<?php
date_default_timezone_set('Asia/Tokyo');

//前月・次月リンクが選択された場合は、GETパラメーターから年月を取得
if(isset($_GET['ym'])){ 
    $ym = date("Y-m-d",strtotime($_GET['ym']));
    $select_date = date('Y-m-d', strtotime($_GET['ym']));
    $timestamp = strtotime($ym);
}else{
    //今月の年月を表示
    $ym = date('Y-m-d');
    //タイムスタンプ（どの時刻を基準にするか）を作成し、フォーマットをチェックする
    //strtotime('Y-m-01')
    $timestamp = strtotime($ym); 
    if($timestamp === false){//エラー対策として形式チェックを追加
        //falseが返ってきた時は、現在の年月・タイムスタンプを取得
        $ym = date('Y-m-d');
        $timestamp = strtotime($ym);
        $select_date = date('Y-m-d',$timestamp);
    }
}
//今月の日付　フォーマット　例）2020-10-2
$today = date('Y-m-d');
//前月・次月の年月を取得
//strtotime(,基準)
$prev = date('Y-m-d', strtotime('-1 day', $timestamp));
$next = date('Y-m-d', strtotime('+1 day', $timestamp));
?>
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
  <?php if(isset($_REQUEST["ym"])): ?>
    <span style="position: relative;"><a href="./allocation.php?ym=<?php echo $_REQUEST["ym"]; ?>" class="login-button blue-button">割り当てへ</a></span>
  <?php else:?>
    <span style="position: relative;"><a href="./allocation.php" class="login-button blue-button">割り当てへ</a></span>
  <?php endif;?>
</div>

<div class="main_visual">
  <div class="join_container join_container-work">
    <div class="join_header">
      <h1>作業</h1>
      <!-- <div class="button_collection">
        <p>割り当て枠数合計：3枠</p>
        <p>参加人数合計：3人</p>
        <p>余り人数・不足枠：2人</p>
      </div> -->
    </div>
    <div class="join_form">
      <ul id="join_work">
        <!-- <li><div>+　参加人数：３人　-</div><div class="button work square">ハンディモップ</div></li> -->
      </ul>
    </div><!-- /.join-form -->
  </div><!-- /.join -->
  <div class="join_container join_container-member">
    <div class="join_header">
      <h1>メンバー</h1>
      <div class="button_collection">
        <!-- <span class="btn yellow">クリア</span> -->
        <span class="btn green md-btn" data-target="modal-select">参加</span><!-- /.btn green -->
      </div><!-- /.button_collection -->
    </div><!-- /.join_header -->
    <div class="join_form">
      <ul id="join_member">
        <!-- <li><div class="button member">ハンディモップ</div></li> -->
      </ul>
    </div><!-- /.join-form -->
  </div><!-- /.join-work -->
</div>

<!-- <div class="join_work">
  <ul id="join_work" style="display:flex;margin-bottom:30px;"></ul>
</div>
<div class="join_member">
  <button class="md-btn" data-target="modal-select">参加</button>
  <ul id="join_member" style="display: flex;flex-flow: column;"></ul>
</div> -->

<?php require_once "../public/modal/modal-select.php" ?>
<script src="../src/js/ajax.js"></script>
<script defer src="../src/js/modal.js"></script>
<script src="../src/js/date.js"></script>
