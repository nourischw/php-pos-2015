@section('content')
@include('popups.stock_withdraw_list')
<div id="stock_withdraw" class="page_content">
	<div class="id_row">
		<div class="id_label">Stock Withdraw ID:</div>
        <div class="fL" id="id_text">(New Stock Withdraw record)</div>
	</div>

    <div class="form_block w100" id="supplier_form">
    	<div class="form_title">Supplier Info</div>
        <div class="left_column">
            <div class="form_row">
                <label for="transfer_date" class="form_label"><sup class="crRed">*</sup>Withdraw Date:</label>
                <div class="input-group">
                    <div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
                    <input type="text" class="text_field form-control datepicker" id="withdraw_date" value="{{ date("Y-m-d") }}" />
                </div>
                <div class="form-error_message" id="error_withdraw_date"></div>
            </div>

            <div class="form_row">
				<label class="form_label"><sup class="crRed">*</sup>Supplier:</label>
				<div id="select_supplier_block" class="fL">
					<select class="select_field form-control" id="supplier">
						@foreach ($supplier_list as $supplier)
							<?php extract($supplier); ?>
						<option value="{{ $id }}">{{ $code }} - {{ $name }}</option>
						@endforeach
					</select>
					<button id="select_supplier_button" class="fL btn btn-default btn-xs" style="margin-left: 10px; height: 25px;">Select</button>
					<div class="fL crRed bold w100">Please select Supplier first</div>
				</div>

                <div id="supplier_info">
                    <div class="fL" id="supplier_name_text">---</div>
                    <button id="change_supplier_button" class="btn btn-default btn-xs">Change</button>
                </div>
            </div>
        </div>
        <div class="right_column">
    		<button class="btn btn-default btn-sm stock_block_buttons show_popup_list_button" id="search_stock_withdraw_popup_list_button" data-list_id="stock_withdraw_popup_list" />Search Product</button>
    	</div>
        <div class="fL w100 form-error_message" id="item_already_exist_message">Item already exist in the list</div>
        <input type="hidden" id="supplier_id" />
    </div>

    <div class="list_container">
        <div id="item_list" class="item_list">
        	<div class="list_header">
            	<span class="list_checkbox_column"><input type="checkbox" class="list_checkbox_all" id="list_checkbox_all" /></span>
                <span class="list_column col_gi_code">GI Code</span>
				<span class="list_column col_product_upc">UPC</span>
                <span class="list_column col_product_name">Product Name</span>
                <span class="list_column col_qty">Qty</span>
                <span class="list_column col_remain">Remain</span>
                <span class="list_column col_price">Price</span>
                <span class="list_column col_price">Total</span>
            </div>
            <div class="list_no_items_row" id="list_no_items_row">(Please add products)</div>
            <div class="list_items" id="list_items"></div>
            <div class="list_button_row">
            	<div class="list_buttons_column">
					<button id="list_delete_multi_item_button" class="btn btn-default btn-sm">Remove</button>
				</div>
				<div class="list_total_items_column">
                    <div class="fL w100">
                        <div class="total_label">Total Items:</div>
                        <div id="total_list_items" class="fL">0</div>
                    </div>
				</div>
            </div>
        </div>
        <div class="fL crRed bold fs13 w100" id="no_list_item_error_message">At least 1 item must be added to the list</div>
		<div class="fL crRed bold fs13 w100 hide" id="item_qty_changed_message">
			Some items' remain qty is zero or requested withdraw qty is exceed the stock item's remain qty.<br />
			Please remove the out of stock list items and/or modify the request withdraw qty of the list item
		</div>
    </div>

    <div class="bottom_block">
        <div class="remark_block">
            <div class="fL w100"><textarea id="remarks" class="form-control remark_textarea" placeholder="Remarks"></textarea></div>
        </div>
        <div class="total_amount_block">
            <div class="total_amount_row">
                <div class="total_amount_row_label">Total:</div>
                <div class="total_amount_row_text" id="total_amount">$<span id="total_amount_value">0.00</span></div>
            </div>
        </div>
    </div>

    <div class="list_page_buttons_row">
        <button id="confirm_button" class="btn btn-default btn-sm list_page_buttons">Create</button>
        <button id="reset_all_button" class="btn btn-default btn-sm list_page_buttons">Reset All</button>
        <button class="btn btn-default btn-sm list_page_buttons redirect_button" data-redirect_page="stock_withdraw/list">Return to List</button>
    </div>

    <div class="panel panel-danger result_alert_box" id="have_invalid_items_block">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to process the task.<br />
			One or more items may be out of stock or remain qty is changed when submit the form.<br /><br />
			Please remove the out of stock items first in order to continue.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>
	
    <div class="panel panel-danger result_alert_box" id="edit_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to create stock withdraw order. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>
</div>
@stop