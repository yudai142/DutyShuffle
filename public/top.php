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


    <!-- レコード追加 -->
    <div class="add_products">
        <!-- 入力フォーム -->
        作業名:<input id="name" required><br>
        参加人数:<input  id="multiple" type="number" min=1 value=1 required><br>
        有効/無効:<input id="archive" type="checkbox" value=1 >
        <!-- レコード追加後に入力内容を表示 -->
        <div id="add_result">
        </div>
        <!-- 送信ボタン -->
        <button id="ajax_add">追加ボタン</button>
    </div>
    <!-- レコード全件取得 -->
    <!-- 追加したレコードも即時反映される -->
    <table border="1" id="all_show_result">
        <tr>
            <th>id</th><th>作業名</th><th>人数</th><th>有効/無効</th>
        </tr>
    </table>
    <script src="../src/js/ajax.js"></script>