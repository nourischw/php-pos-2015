<div class="modal fade popup_list" id="supplier_popup_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 1000px">
        <div class="modal-content">
            <div class="modal-header" style="border: 0px; height: 45px; padding-top: 10px; padding-bottom: 0px;">
                <span class="page_title">Supplier List</span>
                <img src="{{ Config::get('path.IMAGES') }}close_button.png" class="close_popup_button" />
            </div>

            <div class="modal-body">
                <div class="search_block">
                    <form id="form_supplier_popup_list" class="popup_list_search_form" data-list_id="supplier_popup_list">
                        <div class="form_row">
                            <label class="form_label">Supplier Code:</label>
                            <div class="fL form_field"><input id="search_supplier_code" type="text" name="search_supplier_code" class="form-control text_field" /></div>
                            <label class="form_label">Supplier Name:</label>
                            <div class="fL form_field"><input type="text" name="search_supplier_name" class="form-control text_field" /></div>                
                            <button class="form_button popup_list_search_button"><span class="glyphicon glyphicon-search"></span></button>
                            <button class="form_button popup_list_reset_button"><span class="glyphicon glyphicon-refresh"></span></button>
                        </div>
                    </form>
                </div>

                <div id="item_list" class="item_list">
                    <div class="list_header">
                        <span class="list_column col_code">Code</span>
                        <span class="list_column col_name">Name</span>
                        <span class="list_column col_mobile">Mobile</span>
                        <span class="list_column col_fax">Fax</span>
                        <span class="list_column col_email">Email</span>
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
						<ul class="pagination pagination-sm list_pagination_bar" id="page_bar" data-list_id="supplier_popup_list"></ul>
					</div>
				</div>
            </div>

        </div>
    </div>
</div>