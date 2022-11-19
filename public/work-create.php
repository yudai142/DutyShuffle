<div class="modal-container">
	<div class="modal-body">
		<!-- 閉じるボタン -->
		<div class="modal-close">×</div>
		<!-- モーダル内のコンテンツ -->
		<div class="modal-content">
      <h1>作業登録</h1>
			<!-- レコード追加 -->
      <div class="add_products">
          <!-- 入力フォーム -->
          作業名:<input id="name" required><br>
          参加人数:<input  id="multiple" type="number" min=1 value=1 required><br>
          有効/無効:<input id="archive" type="checkbox" value=1 >
          <!-- レコード追加後に入力内容を表示 -->
          <div id="add_result">
          </div>
          <!-- 送信ボタン -->
          <button id="ajax_add">追加</button>
          <button id="modal-close">戻る</button>
      </div>
		</div>
	</div>
</div>