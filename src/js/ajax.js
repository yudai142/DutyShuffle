$(function(){
  // レコードを全件表示する
  // 試しに関数にしてみただけ
  function getAllData(){
      $.ajax({
          // 通信先ファイル名
          url: "../classes/ajax_all.php",
          // 通信が成功した時
          success: function(data) {
              // 取得したレコードをeachで順次取り出す
              $.each(data, function(key, value){
                  // #all_show_result内にappendで追記していく
                  $('#all_show_result').append("<tr><td>" + value.id + "</td><td>" + value.name + "</td><td>" + value.multiple + "</td><td>" + value.archive + "</td></tr>");
              });
  
              console.log("通信成功");
              console.log(data);
          },
  
          // 通信が失敗した時
          error: function(){
              console.log("通信失敗");
              console.log(data);
          }
      });
  }
  
  // 関数を実行
  getAllData();
  
  // #ajax_addがクリックされた時の処理
  $('#ajax_add').on('click',function(){
      // 確認メッセージを表示
      // OKならtrue,キャンセルならfalseが代入される
      // var confirmResult = window.confirm("登録してもよろしいですか？");
      var confirmResult = true;
  
      if(confirmResult) {
          $.ajax({
              // 送信方法
              type: "POST",
              // 送信先ファイル名
              url: "../classes/ajax.php",
              // 受け取りデータの種類
              datatype: "json",
              // 送信データ
              data: {
                  // #nameと#priceのvalueをセット
                  "name" : $('#name').val(),
                  "multiple" : $('#multiple').val(),
                  "archive" : Number($('#archive').prop("checked"))
              },
              // 通信が成功した時
              success: function(data) {
                  $('#add_result').html("<p>" + data[0].name + "が" + data[0].multiple + "人の" + data[0].archive + "のデータを登録しました。</p>");
  
                  console.log("通信成功");
                  console.log(data);
  
                  // 一覧に追加したレコードを追記
                  $.each(data, function(key, value){
                      $('#all_show_result').append("<tr><td>" + value.id + "</td><td>" + value.name + "</td><td>" + value.multiple + "</td><td>" + value.archive + "</td></tr>");
                  });
              },
  
              // 通信が失敗した時
              error: function(data) {
                  console.log("通信失敗");
                  console.log(data);
              }
          });
      }
  
      return false;
  });
  });