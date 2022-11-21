$(function(){

  function getAllMember(){
    $.ajax({
      url: "../classes/ajax.php",
      data: {
        "type": 'member_list',
      },
      success: function(data) {
          $.each(data, function(key, value){
            $('#member_show_result').append("<tr><td>" + value.id + "</td><td>" + value.last_name + "</td><td>" + value.first_name + "</td><td>" + value.kana_name + "</td><td>" + value.archive + "</td></tr>"); 
          });
      },
      error: function(){
          console.log("通信失敗");
          console.log(data);
      }
    });
    // return false
  }
  getAllMember()
  // レコードを全件表示する
  // 試しに関数にしてみただけ
  function getAllWork(){
    $.ajax({
      url: "../classes/ajax.php",
      data: {
        "type": 'work_list',
      },
      success: function(data) {
          $.each(data, function(key, value){
              $('#work_show_result').append("<tr><td>" + value.id + "</td><td>" + value.name + "</td><td>" + value.multiple + "</td><td>" + value.archive + "</td></tr>");
          });
      },
      error: function(){
          console.log("通信失敗");
          console.log(data);
      }
    });
    // return false
  }
  getAllWork();
  
  $('#work_add').on('click',function(){
    $.ajax({
        type: "POST",
        url: "../classes/ajax.php",
        datatype: "json",
        data: {
            "type": 'work_add',
            "name" : $('#name').val(),
            "multiple" : $('#multiple').val(),
            "archive" : Number($('#work_archive').prop("checked"))
        },
        success: function(data) {
          $('#work_result').html("<p>" + data[0].name + "が" + data[0].multiple + "人の" + data[0].archive + "のデータを登録しました。</p>");
          $('#work_show_result').append("<tr><td>" + data[0].id + "</td><td>" + data[0].name + "</td><td>" + data[0].multiple + "</td><td>" + data[0].archive + "</td></tr>"); 
        },
        error: function(data) {
            console.log("通信失敗");
            console.log(data);
        }
    });
    // return false
  });

  $('#member_add').on('click',function(){
    $.ajax({
        type: "POST",
        url: "../classes/ajax.php",
        datatype: "json",
        data: {
            "type": 'member_add',
            "last_name" : $('#last_name').val(),
            "first_name" : $('#first_name').val(),
            "kana_name" : $('#kana_name').val(),
            "archive" : Number($('#member_archive').prop("checked"))
        },
        success: function(data) {
          $('#member_result').html("<p>" + data[0].last_name + data[0].first_name + "("+data[0].kana_name + ")" + data[0].archive + "を登録しました。</p>");
          $('#member_show_result').append("<tr><td>" + data[0].id + "</td><td>" + data[0].last_name + "</td><td>" + data[0].first_name + "</td><td>" + data[0].kana_name + "</td><td>" + data[0].archive + "</td></tr>"); 
        },
        error: function(data) {
            console.log("通信失敗");
            console.log(data);
        }
    });
    // return false
  });
});