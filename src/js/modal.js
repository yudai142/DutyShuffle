$(document).on('click', '.md-btn', function(e) {
  e.preventDefault();
  const target = $(this).attr('data-target');
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
            $(modal).find('#family_name').val(data[0].family_name);
            $(modal).find('#given_name').val(data[0].given_name);
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
      $(modal).find('#family_name').val("");
      $(modal).find('#given_name').val("");
      $(modal).find('#kana_name').val("");
      $(modal).find('#member_archive').prop("checked", false);
      $(modal).find('#member_result p').remove();
      $(modal).find('#member_id').remove();
      $(modal).find('#submit_member').text("追加");
    }
  }else if(target == "modal-select"){
    if($(this).attr('data-type') == 'work'){
      $(modal).find('#submit_select').text("確定");
      $(modal).find('#bool-check').html(`<input type="checkbox">：シャッフルの非対称にする`);
      $(modal).find('#submit_select').attr("data-type","work");
      $(modal).find('#select_result p').remove();
      $(modal).find('#select_work_id').remove();
      $.ajax({
        url: "../classes/ajax.php",
        datatype: "json",
        data: {
          "type": 'member_select_work',
          "date": $("#date").val(),
          "work_id" : $(this).val()
        },
        success: function(data) {
          if (!data["err"]){
            let arr = [];
            $.each(data[1], function(key, value){
              checked = (value.work_name == data[0]["name"]) ? "checked" : ""
              style = (value.work_name && value.work_name != data[0]["name"])?"style=color:green;":"";
              work_name = (value.work_name && value.work_name != data[0]["name"])?`<br><span style=color:orange;>${value.work_name}を担当しています</span>`:"";
              arr.push(`<li id=history_${value.history_id}><span><input type='checkbox' value='${value.history_id}'${checked}>：<span ${style}>${value.family_name}　${value.given_name}</span>${work_name}<li>`);
            });
            $('#select_list').html(arr);
            $(modal).find('h1').text(`${data[0]["name"]}に参加するメンバーの選択`);
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
      $(modal).find('#bool-check').html(`<button>選択全解除</button>`);
      $(modal).find('#submit_select').attr("data-type", "");
      $(modal).find('#select_result p').remove();
      $.ajax({
        url: "../classes/ajax.php",
        datatype: "json",
        data: {
          "type": 'member_select_list',
          "date": $("#date").val()
        },
        success: function(data) {
          if (!data["err"]){
            let arr = [];
            $.each(data, function(key, value){
              arr.push(`<li id=member_${value.id}><span><input type='checkbox' value='${value.id}'${value.checked}>：<span>${value.family_name}　${value.given_name}<li>`);
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

$(document).on('click', function(e) {
  if($(e.target).closest('.select-member-button').length && $(e.target).closest('.select-member').attr('id') != $('#checkboxes').closest('.select-member').attr('id')){
    let off = "#"+$('#checkboxes').closest('.select-member').attr('id');
    let add = "#"+$(e.target).closest('.select-member').attr('id');
    let button_value = $(e.target).closest('.select-member').find('.select-member-button').attr('value');
    if($('#checkboxes').length){
      $(off).find('#checkboxes').fadeOut().queue(function() {
        this.remove();
      });
    }
    $.ajax({
      type: "POST",
      url: "../classes/ajax.php",
      datatype: "json",
      data: {
        "type": 'work_select_list',
        "history_id": button_value
      },
      success: function(data) {
        if (data == null){
          $(add).append(`
            <div id="checkboxes">作業内容が登録されていません</div>
          `)
          $(add).find('#checkboxes').fadeIn();
        }else if(data["err"] == null){
          let list = [];
          $.each(data, function(work_key, work_value){
            list.push(`
              <div class="select-work" value="${work_value.id}">${work_value.name}</div>
            `);
          });
          $(add).append(`
            <div id="checkboxes">
              <div id="check-form">
                <span>移動先を選んでください</span>
                <br>
                <button><label for="check-copy"><input type="checkbox" name="check-copy" id="check-copy" />複製して追加</label></button>
                <button>削除</button>
              </div>
              ${list.join("")}
            </div>
          `)
          $(add).find('#checkboxes').fadeIn();
        }else{
          $('#allocation-form').html(`<p>${data["err"]}</p>`);
        }
      },
      error: function(data) {
        $('#allocation-form').html("<p>通信エラー</p>");
        console.log("通信失敗");
        console.log(data);
      }
    });
	}else if($('#checkboxes').length && !($(e.target).closest('#check-form').attr('id') == "check-form")){
    $('#checkboxes').fadeOut().queue(function() {
      this.remove();
    });
	}
});