$(function(){
  $(".md-btn").each(function(){
    $(this).on('click',function(e){
      e.preventDefault();
      var target = $(this).data('target');
      var modal = document.getElementById(target);
      $(modal).find('.modal-container').fadeIn();
    });
  });
  $('.md-close').on('click',function(){
    $('.modal-container').fadeOut();
  });
});