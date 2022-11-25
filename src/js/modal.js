$(document).on('click', '.md-btn', function(e) {
  e.preventDefault();
  var target = $(this).data('target');
  var modal = document.getElementById(target);
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
          $(modal).find('#name').val(data[0].name);
          $(modal).find('#multiple').val(data[0].multiple);
          (Number(data[0].archive)) ? $(modal).find('#work_archive').prop("checked", true) : $(modal).find('#work_archive').prop("checked", false);
          $(modal).find('#work_result p').remove();
          $(modal).find('#work_id').remove();
          $(modal).find('form').append("<input type='hidden' id=work_id value=" + data[0].id + ">");
        },
        error: function(data) {
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
          $(modal).find('#last_name').val(data[0].last_name);
          $(modal).find('#first_name').val(data[0].first_name);
          $(modal).find('#kana_name').val(data[0].kana_name);
          (Number(data[0].archive)) ? $(modal).find('#member_archive').prop("checked", true) : $(modal).find('#member_archive').prop("checked", false);
          $(modal).find('#member_result p').remove();
          $(modal).find('#member_id').remove();
          $(modal).find('form').append("<input type='hidden' id=member_id value=" + data[0].id + ">");
        },
        error: function(data) {
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
  }
  $(modal).find('.modal-container').fadeIn();
});
$('.md-close').on('click',function(){
  $('.modal-container').fadeOut();
});
