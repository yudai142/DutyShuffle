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
  }else if ( location.pathname.indexOf("/allocation.php") != -1 ){
    allocationView();
  }else if ( location.pathname.indexOf("/option.php") != -1 ){
    getOptionList();
  }
  
  function allocationView(){
    $.ajax({
      type: "POST",
      url: "../classes/ajax.php",
      datatype: "json",
      data: {
        "type": 'allocation_list',
        "date": $("#date").val()
      },
      success: function(data) {
        if (data == null){
          $('#allocation-form').html("");
        }else if(data["err"] == null){
          let arr = []
          $.each(data[0], function(work_key, work_value){
            if(data[1] != null){
              let member = data[1].filter(value => {if(value.work_id == work_value.id){return true;}});
              let style = (work_value.status == 1)? `style="color:blue;"` : `style="color:blue;background:yellow;"`
              let list = [];
              $.each(member, function(member_key, member_value){
                list.push(`
                  <li class="select-member" id="history_${member_value.history_id}"><button class="select-member-button" value="${member_value.history_id}" style="color:red;">${member_value.family_name}　${member_value.given_name}</button></li>
                `);
              });
              arr.push(`
                <div class="content" style="display:flex;flex-flow: column;white-space: nowrap;">
                  <div class="work-title"><button class="md-btn" data-target="modal-select" data-type="work" value="${work_value.id}" ${style}">${work_value.name}</button></div>
                  <ul class="work-member">
                    ${list.join("")}
                  </ul>
                </div>
              `);
            }else{
              arr.push(`
                <div class="content" style="display:flex;flex-flow: column;">
                  <div class="work-title"><button class="md-btn" data-target="modal-select" ${style}>${work_value.name}</button></div>
                  <ul class="work-member"></ul>
                </div>
              `);
            }
          });
          if(data[1] != null){
            let null_member = data[1].filter(value => {if(value.work_id == null){return true;}});
            let null_list = [];
            $.each(null_member, function(null_key, null_value){
              null_list.push(`
                <li class="select-member" id="history_${null_value.history_id}"><button class="select-member-button" value="${null_value.history_id}" style="color:red;">${null_value.family_name}　${null_value.given_name}</button></li>
              `);
            });
            $('#null-member-list').html(null_list);
          }
          $('#allocation-form').html(arr);
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
  }
  
  function joinMember(){
    $.ajax({
      url: "../classes/ajax.php",
      data: {
        "type": 'join_member',
        "date": $("#date").val()
      },
      success: function(data) {
        if (data == null){
          $('#join_member').html("");
        }else if(data['err'] == null){
          let arr = [];
          $.each(data, function(key, value){
            let work_name = (value.work_name != null)?`<br><span>${value.work_name}を担当しています</span>`:"";
            arr.push(`<li id=join_member_${value.history_id}><button class='state-btn' data-target='remove-member' value=${value.history_id}>${value.family_name}　${value.given_name}</button>${work_name}<li>`);
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
    $.ajax({
      url: "../classes/ajax.php",
      data: {
        "type": 'join_work',
        "date": $("#date").val()
      },
      success: function(data) {
        if (data == null){
          false
        }else if(data['err'] == null){
          let arr = [];
          $.each(data, function(key, value){
            let style = (value.status == 1)? "" : `style="background:yellow;"`
            arr.push(`<li id=join_work_${value.id}><button class='state-btn' data-target='work-change' ${style} value=${value.id}>${value.name}</button><li>`);
          });
          $('#join_work').html(arr);
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
          $('#member_show_result').html("");
        }else if(data['err'] == null){
          let arr = []
          $.each(data, function(key, value){
            arr.push(`<li id=member_${value.id}><button class='md-btn' data-target='modal-member' value=${value.id}>${value.family_name}　${value.given_name}</button><li>`);
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
          $('#work_show_result').html("")
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

  function getOptionList(){
    $.ajax({
      url: "../classes/ajax.php",
      data: {
        "type": 'option_list'
      },
      success: function(data) {
        if (data == null){
          $('#option_list').html("")
        }else if(data['err'] == null){
          console.log(data);
          return
          let member_list = []
          let work_list = []
          $.each(data, function(key, value){
              arr.push(`<li id=work_${value.id}><button class='md-btn' data-target='modal-work' value=${value.id}>${value.name}</button><li>`);
          });
          $('#option_list').html(arr)
        }else{
          $('#option_list').append(`<p>${data["err"]}</p>`);
        }
      },
      error: function(){
        $('#option_list').append("<p>通信エラー</p>");
        console.log("通信失敗");
        console.log(data);
      }
    });
  }

  $('#submit_member').on('click',function(){
    let err = [];
    if ($('#family_name').val() == "") err.push("性");
    if ($('#given_name').val() == "") err.push("名");
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
            "family_name" : $('#family_name').val(),
            "given_name" : $('#given_name').val(),
            "kana_name" : $('#kana_name').val(),
            "archive" : Number($('#member_archive').prop("checked"))
          },
          success: function(data) {
            if (!data["err"]){
              $('#member_result').html(`<p>${data[0].family_name}${data[0].given_name}(${data[0].kana_name})${data[0].archive}を更新しました。</p>`);
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
            "family_name" : $('#family_name').val(),
            "given_name" : $('#given_name').val(),
            "kana_name" : $('#kana_name').val(),
            "archive" : Number($('#member_archive').prop("checked"))
          },
          success: function(data) {
            if (!data["err"]){
              $('#member_result').html(`<p>${data[0].family_name}${data[0].given_name}(${data[0].kana_name})${data[0].archive}を登録しました。</p>`);
              $('#family_name').val("");
              $('#given_name').val("");
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
      if($(this).attr('data-type') == 'work'){
        data_type = "member_select_work_definition"
        work_id = $("#select_work_id").val()
      }else{
        data_type = "member_select_definition"
        work_id = null
      }
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
      //         if (confirm(`${value.family_name}　${value.given_name}さんは他のタスクに入っていますが、除外してもよろしいですか？`)) {
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
          "type": data_type,
          "select": check,
          "date": $("#date").val(),
          work_id
        },
        success: function(data) {
          if (data == null || !data["err"]){
            if( location.pathname.indexOf("/allocation.php") != -1 ){
              allocationView();
            }else if( location.pathname.indexOf("/top.php") != -1 ){
              joinMember();
            }
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

  $(document).on('click', '.select-work', function(){
    let err = [];
    if (err.length) {
      $('#select_result').html(`<p>${err.join("と")}が入力されていません</p>`);
    }else{
      $.ajax({
        type: "POST",
        url: "../classes/ajax.php",
        datatype: "json",
        data: {
          "type": "work_select_definition",
          "select_work": $(this).attr("value"),
          "history_id": $(this).closest(".select-member").find(".select-member-button").attr("value"),
          "date": $("#date").val(),
          "check-copy" : Number($('#check-copy').prop("checked"))
        },
        success: function(data) {
          if (data == null || !data["err"]){
            allocationView();
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
  })
  $(document).on('click', '.state-btn', function(){
    if ($(this).attr("data-target") == "work-change") {
      $.ajax({
        type: "POST",
        url: "../classes/ajax.php",
        datatype: "json",
        data: {
          "type": "work-change",
          "work_id": $(this).attr("value"),
          "date": $("#date").val()
        },
        success: function(data) {
          if (data == null || !data["err"]){
            if( location.pathname.indexOf("/top.php") != -1 ){
              joinWork();
            }else if ( location.pathname.indexOf("/allocation.php") != -1 ){
              allocationView();
              if(data=="1"){
                $('#bool-check').find('.state-btn').attr('style','background:yellow;')
                $('#bool-check').find('.state-btn').text('シャッフルの対称にする')
              }else{
                $('#bool-check').find('.state-btn').attr('style','')
                $('#bool-check').find('.state-btn').text('シャッフルの非対称にする')
              }
            }
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
    }else if ($(this).attr("data-target") == "remove-member") {
      let alert_text = "";
      let add_text = "";
      if( location.pathname.indexOf("/top.php") != -1 ){
        alert_text = `${$(this).text()}さんを不参加にしますか？`
        add_text = ($(this).closest(`#join_member_${$(this).attr("value")}`).find('span').text() == 0)? "":`\n${$(this).closest(`#join_member_${$(this).attr("value")}`).find('span').text().slice( 0, -8 )}の担当も削除されます`;
      }else if ( location.pathname.indexOf("/allocation.php") != -1 ){
        alert_text = `${$(this).closest(".select-member").find(".select-member-button").text()}さんを不参加にしますか？`
        add_text = ($(this).closest('.content').find('.md-btn').text() == 0)? "":`\n${$(this).closest('.content').find('.md-btn').text()}の担当も削除されます`;
      }
      
      if (confirm(`${alert_text}${add_text}`)) {
        $.ajax({
          type: "POST",
          url: "../classes/ajax.php",
          datatype: "json",
          data: {
            "type": "join_member_remove",
            "history_id": $(this).attr("value")
          },
          success: function(data) {
            if (data == null || !data["err"]){
              if( location.pathname.indexOf("/top.php") != -1 ){
                joinMember();
              }else if ( location.pathname.indexOf("/allocation.php") != -1 ){
                allocationView();
              }
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
    }else if ($(this).attr("data-target") == "allocation-remove") {
      if (confirm(`割り当て済みの担当を全て解除します。よろしいでしょうか？`)) {
        $.ajax({
          type: "POST",
          url: "../classes/ajax.php",
          datatype: "json",
          data: {
            "type": "allocation-remove",
            "date": $("#date").val()
          },
          success: function(data) {
            if (data == null || !data["err"]){
              if( location.pathname.indexOf("/top.php") != -1 ){
                joinMember();
              }else if ( location.pathname.indexOf("/allocation.php") != -1 ){
                allocationView();
              }
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
    }else if ($(this).attr("data-target") == "shuffle_btn") {
      $.ajax({
        type: "POST",
        url: "../classes/ajax.php",
        datatype: "json",
        data: {
          "type": "shuffle",
          "date": $("#date").val()
        },
        success: function(data) {
          if (data == null || !data["err"]){
            if( location.pathname.indexOf("/top.php") != -1 ){
              joinMember();
            }else if ( location.pathname.indexOf("/allocation.php") != -1 ){
              allocationView();
            }
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
  })
});
