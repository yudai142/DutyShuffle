<div id="modal-select" class="modal">
  <div class="modal-container">
  	<div class="modal-body">
  		<div class="modal-content">
        <div class="container-select-header">
          <button type="button" class="md-close">戻る</button>
          <h1>メンバー選択</h1>
          <button id="submit_work">確定</button>
        </div>
        <div class="add_products">
          <form onsubmit="return false;">
            作業名:<input id="name" required><br>
            参加人数:<input  id="multiple" type="number" min=1 value=1 required><br>
            有効/無効:<input id="work_archive" type="checkbox" >
            <div id="work_result"></div>
          </form>
        </div>
  		</div>
  	</div>
  </div>
</div>
