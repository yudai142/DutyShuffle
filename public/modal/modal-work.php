<div id="modal-work" class="modal">
  <div class="modal-container">
    <div class="container work-create">
      <h1 class="title">作業登録</h1>
      <form onsubmit="return false;">
        <div class="form-lists">
          <div class="form-list">
            <div class="form">
              <p class="item">作業名：</p>
              <input class="input-tx" id="name" type="text" required>
            </div>
            <div class="form">
              <p class="item">参加枠数：<input class="input-tx" id="multiple" type="number" min=1 value=1 required> 枠</p>
            </div>
          </div>
          <div class="form-list">
            <div class="form">
              <p class="item">無効：</p>
              <input  type="checkbox" class="checkbox" id="work_archive" value="">
              <span class="supple upper">※チェックで無効</span>
            </div>
            <div class="btns">
              <div class="btn orange md-close">
                戻る
              </div>
              <div class="btn green" id="submit_work">
                登録
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
