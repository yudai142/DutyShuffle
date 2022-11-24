$(function(){

  function getAllMember(){
    $.ajax({
      url: "../classes/ajax.php",
      data: {
        "type": 'member_list',
      },
      success: function(data) {
          $.each(data, function(key, value){
            $('#member_show_result').append("<li id=member_" + value.id + "><button class='md-btn' data-target='modal-member' value=" + value.id + ">" + value.last_name + "　" + value.first_name + "</button><li>");
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
              $('#work_show_result').append("<li id=work_" + value.id + "><button class='md-btn' data-target='modal-work' value=" + value.id + ">" + value.name + "</button><li>");
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

  $('#submit_member').on('click',function(){
    if($("#member_id").val()){
      let err = [];
      if ($('#last_name').val() == "") err.push("性");
      if ($('#first_name').val() == "") err.push("名");
      if ($('#kana_name').val() == "") err.push("ふりがな");
      if (err.length) {
        $('#member_result').html("<p>" + err.join("、") + "が不正です</p>");
      }else{
        $.ajax({
          type: "POST",
          url: "../classes/ajax.php",
          datatype: "json",
          data: {
            "type": 'member_update',
            "id" : $("#member_id").val(),
            "last_name" : $('#last_name').val(),
            "first_name" : $('#first_name').val(),
            "kana_name" : $('#kana_name').val(),
            "archive" : Number($('#member_archive').prop("checked"))
          },
          success: function(data) {
            $('#member_result').html("<p>" + data[0].last_name + data[0].first_name + "("+data[0].kana_name + ")" + data[0].archive + "を変更しました。</p>");
            $('#member_show_result').find("#member_" + data[0].id).html("<button class='md-btn' data-target='modal-member' value=" + data[0].id + ">" + data[0].last_name + "　" + data[0].first_name + "</button>");
          },
          error: function(data) {
            $('#member_result').html("<p>入力エラー</p>");
            console.log("通信失敗");
            console.log(data);
          }
        });
      }
    }else{
      let err = [];
      if ($('#last_name').val() == "") err.push("性");
      if ($('#first_name').val() == "") err.push("名");
      if ($('#kana_name').val() == "") err.push("ふりがな");
      if (err.length) {
        $('#member_result').html("<p>" + err.join("、") + "が不正です</p>");
      }else{
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
            $('#member_show_result').append("<li id=member_" + data[0].id + "><button class='md-btn' data-target='modal-member' value=" + data[0].id + ">" + data[0].last_name + "　" + data[0].first_name + "</button><li>");
          },
          error: function(data) {
            $('#member_result').html("<p>入力エラー</p>");
            console.log("通信失敗");
            console.log(data);
          }
        });
      }
    }
    // return false
  });
  
  $('#submit_work').on('click',function(){
    if($("#work_id").val()){
      let err = [];
      if ($('#name').val() == "") err.push("作業名");
      if ($('#multiple').val() == "" || $('#multiple').val() == 0) err.push("参加人数");
      if (err.length) {
        $('#work_result').html("<p>" + err.join("、") + "が不正です</p>");
      }else{
        $.ajax({
            type: "POST",
            url: "../classes/ajax.php",
            datatype: "json",
            data: {
                "type": 'work_update',
                "id" : $("#work_id").val(),
                "name" : $('#name').val(),
                "multiple" : $('#multiple').val(),
                "archive" : Number($('#work_archive').prop("checked"))
            },
            success: function(data) {
              $('#work_result').html("<p>" + data[0].name + "が" + data[0].multiple + "人の" + data[0].archive + "のデータを変更しました。</p>");
              $('#work_show_result').find("#work_" + data[0].id).html("<button class='md-btn' data-target='modal-work' value=" + data[0].id + ">" + data[0].name + "</button>");
            },
            error: function(data) {
                $('#work_result').html("<p>入力エラー</p>");
                console.log("通信失敗");
                console.log(data);
            }
        });
      }
    }else{
      let err = [];
      if ($('#name').val() == "") err.push("作業名");
      if ($('#multiple').val() == "") err.push("参加人数");
      if (err.length) {
        $('#work_result').html("<p>" + err.join("、") + "が不正です</p>");
      }else{
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
              $('#work_result').html("<p>入力エラー</p>");
              console.log("通信失敗");
              console.log(data);
            }
        });
      }
    }
    // return false
  });
});
