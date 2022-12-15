<div id="modal-member" class="modal">
  <div class="modal-container">
    <div class="container member-form">
      <h1 class="title">メンバー登録</h1>
      <form onsubmit="return false;">
        <div class="form-lists">
          <div class="form-list">
            <div class="form">
              <p class="item">性：</p>
              <input type="text" class="input-tx" id="family_name" required>
            </div>
            <div class="form">
              <p class="item">ふりがな：</p>
              <input type="text" class="input-tx" id="kana_name" pattern="[\u3041-\u3096]*" required>
            </div>
          </div>
          <div class="form-list">
            <div class="form">
              <p class="item">名：</p>
              <input type="text" class="input-tx" id="given_name" required>
            </div>
            <div class="form">
              <p class="item">無効：</p>
              <input  type="checkbox" class="checkbox" id="member_archive">
              <span class="supple upper">※チェックで無効</span>
            </div>
            <p class="supple lower">※退所したらチェックを入れてください</p>
          </div>
        </div>
        <div class="btns">
          <div class="btn orange md-close">
            戻る
          </div>
          <div class="btn green" id="submit_member">
            登録
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
