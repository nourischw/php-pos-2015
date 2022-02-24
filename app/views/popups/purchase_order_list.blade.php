<div class="modal fade popup_list" id="purchase_order_popup_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 1000px">
        <div class="modal-content">
            <div class="modal-header" style="border: 0px; height: 45px; padding-top: 10px; padding-bottom: 0px;">
                <span class="page_title">Purchase Order List</span>
                <img src="{{ Config::get('path.IMAGES') }}close_button.png" class="close_popup_button" />
            </div>

            <div class="modal-body">
                <div class="search_block">
                    <form id="form_purchase_order_popup_list" class="popup_list_search_form" data-list_id="purchase_order_popup_list">
                        <div class="form_row">
                            <label class="form_label">Purchase Order No:</label>
                            <div class="fL form_field"><input type="text" id="search_purchase_order_code" name="search_purchase_order_code" class="form-control text_field" /></div>
							<button class="form_button popup_list_search_button"><span class="glyphicon glyphicon-search"></span></button>
                            <button class="form_button popup_list_reset_button"><span class="glyphicon glyphicon-refresh"></span></button>
                        </div>
                    </form>
                </div>

                <div id="item_list" class="item_list">
                    <div class="list_header">
                        <span class="list_column col_po_no">Purchase Order No</span>
                        <span class="list_column col_supplier">Supplier</span>
                        <span class="list_column col_ship_to">Ship to</span>
                        <span class="list_column col_qty">qty</span>
                        <span class="list_column col_amount">Amount</span>
                        <span class="list_column col_update_by">Update By</span>
                        <span class="list_column col_create_date">Create date</span>
                    </div>
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
						<ul class="pagination pagination-sm list_pagination_bar" id="page_bar" data-list_id="purchase_order_popup_list"></ul>
					</div>
				</div>
            </div>

        </div>
    </div>
</div>