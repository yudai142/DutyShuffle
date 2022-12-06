$(function($){
  if ( location.pathname.indexOf("/create-edit.php") !== -1 ){
    getAllMember();
    getAllWork();
    $('#member_view').change(function() {
      getAllMember();
    });
      
    $('#work_view').change(function() {
      getAllWork();
    });
  }else if ( location.pathname.indexOf("/top.php") != -1 ){
    joinMember();
    joinWork();
  }
  
  function joinMember(){
    $.ajax({
      url: "../classes/ajax.php",
      data: {
        "type": 'join_member',
        "day": $("#date").val()
      },
      success: function(data) {
        if (data == null){
          false
        }else if(data['err'] == null){
          let arr = [];
          $.each(data, function(key, value){
            arr.push(`<li id=join_member_${value.id}><button class='remove-btn' data-target='remove-member' value=${value.id}>${value.last_name}　${value.first_name}</button><li>`);
          });
          $('#join_member').html(arr);
        }else{
          $('#join_member').append(`<p>${data["err"]}</p>`);
        }
      },
      error: function(){
        $('#join_member').append("<p>通信エラー</p>");
        console.log("通信失敗");
        console.log(data);
      }
    });
  }
  function joinWork(){
    // $(this).val() ? $data = {"type": 'join_work'}: $data = {"type": 'join_work', day: $(this).val()}
    $.ajax({
      url: "../classes/ajax.php",
      data: {"type": 'join_work'},
      success: function(data) {
        if (data == null){
          false
        }else if(data['err'] == null){
          $.each(data, function(key, value){
            $('#join_work').append(`<li id=join_work_${value.id}><button class='off-btn' data-target='status-change' value=${value.id}>${value.name}</button><li>`);
          });
        }else{
          $('#join_work').append(`<p>${data["err"]}</p>`);
        }
      },
      error: function(){
        $('#join_work').append("<p>通信エラー</p>");
        console.log("通信失敗");
        console.log(data);
      }
    });
  }
  function getAllMember(){
    $.ajax({
      url: "../classes/ajax.php",
      data: {
        "type": 'member_list',
        "select": $('#member_view').val()
      },
      success: function(data) {
        if (data == null){
          false
        }else if(data['err'] == null){
          let arr = []
          $.each(data, function(key, value){
            arr.push(`<li id=member_${value.id}><button class='md-btn' data-target='modal-member' value=${value.id}>${value.last_name}　${value.first_name}</button><li>`);
            $('#member_show_result').html(arr);
          });
        }else{
          $('#member_show_result').append(`<p>${data["err"]}</p>`);
        }
      },
      error: function(){
        $('#member_show_result').append("<p>通信エラー</p>");
        console.log("通信失敗");
        console.log(data);
      }
    });
    // return false
  }
  
  function getAllWork(){
    $.ajax({
      url: "../classes/ajax.php",
      data: {
        "type": 'work_list',
        "select": $('#work_view').val()
      },
      success: function(data) {
        if (data == null){
          false
        }else if(data['err'] == null){
          let arr = []
          $.each(data, function(key, value){
              arr.push(`<li id=work_${value.id}><button class='md-btn' data-target='modal-work' value=${value.id}>${value.name}</button><li>`);
          });
          $('#work_show_result').html(arr)
        }else{
          $('#work_show_result').append(`<p>${data["err"]}</p>`);
        }
      },
      error: function(){
        $('#work_show_result').append("<p>通信エラー</p>");
        console.log("通信失敗");
        console.log(data);
      }
    });
    // return false
  }

  $('#submit_member').on('click',function(){
    let err = [];
    if ($('#last_name').val() == "") err.push("性");
    if ($('#first_name').val() == "") err.push("名");
    if ($('#kana_name').val() == "") err.push("ふりがな");
    if (err.length) {
      $('#member_result').html(`<p>${err.join("と")}が入力されていません</p>`);
    }else{
      if($("#member_id").val()){
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
            if (!data["err"]){
              $('#member_result').html(`<p>${data[0].last_name}${data[0].first_name}(${data[0].kana_name})${data[0].archive}を更新しました。</p>`);
              getAllMember();
            }else{
              $('#member_result').html(`<p>${data["err"]}</p>`);
            }
          },
          error: function(data) {
            $('#member_result').html("<p>入力エラー</p>");
            console.log("通信失敗");
            console.log(data);
          }
        });
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
            if (!data["err"]){
              $('#member_result').html(`<p>${data[0].last_name}${data[0].first_name}(${data[0].kana_name})${data[0].archive}を登録しました。</p>`);
              $('#last_name').val("");
              $('#first_name').val("");
              $('#kana_name').val("");
              $('#member_archive').prop("checked", false);
              getAllMember();
            }else{
              $('#member_result').html(`<p>${data["err"]}</p>`);
            }
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
    let err = [];
    if ($('#name').val() == "") err.push("作業名");
    if ($('#multiple').val() == "") err.push("参加人数");
    if (err.length) {
      $('#work_result').html(`<p>${err.join("と")}が入力されていません</p>`);
    }else{
      if($("#work_id").val()){
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
            if (!data["err"]){
              $('#work_result').html(`<p>${data[0].name}が${data[0].multiple}人の${data[0].archive}のデータを更新しました。</p>`);
              getAllWork();
            }else{
              $('#work_result').html(`<p>${data["err"]}</p>`);
            }
          },
          error: function(data) {
            $('#work_result').html("<p>入力エラー</p>");
            console.log("通信失敗");
            console.log(data);
          }
        });
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
            if (!data["err"]){
              $('#work_result').html(`<p>${data[0].name}が${data[0].multiple}人の${data[0].archive}のデータを登録しました。</p>`);
              $('#name').val("");
              $('#multiple').val(1);
              $('#work_archive').prop("checked", false);
              getAllWork();
            }else{
              $('#work_result').html(`<p>${data["err"]}</p>`);
            }
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
  
  $('#submit_select').on('click',function(){
    let err = [];
    if (err.length) {
      $('#select_result').html(`<p>${err.join("と")}が入力されていません</p>`);
    }else{
      let check = [];
      $("#select_list input[type=checkbox]:checked").each(function() {
        check.push($(this).val());
      });
      // $.ajax({
      //   type: "POST",
      //   url: "../classes/ajax.php",
      //   datatype: "json",
      //   data: {
      //     "type": 'member_select_check',
      //     "select": check,
      //     "day": $("#date").val()
      //   },
      //   success: function(data) {
      //     if (!data["err"]){
      //       $.each(data, function(key, value){
      //         if (confirm(`${value.last_name}　${value.first_name}さんは他のタスクに入っていますが、除外してもよろしいですか？`)) {
      //           return false;
      //         } else {
      //           check.push(value.id)
      //         }
      //       });
      //     }else{
      //       $('#select_result').html(`<p>${data["err"]}</p>`);
      //     }
      //   },
      //   error: function(data) {
      //     $('#select_result').html("<p>入力エラー</p>");
      //     console.log("通信失敗");
      //     console.log(data);
      //   }
      // });
      $.ajax({
        type: "POST",
        url: "../classes/ajax.php",
        datatype: "json",
        data: {
          "type": 'member_select_definition',
          "select": check,
          "day": $("#date").val()
        },
        success: function(data) {
          if (data == null){
            $('#join_member').html("")
            $('.modal-container').fadeOut();
          }else if(data['err'] == null){
            let arr = []
            $.each(data, function(key, value){
              arr.push(`<li id=join_member_${value.id}><button class='remove-btn' data-target='remove-member' value=${value.id}>${value.last_name}　${value.first_name}</button><li>`);
            });
            $('#join_member').html(arr)
            $('.modal-container').fadeOut();
          }else{
            $('#select_result').html(`<p>${data["err"]}</p>`);
          }
        },
        error: function(data) {
          $('#select_result').html("<p>入力エラー</p>");
          console.log("通信失敗");
          console.log(data);
        }
      });
    }
  });
});
