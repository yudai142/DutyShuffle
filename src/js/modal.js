$(document).on('click', '.md-btn', function(e) {
  e.preventDefault();
  const target = $(this).data('target');
  const modal = document.getElementById(target);
  if(target == "modal-work"){
    if($(this).val()){
      $(modal).find('h1').text("作業編集");
      $(modal).find('#submit_work').text("変更");
      $.ajax({
        url: "../classes/ajax.php",
        datatype: "json",
        data: {
          "type": 'work_edit',
          "id" : $(this).val()
        },
        success: function(data) {
          if (!data["err"]){
            $(modal).find('#name').val(data[0].name);
            $(modal).find('#multiple').val(data[0].multiple);
            (Number(data[0].archive)) ? $(modal).find('#work_archive').prop("checked", true) : $(modal).find('#work_archive').prop("checked", false);
            $(modal).find('#work_result p').remove();
            $(modal).find('#work_id').remove();
            $(modal).find('form').append(`<input type='hidden' id=work_id value=${data[0].id}>`);
          }else{
            $(modal).find('#work_result').html(`<p>${data["err"]}</p>`);
          }
        },
        error: function(data) {
          $(modal).find('#work_result').html("<p>通信エラー</p>");
          console.log("通信失敗");
          console.log(data);
        }
    });
    }else{
      $(modal).find('h1').text("作業登録");
      $(modal).find('#name').val("");
      $(modal).find('#multiple').val(1);
      $(modal).find('#work_archive').prop("checked", false);
      $(modal).find('#work_result p').remove();
      $(modal).find('#work_id').remove();
      $(modal).find('#submit_work').text("追加");
    }
  }else if(target == "modal-member"){
    if($(this).val()){
      $(modal).find('h1').text("メンバー編集");
      $(modal).find('#submit_member').text("変更");
      $.ajax({
        url: "../classes/ajax.php",
        datatype: "json",
        data: {
          "type": 'member_edit',
          "id" : $(this).val()
        },
        success: function(data) {
          if (!data["err"]){
            $(modal).find('#last_name').val(data[0].last_name);
            $(modal).find('#first_name').val(data[0].first_name);
            $(modal).find('#kana_name').val(data[0].kana_name);
            (Number(data[0].archive)) ? $(modal).find('#member_archive').prop("checked", true) : $(modal).find('#member_archive').prop("checked", false);
            $(modal).find('#member_result p').remove();
            $(modal).find('#member_id').remove();
            $(modal).find('form').append(`<input type='hidden' id=member_id value=${data[0].id}>`);
          }else{
            $(modal).find('#member_result').html(`<p>${data["err"]}</p>`);
          }
        },
        error: function(data) {
          $(modal).find('#member_result').html("<p>通信エラー</p>");
          console.log("通信失敗");
          console.log(data);
        }
      });
    }else{
      $(modal).find('h1').text("メンバー登録");
      $(modal).find('#last_name').val("");
      $(modal).find('#first_name').val("");
      $(modal).find('#kana_name').val("");
      $(modal).find('#member_archive').prop("checked", false);
      $(modal).find('#member_result p').remove();
      $(modal).find('#member_id').remove();
      $(modal).find('#submit_member').text("追加");
    }
  }else if(target == "modal-select"){
    if($(this).hasClass('work')){
      $.ajax({
        url: "../classes/ajax.php",
        datatype: "json",
        data: {
          "type": 'member_select_work',
          "day": $("#date").val(),
          "work_id" : $(this).val()
        },
        success: function(data) {
          if (!data["err"]){
            console.log(data)
            // let arr = [];
            // $.each(data, function(key, value){
            //   arr.push(`<li id=member_${value.id}><span><input type='checkbox' value='${value.id}'${value.checked}>：<span>${value.last_name}　${value.first_name}<li>`);
            // });
            // $('#select_list').html(arr);
            $(modal).find('h1').text(`${data[0]["name"]}に参加するメンバーの選択`);
            $(modal).find('#submit_select').text("確定");
            $(modal).find('#select_result p').remove();
            $(modal).find('#select_work_id').remove();
            $(modal).find('form').append(`<input type='hidden' id=select_work_id value=${data[0]["id"]}>`);
          }else{
            $('#select_list').html(`<p>${data["err"]}</p>`);
          }
        },
        error: function(data) {
          $(modal).find('#select_list').html("<p>通信エラー</p>");
          console.log("通信失敗");
          console.log(data);
        }
      });
    }else{
      $(modal).find('h1').text("参加メンバー選択");
      $(modal).find('#submit_select').text("確定");
      $(modal).find('#select_result p').remove();
      $.ajax({
        url: "../classes/ajax.php",
        datatype: "json",
        data: {
          "type": 'member_select_list',
          "day": $("#date").val()
        },
        success: function(data) {
          if (!data["err"]){
            let arr = [];
            $.each(data, function(key, value){
              arr.push(`<li id=member_${value.id}><span><input type='checkbox' value='${value.id}'${value.checked}>：<span>${value.last_name}　${value.first_name}<li>`);
            });
            $('#select_list').html(arr);
          }else{
            $('#select_list').html(`<p>${data["err"]}</p>`);
          }
        },
        error: function(data) {
          $(modal).find('#select_list').html("<p>通信エラー</p>");
          console.log("通信失敗");
          console.log(data);
        }
      });
    }
  }
  $(modal).find('.modal-container').fadeIn();
});
$('.md-close').on('click',function(){
  $('.modal-container').fadeOut();
});
