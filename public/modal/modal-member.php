<div id="modal-member" class="modal">
  <div class="modal-container">
  	<div class="modal-body">
  		<div class="modal-content">
        <h1></h1>
        <div class="add_products">
          <form onsubmit="return false;">
            姓:<input id="family_name" required><br>
            名:<input id="given_name" required><br>
            ふりがな:<input id="kana_name" pattern="[\u3041-\u3096]*" required><br>
            有効/無効:<input id="member_archive" type="checkbox" >
            <div id="member_result"></div>
            <button id="submit_member">
            <button type="button" class="md-close">戻る</button>
          </form>
        </div>
  		</div>
  	</div>
  </div>
</div>
