$(function($){
  // ==================== キャッシングシステム ====================
  const CACHE_CONFIG = {
    GLOBAL_TTL: 60 * 1000,        // グローバル キャッシュ有効期間: 60秒
    ALLOCATION_TTL: 30 * 1000,    // allocation_list キャッシュ: 30秒
    MEMBER_TTL: 120 * 1000,       // member_list キャッシュ: 120秒
    WORK_TTL: 120 * 1000          // work_list キャッシュ: 120秒
  };

  const ajaxCache = new Map();
  
  /**
   * キャッシュキーの生成
   */
  function getCacheKey(type, params = {}) {
    return JSON.stringify({ type, params: Object.keys(params).sort().reduce((sorted, key) => {
      sorted[key] = params[key];
      return sorted;
    }, {})});
  }
  
  /**
   * キャッシュ設定の取得
   */
  function getCacheTTL(type) {
    const ttlMap = {
      'allocation_list': CACHE_CONFIG.ALLOCATION_TTL,
      'member_list': CACHE_CONFIG.MEMBER_TTL,
      'work_list': CACHE_CONFIG.WORK_TTL,
      'join_member': CACHE_CONFIG.ALLOCATION_TTL,
      'join_work': CACHE_CONFIG.ALLOCATION_TTL,
      'option_list': CACHE_CONFIG.GLOBAL_TTL
    };
    return ttlMap[type] || CACHE_CONFIG.GLOBAL_TTL;
  }
  
  /**
   * キャッシュからデータ取得（有効期限内の場合）
   */
  function getCachedData(type, params = {}) {
    const key = getCacheKey(type, params);
    const cached = ajaxCache.get(key);
    
    if (cached && Date.now() - cached.timestamp < getCacheTTL(type)) {
      return cached.data;
    }
    
    return null;
  }
  
  /**
   * キャッシュにデータを保存
   */
  function setCachedData(type, params = {}, data) {
    const key = getCacheKey(type, params);
    ajaxCache.set(key, { data, timestamp: Date.now() });
  }
  
  /**
   * 特定のキャッシュをクリア
   */
  function invalidateCache(pattern = null) {
    if (pattern === null) {
      ajaxCache.clear();
    } else {
      for (const key of ajaxCache.keys()) {
        if (key.includes(pattern)) {
          ajaxCache.delete(key);
        }
      }
    }
  }
  
  /**
   * キャッシュ対応のAJAX関数（内部使用）
   */
  function cachedAjax(type, params = {}) {
    return new Promise((resolve, reject) => {
      // キャッシュを確認
      const cached = getCachedData(type, params);
      if (cached !== null) {
        return resolve(cached);
      }
      
      // キャッシュがない場合はAJAX実行
      $.ajax({
        type: "POST",
        url: "../classes/ajax.php",
        dataType: "json",
        data: { type, ...params },
        success: function(data) {
          // キャッシュに保存
          setCachedData(type, params, data);
          resolve(data);
        },
        error: function(xhr, status, error) {
          reject(new Error(`AJAX Error: ${status}`));
        }
      });
    });
  }

  // DOM操作完了を追跡するPromise
  let domOperationPromise = Promise.resolve();

  // グローバルAjax設定 - すべてのAJAXリクエストに30秒のタイムアウトを設定
  $.ajaxSetup({
    complete: function() {
      // DOM操作が完了するまで待機するPromiseを作成
      domOperationPromise = new Promise(resolve => {
        setTimeout(function() {
          requestAnimationFrame(function() {
            requestAnimationFrame(function() {
              resolve();
            });
          });
        }, 0);
      });
    }
  });

  // グローバルなAJAXイベントハンドラ - すべてのAJAXリクエストで自動的にローディングを表示/非表示
  $(document).on('ajaxStart', function() {
    $('#loading-spinner').removeClass('hidden').addClass('show');
  });

  // ajaxStop：すべてのAJAX通信が完了したときに実行
  $(document).on('ajaxStop', async function() {
    // DOM操作の完了を待つ
    await domOperationPromise;
    // ローダーを消す
    $('#loading-spinner').removeClass('show').addClass('hidden');
  });

  // グローバル変数 - カレンダー初期化用
  let selectedDates = [];
  let flatpickrInstance;

  if ( location.pathname.indexOf("/create-edit.php") !== -1 ){
    $("#create-edit_page").html("<p>登録・編集</p>");
    // 並列処理でメンバーと作業を同時取得
    Promise.all([
      cachedAjax('member_list', { select: $('#member_view').val() }),
      cachedAjax('work_list', { select: $('#work_view').val() })
    ]).then(([members, works]) => {
      renderMembersTable(members);
      renderWorksTable(works);
    });
    
    $('#member_view').change(function() {
      invalidateCache('member_list');
      getAllMember();
    });
      
    $('#work_view').change(function() {
      invalidateCache('work_list');
      getAllWork();
    });
  }else if ( location.pathname.indexOf("/top.php") != -1 ){
    $("#top_page").html("<p>トップ</p>");
    // 並列処理でメンバーと作業を同時取得
    Promise.all([
      cachedAjax('join_member', { date: $("#date").val() }),
      cachedAjax('join_work', { date: $("#date").val() })
    ]).then(([members, works]) => {
      renderJoinMembers(members);
      renderJoinWorks(works);
    });
  }else if ( location.pathname.indexOf("/allocation.php") != -1 ){
    $("#allocation_page").html("<p>割り当て</p>");
    allocationView();
  }else if ( location.pathname.indexOf("/option.php") != -1 ){
    $("#option_page").html("<p>オプション</p>");
    // バッチリクエストで全オプション初期化データを一度に取得
    $.ajax({
      type: "POST",
      url: "../classes/ajax.php",
      dataType: "json",
      data: { "type": 'batch_init_option' },
      success: function(data) {
        if (data && !data.err) {
          $('#interval_input').val(data.interval);
          $('input[name="week_mode"][value="' + data.weekUse + '"]').prop('checked', true);
          $('#week_day_select').val(data.week.toString());
          
          selectedDates = data.resetDates;
          if (flatpickrInstance) {
            const dates = selectedDates.map(dateStr => new Date(dateStr));
            flatpickrInstance.setDate(dates, false);
          }
        }
      }
    });
    
    getOptionList();
    initResetDatePicker();
  }
  
  // ==================== レンダリング関数 ====================
  function renderMembersTable(data) {
    if (data == null || data.err != null) {
      $('#member_show_result').html("");
    } else {
      let arr = [];
      $.each(data, function(key, value) {
        arr.push(`<div id=member_${value.id} class="button member b-select md-btn" data-target='modal-member' value=${value.id}>${value.family_name}　${value.given_name}</div>`);
      });
      $('#member_show_result').html(arr);
    }
  }

  function renderWorksTable(data) {
    if (data == null || data.err != null) {
      $('#work_show_result').html("");
    } else {
      let arr = [];
      $.each(data, function(key, value) {
        arr.push(`<div id=work_${value.id} class="button work b-select md-btn" data-target='modal-work' value=${value.id}>${value.name}</div>`);
      });
      $('#work_show_result').html(arr);
    }
  }

  function renderJoinMembers(data) {
    if (data == null || data.err != null) {
      $('#join_member').html("");
    } else {
      let arr = [];
      $.each(data, function(key, value) {
        let work_name = (value.work_name != null) ? `<p>${value.work_name}</p>` : "";
        arr.push(`<li id=join_member_${value.history_id}><div class="button member state-btn" data-target='remove-member' value=${value.history_id}>${value.family_name}　${value.given_name}</div>${work_name}</li>`);
      });
      $('#join_member').html(arr);
    }
  }

  function renderJoinWorks(data) {
    if (data == null || data.err != null) {
      $('#join_work').html("");
    } else {
      let arr = [];
      $.each(data, function(key, value) {
        let style = (value.status == 1) ? 'work' : 'off';
        arr.push(`<li id=join_work_${value.id}><div class="button ${style} state-btn" data-target='work-change' value=${value.id}>${value.name}</div></li>`);
      });
      $('#join_work').html(arr);
    }
  }
  
  function allocationView(){
    cachedAjax('allocation_list', { date: $("#date").val() })
      .then(data => {
        if (data == null) {
          $('#allocation-form').html("");
        } else if(data["err"] == null) {
          let arr = [];
          $.each(data[0], function(work_key, work_value) {
            let style = (work_value.status == 1) ? 'work' : 'off';
            if(data[1] != null) {
              let member = data[1].filter(value => {if(value.work_id == work_value.id){return true;}});
              let list = [];
              $.each(member, function(member_key, member_value) {
                list.push(`
                  <div class="select-member-button" id="history_${member_value.history_id}" value="${member_value.history_id}">
                    <div class="button member">${member_value.family_name}　${member_value.given_name}</div>
                  </div>
                `);
              });
              arr.push(`
                <li class="select-member">
                  <div class="md-btn button ${style} square work-title content" data-target="modal-select" data-type="work" value="${work_value.id}">${work_value.name}</div>
                  ${list.join("")}
                </li>
              `);
            } else {
              arr.push(`<li class="select-member"><div class="md-btn button ${style} square work-title content" data-target="modal-select" data-type="work" value="${work_value.id}">${work_value.name}</div></li>`);
            }
          });
          if(data[1] != null) {
            let null_member = data[1].filter(value => {if(value.work_id == null){return true;}});
            let null_list = [];
            $.each(null_member, function(null_key, null_value) {
              null_list.push(`<li class="select-member"><div class="select-member-button" id="history_${null_value.history_id}" value="${null_value.history_id}"><div class="button member">${null_value.family_name}　${null_value.given_name}</div></div></li>`);
            });
            $('#null-member-list').html(null_list);
          }
          $('#allocation-form').html(arr);
        } else {
          $('#allocation-form').html(`<p>${data["err"]}</p>`);
        }
      })
      .catch(error => {
        $('#allocation-form').html("<p>通信エラー</p>");
      });
  }
  
  function joinMember(){
    cachedAjax('join_member', { date: $("#date").val() })
      .then(data => renderJoinMembers(data))
      .catch(() => $('#join_member').append("<p>通信エラー</p>"));
  }

  function joinWork(){
    cachedAjax('join_work', { date: $("#date").val() })
      .then(data => renderJoinWorks(data))
      .catch(() => $('#join_work').append("<p>通信エラー</p>"));
  }

  function getAllMember(){
    cachedAjax('member_list', { select: $('#member_view').val() })
      .then(data => renderMembersTable(data))
      .catch(() => $('#member_show_result').append("<p>通信エラー</p>"));
  }
  
  function getAllWork(){
    cachedAjax('work_list', { select: $('#work_view').val() })
      .then(data => renderWorksTable(data))
      .catch(() => $('#work_show_result').append("<p>通信エラー</p>"));
  }

  function getOptionList(){
    cachedAjax('option_list', {})
      .then(data => {
        if (data == null) {
          $('#option_list').html("");
        } else if(data['err'] == null) {
          let fixed_list = [];
          let exclusion_list = [];
          data[2].unshift({ id:null, status: 0 }, { id:null, status: 1 });
          
          $.each(data[2], function(option_key, option_value) {
            let work_class = (option_value.id == null) ? "add_option" : "change_option";
            let work_list = $("<select>", {
              id: `works_${(option_value.id == null) ? "new" : option_value.id}`,
              name: 'works',
              class:`button work square ${work_class}`,
            });
            
            if(option_value.id == null) {
              work_list.append($('<option>').prop({ hidden: true, text: "ーー" }));
            }
            
            for (const val of data[1]) {
              if(val["archive"] == 0 || val["id"] == option_value.work_id) {
                let selected = (val["id"] == option_value.work_id) ? true : false;
                $(work_list).append($('<option>').prop({
                  value: val["id"],
                  text: val["name"],
                  selected: selected
                }));
              }
            }

            let member_class = (option_value.id == null) ? "add_option" : "change_option";
            let member_list = $("<select>", {
              id: `members_${(option_value.id == null) ? "new" : option_value.id}`,
              name: 'members',
              class:`button member square ${member_class}`
            });
            
            if(option_value.id == null) {
              member_list.append($('<option>').prop({ hidden: true, text: "ーー" }));
            }
            
            for (const val of data[0]) {
              if(val["archive"] == 0 || val["id"] == option_value.member_id) {
                let selected = (val["id"] == option_value.member_id) ? true : false;
                $(member_list).append($('<option>').prop({
                  value: val["id"],
                  text: `${val["family_name"]}　${val["given_name"]}`,
                  selected: selected
                }));
              }
            }
            
            let option_list = $("<form>", {onsubmit: "return false;"})
              .append($("<ul>", {class: "option-group"})
              .append($("<li>").append(work_list), $("<li>").append(member_list), 
                $("<li>").append($("<div>",{
                  text:(option_value.id == null) ? "追加" : "解除", 
                  value: (option_value.id == null) ? option_value.status : option_value.id,
                  class: "button work state-btn",
                  "data-target": (option_value.id == null) ? "add-member_option" : "delete-member_option"
                }))));
            
            if(option_value.status == 0) {
              fixed_list.push(option_list);
            } else {
              exclusion_list.push(option_list);
            }
          });
          
          $('#fixed_list').html(fixed_list);
          $('#exclusion_list').html(exclusion_list);
        } else {
          $('#option_list').append(`<p>${data["err"]}</p>`);
        }
      })
      .catch(() => $('#option_list').append("<p>通信エラー</p>"));
  }

  function getIntervalValue(){
    $.ajax({
      url: "../classes/ajax.php",
      data: { "type": 'get_interval' },
      dataType: "json",
      success: function(data) {
        if (typeof data === 'string') data = JSON.parse(data);
        $('#interval_input').val(data && data.interval !== null ? parseInt(data.interval) : 0);
      },
      error: function(){
        $('#interval_input').val(0);
      }
    });
  }

  function getWeekUseValue(){
    $.ajax({
      url: "../classes/ajax.php",
      data: { "type": 'get_week_use' },
      dataType: "json",
      success: function(data) {
        if (typeof data === 'string') data = JSON.parse(data);
        const weekUseValue = data && data.weekUse !== null ? (parseInt(data.weekUse) === 1 ? '1' : '0') : '0';
        $('input[name="week_mode"][value="' + weekUseValue + '"]').prop('checked', true);
      },
      error: function(){
        $('input[name="week_mode"][value="0"]').prop('checked', true);
      }
    });
  }

  function getWeekValue(){
    $.ajax({
      url: "../classes/ajax.php",
      data: { "type": 'get_week' },
      dataType: "json",
      success: function(data) {
        if (typeof data === 'string') data = JSON.parse(data);
        $('#week_day_select').val(data && data.week !== null ? data.week.toString() : '0');
      },
      error: function(){
        $('#week_day_select').val('0');
      }
    });
  }

  // 複数日付選択用のカレンダー初期化（Flatpickr）
  
  function initResetDatePicker(){
    flatpickrInstance = flatpickr("#datepicker", {
      mode: "multiple",
      dateFormat: "Y-m-d",
      locale: "ja",
      onChange: function(selectedDates, dateStr, instance) {
        // 選択された日付が更新された時の処理
        updateSelectedDatesDisplay();
      }
    });
  }

  function updateSelectedDatesDisplay(){
    if (flatpickrInstance) {
      selectedDates = flatpickrInstance.selectedDates.map(date => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
      });
    }
  }

  function getResetDates(){
    $.ajax({
      url: "../classes/ajax.php",
      data: { "type": 'get_reset_dates' },
      dataType: "json",
      success: function(data) {
        if (typeof data === 'string') data = JSON.parse(data);
        if (data && data.dates && Array.isArray(data.dates) && data.dates.length > 0) {
          selectedDates = data.dates;
          if (flatpickrInstance) {
            const dates = selectedDates.map(dateStr => new Date(dateStr));
            flatpickrInstance.setDate(dates, false);
          }
          updateSelectedDatesDisplay();
        } else {
          selectedDates = [];
          if (flatpickrInstance) flatpickrInstance.clear();
        }
      },
      error: function(){
        selectedDates = [];
      }
    });
  }

  // 「表示」ボタンのハンドラ - カレンダーの表示/非表示をトグル
  $('#reset_date_clear_btn').on('click', function(){
    let container = $('#calendar_container');
    if (container.hasClass('show')) {
      container.removeClass('show');
    } else {
      container.addClass('show');
      // カレンダーが開く際にフォーカスを当てる
      setTimeout(function(){
        $('#calendar_toggle_btn').focus();
      }, 100);
    }
  });

  // カレンダーボタンのハンドラ - datepicker をクリック
  $(document).on('click', '#calendar_toggle_btn', function(){
    $('#datepicker').click();
  });

  // 週ごと/指定日ラジオボタンの変更イベント
  $(document).on('change', 'input[name="week_mode"]', function(){
    let selectedValue = $(this).val();
    
    if (selectedValue === '0') {
      // 指定日を選択
      $('#reset_date_container').show();
      $('#week_container').hide();
    } else {
      // 週ごとを選択
      $('#reset_date_container').hide();
      $('#week_container').show();
    }
  });

  // ページ読み込み時の表示制御
  $(function() {
    // オプションページの場合、初期表示を設定
    if (location.pathname.indexOf("/option.php") !== -1) {
      let selectedWeekMode = $('input[name="week_mode"]:checked').val();
      if (selectedWeekMode === '1') {
        $('#reset_date_container').hide();
        $('#week_container').show();
      } else {
        $('#reset_date_container').show();
        $('#week_container').hide();
      }
    }
  });

  $('#interval_save_btn').on('click', function(){
    // 日付変更時のキャッシュ無効化をここで設定
    $(document).on('change', '#date', function(){
      invalidateCache('allocation_list');
      invalidateCache('join_member');
      invalidateCache('join_work');
      if (location.pathname.indexOf("/top.php") !== -1) {
        joinMember();
        joinWork();
      } else if (location.pathname.indexOf("/allocation.php") !== -1) {
        allocationView();
      }
    });
    
    let interval = $('#interval_input').val();
    if (interval == "" || isNaN(interval) || parseInt(interval) < 0) {
      $('#option_result').html("<p>有効な数値を入力してください</p>");
      return;
    }

    $('#option_result').html("<p>保存中...</p>");
    let saveCount = 0;
    let successCount = 0;
    let hasError = false;

    // 期間のAJAX保存
    $.ajax({
      type: "POST",
      url: "../classes/ajax.php",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded; charset=UTF-8",
      data: {
        "type": 'save_interval',
        "interval": parseInt(interval)
      },
      success: function(data) {
        try {
          if (data && data.success === true) {
            successCount++;
          } else if (typeof data === 'object' && !data.err) {
            successCount++;
          } else {
            hasError = true;
          }
        } catch (e) {
          hasError = true;
        }
        saveCount++;
        checkSaveCompletion();
      },
      error: function(xhr, status, error) {
        hasError = true;
        saveCount++;
        checkSaveCompletion();
      }
    });

    // リセット日程のAJAX保存
    $.ajax({
      type: "POST",
      url: "../classes/ajax.php",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded; charset=UTF-8",
      data: {
        "type": 'save_reset_dates',
        "dates": JSON.stringify(selectedDates)
      },
      success: function(data) {
        try {
          if (data && data.success === true) {
            successCount++;
          } else if (typeof data === 'object' && !data.err) {
            successCount++;
          } else {
            hasError = true;
          }
        } catch (e) {
          hasError = true;
        }
        saveCount++;
        checkSaveCompletion();
      },
      error: function(xhr, status, error) {
        hasError = true;
        saveCount++;
        checkSaveCompletion();
      }
    });

    // 週ごとのリセット設定のAJAX保存
    let weekUse = $('input[name="week_mode"]:checked').val();
    if (weekUse === undefined || weekUse === null) {
      weekUse = '0'; // デフォルト値
    }
    $.ajax({
      type: "POST",
      url: "../classes/ajax.php",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded; charset=UTF-8",
      data: {
        "type": 'save_week_use',
        "weekUse": parseInt(weekUse)
      },
      success: function(data) {
        try {
          if (data && data.success === true) {
            successCount++;
          } else if (typeof data === 'object' && !data.err) {
            successCount++;
          } else {
            hasError = true;
          }
        } catch (e) {
          hasError = true;
        }
        saveCount++;
        checkSaveCompletion();
      },
      error: function(xhr, status, error) {
        hasError = true;
        saveCount++;
        checkSaveCompletion();
      }
    });

    // 曜日選択のAJAX保存
    let weekDay = $('#week_day_select').val();
    if (weekDay === undefined || weekDay === null || weekDay === '') {
      weekDay = '0'; // デフォルト値
    }
    $.ajax({
      type: "POST",
      url: "../classes/ajax.php",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded; charset=UTF-8",
      data: {
        "type": 'save_week',
        "week": parseInt(weekDay)
      },
      success: function(data) {
        try {
          if (data && data.success === true) {
            successCount++;
          } else if (typeof data === 'object' && !data.err) {
            successCount++;
          } else {
            hasError = true;
          }
        } catch (e) {
          hasError = true;
        }
        saveCount++;
        checkSaveCompletion();
      },
      error: function(xhr, status, error) {
        hasError = true;
        saveCount++;
        checkSaveCompletion();
      }
    });

    function checkSaveCompletion(){
      if (saveCount === 4) {
        if (!hasError && successCount === 4) {
          $('#option_result').html("<p style=\"color: green;\">保存しました</p>");
          setTimeout(function(){
            $('#option_result').html("");
          }, 3000);
        } else {
          $('#option_result').html("<p>保存に失敗しました</p>");
        }
      }
    }
  });

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
              invalidateCache('member_list');
              $('.modal-container').fadeOut();
              getAllMember();
            }else{
              $('#member_result').html(`<p>${data["err"]}</p>`);
            }
          },
          error: function(xhr, status, error) {
            $('#member_result').html("<p>入力エラー</p>");
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
              invalidateCache('member_list');
              getAllMember();
            }else{
              $('#member_result').html(`<p>${data["err"]}</p>`);
            }
          },
          error: function(xhr, status, error) {
            $('#member_result').html("<p>入力エラー</p>");
          }
        });
      }
    }
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
            "archive" : Number($('#work_archive').prop("checked")),
            "isAbove" : Number($('input[name="is_above"]:checked').val())
          },
          success: function(data) {
            if (!data["err"]){
              $('#work_result').html(`<p>${data[0].name}(${data[0].multiple}人)${(data[0].archive==0)?"有効":"無効"}に更新しました。</p>`);
              invalidateCache('work_list');
              invalidateCache('option_list');
              $('.modal-container').fadeOut();
              getAllWork();
            }else{
              $('#work_result').html(`<p>${data["err"]}</p>`);
            }
          },
          error: function(xhr, status, error) {
            $('#work_result').html("<p>入力エラー</p>");
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
            "archive" : Number($('#work_archive').prop("checked")),
            "isAbove" : Number($('input[name="is_above"]:checked').val())
          },
          success: function(data) {
            if (!data["err"]){
              $('#work_result').html(`<p>${data[0].name}(${data[0].multiple}人)${(data[0].archive==0)?"有効":"無効"}を登録しました。</p>`);
              $('#name').val("");
              $('#multiple').val(1);
              $('#work_archive').prop("checked", false);
              invalidateCache('work_list');
              invalidateCache('option_list');
              getAllWork();
            }else{
              $('#work_result').html(`<p>${data["err"]}</p>`);
            }
          },
          error: function(xhr, status, error) {
            $('#work_result').html("<p>入力エラー</p>");
          }
        });
      }
    }
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
      //   }
      // });
      $.ajax({
        type: "POST",
        url: "../classes/ajax.php",
        dataType: "json",
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
        error: function(xhr, status, error) {
          $('#select_result').html("<p>入力エラー</p>");
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
        dataType: "json",
        data: {
          "type": "work_select_definition",
          "select_work": $(this).attr("value"),
          "history_id": $(this).attr("data-history-id"),
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
        error: function(xhr, status, error) {
          $('#select_result').html("<p>入力エラー</p>");
        }
      });
    }
  })
  $(document).on('click', '.state-btn', function(){
    if ($(this).attr("data-target") == "work-change") {
      $.ajax({
        type: "POST",
        url: "../classes/ajax.php",
        dataType: "json",
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
        error: function(xhr, status, error) {
          $('#select_result').html("<p>入力エラー</p>");
        }
      });
    }else if ($(this).attr("data-target") == "remove-member") {
      let alert_text = "";
      let add_text = "";
      if( location.pathname.indexOf("/top.php") != -1 ){
        alert_text = `${$(this).text()}さんを不参加にしますか？`
        add_text = ($(this).closest(`#join_member_${$(this).attr("value")}`).find('span').text() == 0)? "":`\n${$(this).closest(`#join_member_${$(this).attr("value")}`).find('span').text().slice( 0, -8 )}の担当も削除されます`;
      }else if ( location.pathname.indexOf("/allocation.php") != -1 ){
        alert_text = `${$(this).closest(".select-member-button").find(".member").text()}さんを不参加にしますか？`
        add_text = ($(this).closest('.select-member').find('.md-btn').text() == 0)? "":`\n${$(this).closest('.select-member').find('.md-btn').text()}の担当も削除されます`;
      }
      
      if (confirm(`${alert_text}${add_text}`)) {
        $.ajax({
          type: "POST",
          url: "../classes/ajax.php",
          dataType: "json",
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
          error: function(xhr, status, error) {
            $('#select_result').html("<p>入力エラー</p>");
          }
        });
      }
    }else if ($(this).attr("data-target") == "allocation-remove") {
      if (confirm(`割り当て済みの担当を全て解除します。よろしいですか？`)) {
        $.ajax({
          type: "POST",
          url: "../classes/ajax.php",
          dataType: "json",
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
          error: function(xhr, status, error) {
            $('#select_result').html("<p>入力エラー</p>");
          }
        });
      }
    }else if ($(this).attr("data-target") == "add-member_option") {
      let work_id = $(this).closest('form').find('select[name="works"]').val();
      let member_id = $(this).closest('form').find('select[name="members"]').val();
      let status = $(this).attr("value");
      
      if (!work_id || !member_id) {
        $('#option_result').html("<p>作業とメンバーを選択してください</p>");
        return;
      }
      
      $.ajax({
        type: "POST",
        url: "../classes/ajax.php",
        dataType: "json",
        data: {
          "type": "add-member_option",
          "work_id": work_id,
          "member_id": member_id,
          "status": status
        },
        success: function(data) {
          if (data && data.success === true) {
            $('#option_result').html("<p style='color:green;'>保存しました</p>");
            setTimeout(function() {
              getOptionList();
            }, 500);
            setTimeout(function() {
              $('#option_result').html("");
            }, 3000);
          } else {
            $('#option_result').html(`<p>${data.err || "保存に失敗しました"}</p>`);
          }
        },
        error: function(xhr, status, error) {
          $('#option_result').html("<p>通信エラーが発生しました</p>");
        }
      });
    }else if ($(this).attr("data-target") == "update-member_option") {
      let work_id = $(this).closest('form').find('select[name="works"]').val();
      let member_id = $(this).closest('form').find('select[name="members"]').val();
      let option_id = $(this).attr("value");
      
      if (!work_id || !member_id) {
        $('#option_result').html("<p>作業とメンバーを選択してください</p>");
        return;
      }
      
      $.ajax({
        type: "POST",
        url: "../classes/ajax.php",
        dataType: "json",
        data: {
          "type": "update-member_option",
          "work_id": work_id,
          "member_id": member_id,
          "option_id": option_id,
          "change_tag": "works"
        },
        success: function(data) {
          if (data && !data.err) {
            $('#option_result').html("<p style='color:green;'>更新しました</p>");
            setTimeout(function() {
              getOptionList();
            }, 500);
            setTimeout(function() {
              $('#option_result').html("");
            }, 3000);
          } else {
            $('#option_result').html(`<p>${data.err || "更新に失敗しました"}</p>`);
          }
        },
        error: function(xhr, status, error) {
          $('#option_result').html("<p>通信エラーが発生しました</p>");
        }
      });
    }else if ($(this).attr("data-target") == "delete-member_option") {
      if (confirm("この設定を削除しますか？")) {
        $.ajax({
          type: "POST",
          url: "../classes/ajax.php",
          dataType: "json",
          data: {
            "type": "delete-member_option",
            "option_id": $(this).attr("value")
          },
          success: function(data) {
            if (data == null || !data.err) {
              $('#option_result').html("<p style='color:green;'>削除しました</p>");
              setTimeout(function() {
                getOptionList();
              }, 500);
            } else {
              $('#option_result').html(`<p>${data.err}</p>`);
            }
          },
          error: function(xhr, status, error) {
            $('#option_result').html("<p>通信エラーが発生しました</p>");
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
        error: function(xhr, status, error) {
          $('#select_result').html("<p>入力エラー</p>");
        }
      });
    }else if ($(this).attr("data-target") == "add-member_option") {
      let err = [];
      if (isNaN($(this).closest('form').find('#works_new').val())) err.push("作業");
      if (isNaN($(this).closest('form').find('#members_new').val())) err.push("メンバー");
      if (err.length) {
        $('#option_result').html(`<p>${err.join("と")}が入力されていません</p>`);
      }else{
        $.ajax({
          type: "POST",
          url: "../classes/ajax.php",
          dataType: "json",
          data: {
            "type": "add-member_option",
            "member_id": $(this).closest('form').find('#members_new').val(),
            "work_id": $(this).closest('form').find('#works_new').val(),
            "status": $(this).val()
          },
          success: function(data) {
            if (data == null || !data["err"]){
              $('#option_result').html("<p style=\"color: green;\">保存しました</p>");
              setTimeout(function(){
                $('#option_result').html("");
              }, 3000);
              getOptionList();
            }else{
              $('#option_result').html(`<p>${data["err"]}</p>`);
            }
          },
          error: function(xhr, status, error) {
            $('#select_result').html("<p>入力エラー</p>");
          }
        })
      }
    }else if ($(this).attr("data-target") == "delete-member_option") {
      let err = [];
      if (err.length) {
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
                  error: function(xhr, status, error) {
                    $('#select_result').html("<p>入力エラー</p>");
                  }
                })
              }
            }else{
              $('#select_result').html(`<p>${data["err"]}</p>`);
            }
          },
          error: function(xhr, status, error) {
            $('#select_result').html("<p>情報の取得に失敗しました</p>");
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
      $('#option_result').html(`<p>${err.join("と")}が入力されていません</p>`);
    }else{
      // ボタンのテキストと data-target を変更
      $(this).closest('form').find('.state-btn').text('更新').attr('data-target', 'update-member_option');
    }
  })

  let arySpinnerCtrl = [];
  let spin_speed = 20; //変動スピード
  
  //長押し押下時
  $(document).on('touchstart mousedown click', '.btn-spinner', function(e){
      if(arySpinnerCtrl['interval']) return false;
      let target = $(this).data('target');
      arySpinnerCtrl['target'] = target;
      arySpinnerCtrl['timestamp'] = e.timeStamp;
      arySpinnerCtrl['cal'] = Number($(this).data('cal'));
      //クリックは単一の処理に留める
      if(e.type == 'click'){
          spinnerCal();
          arySpinnerCtrl = [];
          return false;
      }
      //長押し時の処理
      setTimeout(function(){
          //インターバル未実行中 + 長押しのイベントタイプスタンプ一致時に計算処理
          if(!arySpinnerCtrl['interval'] && arySpinnerCtrl['timestamp'] == e.timeStamp){
              arySpinnerCtrl['interval'] = setInterval(spinnerCal, spin_speed);
          }
      }, 500);
  });
  
  //長押し解除時 画面スクロールも解除に含む
  $(document).on('touchend mouseup scroll', function(e){
      if(arySpinnerCtrl['interval']){
          clearInterval(arySpinnerCtrl['interval']);
          arySpinnerCtrl = [];
      }
  });
  
  //変動計算関数
  function spinnerCal(){
      let target = $(arySpinnerCtrl['target']);
      let num = Number(target.val());
      num = num + arySpinnerCtrl['cal'];
      if(num > Number(target.data('max'))){
          target.val(Number(target.data('max')));
      }else if(Number(target.data('min')) > num){
          target.val(Number(target.data('min')));
      }else{
          target.val(num);
      }
  }


});
