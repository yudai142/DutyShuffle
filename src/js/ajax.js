$(function(){

  function getAllMember(){
    $.ajax({
      url: "../classes/ajax.php",
      data: {
        "type": 'member_list',
      },
      success: function(data) {
          $.each(data, function(key, value){
            $('#member_show_result').append("<li><button class='md-btn' data-target='modal-member' value=" + value.id + ">" + value.last_name + "　" + value.first_name + "</button><li>");
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
              $('#work_show_result').append("<li><button class='md-btn' data-target='modal-work' value=" + value.id + ">" + value.name + "</button><li>");
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
  
  $('#submit_work').on('click',function(){
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
          $('#work_show_result').append("<li><button class='md-btn' data-target='modal-work' value=" + data[0].id + ">" + data[0].name + "</button><li>"); 
        },
        error: function(data) {
            console.log("通信失敗");
            console.log(data);
        }
    });
    // return false
  });
});
