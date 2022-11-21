$(function(){
  $(".md-btn").each(function(){
    $(this).on('click',function(e){
      e.preventDefault();
      var target = $(this).data('target');
      var modal = document.getElementById(target);
      if(target == "modal-work"){
        if($(this).val()){
          $(modal).find('h1').text("作業編集");
        }else{
          $(modal).find('h1').text("作業登録");
          $(modal).find('#name').val("");
          $(modal).find('#multiple').val(1);
          $(modal).find('#work_archive').removeAttr("checked").prop("checked", false).change();
          $(modal).find('#work_result p').remove();
          $(modal).find('#submit_work').text("追加");
        }
      }else if(target == "modal-member"){
        if($(this).val()){
          $(modal).find('h1').text("メンバー編集");
        }else{
          $(modal).find('h1').text("メンバー登録");
          $(modal).find('#last_name').val("");
          $(modal).find('#first_name').val("");
          $(modal).find('#kana_name').val("");
          $(modal).find('#member_archive').removeAttr("checked").prop("checked", false).change();
          $(modal).find('#member_result p').remove();
          $(modal).find('#submit_member').text("追加");
        }
      }
      $(modal).find('.modal-container').fadeIn();
    });
  });
  $('.md-close').on('click',function(){
    $('.modal-container').fadeOut();
  });
});