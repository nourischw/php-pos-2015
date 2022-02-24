<div class="modal fade popup_list" id="quotation_popup_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 1000px">
        <div class="modal-content">
            <div class="modal-header" style="border: 0px; height: 45px; padding-top: 10px; padding-bottom: 0px;">
                <span class="page_title">Quotation List</span>
                <img src="{{ Config::get('path.IMAGES') }}close_button.png" class="close_popup_button" />
            </div>
			
			<div class="modal-body">
				<div class="search_block">
					<form id="form_quotation_popup_list" class="popup_list_search_form" data-list_id="quotation_popup_list">
						<div class="form_row">
							<label class="form_label">Quotation Number:</label>
							<div class="fL form_field"><input type="text" id="search_quotation_number" name="search_quotation_number" class="form-control text_field" /></div>
							<button class="form_button popup_list_search_button"><span class="glyphicon glyphicon-search"></span></button>
							<button class="form_button popup_list_reset_button"><span class="glyphicon glyphicon-refresh"></span></button>
						</div>
					</form>
				</div>

				<div id="item_list" class="item_list">
					<div class="list_header">
						<span class="list_column col_quotation_number">Quotation No.</span>
						<span class="list_column col_quote_date">Date</span>
						<span class="list_column col_quote_type">Quote Type</span>
						<span class="list_column col_quote_terms">Terms</span>
						<span class="list_column col_total_items">Items</span>
						<span class="list_column col_total_qty">Qty</span>
						<span class="list_column col_amount">Total Amt</span>
						<span class="list_column col_amount">Dis Amt</span>
						<span class="list_column col_amount">Sub Amt</span>
						<span class="list_column col_shop_code">Shop Code</span>
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
						<ul class="pagination pagination-sm list_pagination_bar" id="page_bar" data-list_id="quotation_popup_list"></ul>
					</div>
				</div>
			</div>
			
        </div>
    </div>
</div>
