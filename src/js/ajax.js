$(function(){
  // レコードを全件表示する
  // 試しに関数にしてみただけ
  function getAllData(){
    $.ajax({
      url: "../classes/ajax_all.php",
      data: {
        "type": 'work_list',
      },
      success: function(data) {
          $.each(data, function(key, value){
              $('#all_show_result').append("<tr><td>" + value.id + "</td><td>" + value.name + "</td><td>" + value.multiple + "</td><td>" + value.archive + "</td></tr>");
          });
      },
      error: function(){
          console.log("通信失敗");
          console.log(data);
      }
    });
  }
  getAllData();
  
  // #ajax_addがクリックされた時の処理
  $('#ajax_add').on('click',function(){
    $.ajax({
        type: "POST",
        url: "../classes/ajax.php",
        datatype: "json",
        data: {
            "type": 'work_add',
            "name" : $('#name').val(),
            "multiple" : $('#multiple').val(),
            "archive" : Number($('#archive').prop("checked"))
        },
        success: function(data) {
          $('#add_result').html("<p>" + data[0].name + "が" + data[0].multiple + "人の" + data[0].archive + "のデータを登録しました。</p>");
          $('#all_show_result').append("<tr><td>" + data[0].id + "</td><td>" + data[0].name + "</td><td>" + data[0].multiple + "</td><td>" + data[0].archive + "</td></tr>"); 
        },
        error: function(data) {
            console.log("通信失敗");
            console.log(data);
        }
    });
    // return false

  });
});