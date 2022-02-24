<div class="modal fade popup_list" id="stock_popup_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 1000px">
        <div class="modal-content">
            <div class="modal-header" style="border: 0px; height: 45px; padding-top: 10px; padding-bottom: 0px;">
                <span class="page_title">Stock List</span>
                <img src="{{ Config::get('path.IMAGES') }}close_button.png" class="close_popup_button" />
            </div>
			
			<div class="modal-body">
				<div class="search_block">
					<form id="form_stock_popup_list" class="popup_list_search_form" data-list_id="stock_popup_list">
						<div class="form_row">
                            <label class="form_label">From Shop:</label>
                            <div class="fL form_field" id="from_shop_name">---</div>
						</div>
						<div class="form_row">
							<label class="form_label">Product UPC:</label>
							<div class="fL form_field"><input type="text" id="search_product_upc" name="search_product_upc" class="form-control text_field" /></div>
							<label class="form_label">Product Name:</label>
							<div class="fL form_field"><input type="text" name="search_product_name" class="form-control text_field" /></div>
							<label class="form_label">Serial Number:</label>
							<div class="fL form_field"><input type="text" id="search_serial_number" name="search_serial_number" class="form-control text_field" /></div>
							<button class="form_button popup_list_search_button"><span class="glyphicon glyphicon-search"></span></button>
							<button class="form_button popup_list_reset_button"><span class="glyphicon glyphicon-refresh"></span></button>
						<input type="hidden" name="search_from_shop" id="search_from_shop" />
						</div>
					</form>
				</div>

				<div id="item_list" class="item_list">
					<div class="list_header">
						<span class="list_column col_product_upc">UPC</span>
						<span class="list_column col_product_name">Product Name</span>
						<span class="list_column col_serial_number">Serial Number</span>
						<span class="list_column col_qty">Qty</span>
						<span class="list_column col_unit_price">Unit Price</span>
					</div>
					<div class="list_no_items_row" style="display: none">(No record found)</div>
					<div class="popup_list_items" id="popup_list_items"></div>
				</div>
			</div>
			
            <div class="modal-footer" style="border: 0px;">
				<div class="list_pagination">
					<div class="total_records_column">
						<strong>Total records found: </strong>
						<span id="total_records"></span>
					</div>
					<div class="paging_bar_column">
						<ul class="pagination pagination-sm list_pagination_bar" id="page_bar" data-list_id="stock_popup_list"></ul>
					</div>
				</div>
            </div>
			
        </div>
    </div>
</div>
