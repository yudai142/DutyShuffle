$(function($){
  if ( location.pathname.indexOf("/create-edit.php") !== -1 ){
    $("#create-edit_page").html("<p>登録・編集</p>");
    getAllMember();
    getAllWork();
    $('#member_view').change(function() {
      getAllMember();
    });
      
    $('#work_view').change(function() {
      getAllWork();
    });
  }else if ( location.pathname.indexOf("/top.php") != -1 ){
    $("#top_page").html("<p>トップ</p>");
    joinMember();
    joinWork();
  }else if ( location.pathname.indexOf("/allocation.php") != -1 ){
    $("#allocation_page").html("<p>割り当て</p>");
    allocationView();
  }else if ( location.pathname.indexOf("/option.php") != -1 ){
    $("#option_page").html("<p>オプション</p>");
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
            let style = (work_value.status == 1)? 'work' : 'off'
            if(data[1] != null){
              let member = data[1].filter(value => {if(value.work_id == work_value.id){return true;}});
              let list = [];
              $.each(member, function(member_key, member_value){
                list.push(`<div class="select-member-button" id="history_${member_value.history_id}" value="${member_value.history_id}"><div class="button member">${member_value.family_name}　${member_value.given_name}</div></div>`);
              });
              arr.push(`<li class="select-member"><div class="md-btn button ${style} square work-title content" data-target="modal-select" data-type="work" value="${work_value.id}">${work_value.name}</div>${list.join("")}</li>`);
            }else{
              arr.push(`<li class="select-member"><div class="md-btn button ${style} square work-title content" data-target="modal-select" data-type="work" value="${work_value.id}">${work_value.name}</div></li>
              `);
            }
          });
          if(data[1] != null){
            let null_member = data[1].filter(value => {if(value.work_id == null){return true;}});
            let null_list = [];
            $.each(null_member, function(null_key, null_value){
              null_list.push(`<li class="select-member"><div class="select-member-button" id="history_${null_value.history_id}" value="${null_value.history_id}"><div class="button member">${null_value.family_name}　${null_value.given_name}</div></div></li>`);
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
            let work_name = (value.work_name != null)?`<p>${value.work_name}</p>`:"";
            arr.push(`<li id=join_member_${value.history_id}><div class="button member state-btn" data-target='remove-member' value=${value.history_id}>${value.family_name}　${value.given_name}</div>${work_name}</li>`);
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
            let style = (value.status == 1)? 'work' : 'off'
            arr.push(`<li id=join_work_${value.id}><div class="button ${style} state-btn" data-target='work-change' value=${value.id}>${value.name}</div></li>`);
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
            arr.push(`<div id=member_${value.id} class="button member b-select md-btn" data-target='modal-member' value=${value.id}>${value.family_name}　${value.given_name}</div>`);
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
              arr.push(`<div id=work_${value.id} class="button work b-select md-btn" data-target='modal-work' value=${value.id}>${value.name}</div>`);
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
          let fixed_list = [];
          let exclusion_list = [];
          data[2].unshift({
            id:null,
            status: 0
          },{
            id:null,
            status: 1
          })
          $.each(data[2], function(option_key, option_value){
            let work_class = (option_value.id == null)?"add_option":"change_option";
            let work_list = $("<select>", {
              id: `works_${(option_value.id == null)?"new":option_value.id}`,
              name: 'works',
              class:`button work square ${work_class}`,
            })
            if(option_value.id == null){
              work_list.append($('<option>')
              .prop({
                hidden: true,
                text: "ーー"
              }))
            }
            for (const val of data[1]) {
              if(val["archive"] == 0 || val["id"] == option_value.work_id){
                let selected = (val["id"] == option_value.work_id)?true : false;
                $(work_list).append($('<option>')
                .prop({
                  value: val["id"],
                  text: val["name"],
                  selected: selected
                }))
              }
            }

            let member_class = (option_value.id == null)?"add_option":"change_option";
            let member_list = $("<select>", {
              id: `members_${(option_value.id == null)?"new":option_value.id}`,
              name: 'members',
              class:`button member square ${member_class}`
            })
            if(option_value.id == null){
              member_list.append($('<option>')
              .prop({
                hidden: true,
                text: "ーー"
              }))
            }
            for (const val of data[0]) {
              if(val["archive"] == 0 || val["id"] == option_value.member_id){
                let selected = (val["id"] == option_value.member_id)?true : false;
                $(member_list).append($('<option>')
                .prop({
                  value: val["id"],
                  text: `${val["family_name"]}　${val["given_name"]}`,
                  selected: selected
                }))
              }
            }
            let option_list = $("<form>", {onsubmit: "return false;"})
              .append($("<ul>", {class: "option-group"})
              .append($("<li>").append(work_list), $("<li>").append(member_list), $("<li>").append($("<div>",{
                text:(option_value.id == null)?"追加":"解除", 
                value: (option_value.id == null)?option_value.status:option_value.id,
                class: "button work state-btn",
                "data-target": (option_value.id == null)?"add-member_option":"delete-member_option"
              }))))
            if(option_value.status == 0){
              fixed_list.push(option_list);
            }else{
              exclusion_list.push(option_list);
            }
          });
          $('#fixed_list').html(fixed_list)
          $('#exclusion_list').html(exclusion_list)
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
              $('#member_result').html(`<p>${data[0].family_name}${data[0].given_name}(${data[0].kana_name})${(data[0].archive==0)?"有効":"無効"}を更新しました。</p>`);
              $('.modal-container').fadeOut();
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
              $('#member_result').html(`<p>${data[0].family_name}${data[0].given_name}(${data[0].kana_name})${(data[0].archive==0)?"有効":"無効"}を登録しました。</p>`);
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
              $('#work_result').html(`<p>${data[0].name}(${data[0].multiple}人)${(data[0].archive==0)?"有効":"無効"}に更新しました。</p>`);
              $('.modal-container').fadeOut();
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
              $('#work_result').html(`<p>${data[0].name}(${data[0].multiple}人)${(data[0].archive==0)?"有効":"無効"}を登録しました。</p>`);
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
                $('#bool-check').find('.state-btn').removeClass('work')
                $('#bool-check').find('.state-btn').addClass('off')
                $('#bool-check').find('.state-btn').text('シャッフルの対称にする')
              }else{
                
                $('#bool-check').find('.state-btn').removeClass('off')
                $('#bool-check').find('.state-btn').addClass('work')
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
        alert_text = `${$(this).closest(".select-member-button").find("p").text()}さんを不参加にしますか？`
        add_text = ($(this).closest('.select-member').find('.md-btn').text() == 0)? "":`\n${$(this).closest('.select-member').find('.md-btn').text()}の担当も削除されます`;
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
      if (confirm(`割り当て済みの担当を全て解除します。よろしいですか？`)) {
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
              // console.log(data)
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
    }else if ($(this).attr("data-target") == "add-member_option") {
      let err = [];
      if (isNaN($(this).closest('form').find('#works_new').val())) err.push("作業");
      if (isNaN($(this).closest('form').find('#members_new').val())) err.push("メンバー");
      if (err.length) {
        console.log(`${err.join("と")}が入力されていません`)
        $('#option_result').html(`<p>${err.join("と")}が入力されていません</p>`);
      }else{
        $.ajax({
          type: "POST",
          url: "../classes/ajax.php",
          datatype: "json",
          data: {
            "type": "add-member_option",
            "member_id": $(this).closest('form').find('#members_new').val(),
            "work_id": $(this).closest('form').find('#works_new').val(),
            "status": $(this).val()
          },
          success: function(data) {
            if (data == null || !data["err"]){
              getOptionList();
            }else{
              $('#select_result').html(`<p>${data["err"]}</p>`);
            }
          },
          error: function(data) {
            $('#select_result').html("<p>入力エラー</p>");
            console.log("通信失敗");
            console.log(data);
          }
        })
      }
    }else if ($(this).attr("data-target") == "delete-member_option") {
      let err = [];
      if (err.length) {
        console.log(`${err.join("と")}が入力されていません`)
        $('#option_result').html(`<p>${err.join("と")}が入力されていません</p>`);
      }else{
        $.ajax({
          type: "POST",
          url: "../classes/ajax.php",
          datatype: "json",
          data: {
            "type": "confirm-member_option",
            "option_id": $(this).val()
          },
          success: function(data) {
            if (data == null || !data["err"]){
              let status = (data[0].status == 0)?"固定":"除外";
              if (confirm(`${data[0].family_name}${data[0].given_name}さんの${data[0].name}の${status}設定を解除します。よろしいですか？`)) {
                $.ajax({
                  type: "POST",
                  url: "../classes/ajax.php",
                  datatype: "json",
                  data: {
                    "type": "delete-member_option",
                    "option_id": data[0].id
                  },
                  success: function(data) {
                    if (data == null || !data["err"]){
                      getOptionList();
                    }else{
                      $('#select_result').html(`<p>${data["err"]}</p>`);
                    }
                  },
                  error: function(data) {
                    $('#select_result').html("<p>入力エラー</p>");
                    console.log("通信失敗");
                    console.log(data);
                  }
                })
              }
            }else{
              $('#select_result').html(`<p>${data["err"]}</p>`);
            }
          },
          error: function(data) {
            $('#select_result').html("<p>情報の取得に失敗しました</p>");
            console.log("通信失敗");
            console.log(data);
          }
        })
      }
    }
  })

  $(document).on('change', '.change_option', function(){
    let err = [];
    if (isNaN($(this).closest('form').find('select[name="works"]').val())) err.push("作業");
    if (isNaN($(this).closest('form').find('select[name="members"]').val())) err.push("メンバー");
    if (err.length) {
      console.log(`${err.join("と")}が入力されていません`)
      $('#option_result').html(`<p>${err.join("と")}が入力されていません</p>`);
    }else{
      $.ajax({
        type: "POST",
        url: "../classes/ajax.php",
        datatype: "json",
        data: {
          "type": "update-member_option",
          "member_id": $(this).closest('form').find('select[name="members"]').val(),
          "work_id": $(this).closest('form').find('select[name="works"]').val(),
          "option_id": $(this).closest('form').find('button').val(),
          "change_tag": $(this).attr("name"),
        },
        success: function(data) {
          if (data == null || !data["err"]){
            console.log(data)
            getOptionList();
          }else{
            $('#select_result').html(`<p>${data["err"]}</p>`);
          }
        },
        error: function(data) {
          $('#select_result').html("<p>入力エラー</p>");
          console.log("通信失敗");
          console.log(data);
        }
      })
    }
  })
});
