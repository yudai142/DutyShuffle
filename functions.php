<?php
  /**
   * XSS対策：エスケープ処理
   * htmlspecialchars()を使うことで対策可能
   * @param string $str 対象の文字列
   * @return string 処理された文字列
   */
  function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }

  /**
   * CSRF対策(偽装リクエスト対策)
   * ワンタイムトークンを利用して対策
   * @param void
   * @return string $csrf_token
   */
  function setToken() {
    // トークンを生成
    // フォームからそのトークンを送信
    // 送信後の画面でそのトークンを照会
    // トークンを削除
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;

    return $csrf_token;
  }
?>
