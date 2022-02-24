@section('content')
@include('popups.product_list')
@include('popups.quotation_list')
<?php $is_update = ($record_id > 0) ? true : false ?>
<div id="deposit_edit" class="page_content">
	<div class="id_row">
		<div class="id_label">Deposit No.:</div>
        <div class="fL" id="id_text">{{ ($record_id > 0) ? $d_deposit_number : "(New Deposit record)" }}</div>
	</div>

    <form id="form_deposit_edit">
        <div class="form_block_row">
            <div class="left_column">
                <div class="form_block">
                	<div class="form_title">Deposit Info</div>
                    <div class="form_row">
                        <label class="form_label" for="deposit_date">Deposit Date:</label>
                        <div class="input-group">
                            <div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
                            <input type="text" class="form-control datepicker calendar_text_field" id="deposit_date" value="{{ $d_deposit_date }}" />
                        </div>
                    </div>
                    <div class="form_row">
                        <label for="shop_code" class="form_label">Shop Code:</label>
                        <select class="text_field form-control" id="shop_code">
                            @foreach ($shop_list as $shop)
                                <?php extract($shop); ?>
                            <option value="{{ $code }}" {{ ($code === $d_shop_code) ? 'selected' : null; }}>{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form_row">
                        <label class="form_label" for="staff_id">Staff ID:</label>
                        <select class="text_field form-control" id="staff_id">
                            @foreach ($staff_list as $index => $staff)
                                <?php extract($staff); ?>
                            <option value="{{ $id }}" {{ ($id === $d_staff_id) ? 'selected' : null }}>{{ $staff_code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form_row">
                        <label class="form_label" for="quotation_number">Quotation Number:</label>
                        <span class="quotation_number" id="quotation_number_text" style="min-width: 200px; float: left;">{{ ($d_quotation_number != "") ? $d_quotation_number : "---" }}</span>
                        <span class="input-group-btn text_field_buttons" style="float: left; width: 50px;">
                            <button class="btn btn-default text_field_button show_popup_list_button fL" id="show_quotation_popup_list_button" data-list_id="quotation_popup_list">...</button>
                            <button class="btn btn-default text_field_button glyphicon glyphicon-remove fL" style="margin-top: -1px;" id="clear_quotation_number_button"></button>
                        </span>
                        <input type="hidden" id="quotation_number" value="{{ $d_quotation_number }}}" />
                    </div>
					@if ($is_update)
					<div class="form_row">
						<label class="form_label">Status:</label>
						{{ ($d_status == 1) ? 'Voided' : 'Normal' }}
					</div>
					@endif
                </div>
            </div>
            <div class="right_column">
                <div class="form_block">
                    <div class="form_title">Payment Info</div>
                    <div class="form_row">
                        <label class="form_label" for="deposit_terms">Deposit Terms:</label>
                        <select class="form-control text_field" id="deposit_terms">
                            @foreach ($deposit_terms_list as $id => $text)
                            <option value="{{ $id }}" {{ ($id === $d_deposit_terms) ? 'selected' : null }}>{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form_row">
                        <label class="form_label" for="payment_type">Payment Type:</label>
                        <select class="form-control text_field" id="payment_type">
                            @foreach ($payment_type_list as $id => $text)
                            <option value="{{ $id }}" {{ ($id === $d_payment_type) ? 'selected' : null }}>{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form_row">
                        <label class="form_label" for="cheque_number">Cheque Number:</label>
                        <input type="text" class="form-control text_field" id="cheque_number" value="{{ $d_cheque_number }}" />
                    </div>
                    <div class="form_row">
                        <label class="form_label" for="cheque_date">Cheque Date:</label>
                        <div class="input-group">
                            <div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
                            <input type="text" class="form-control datepicker calendar_text_field" id="cheque_date" value="{{ $d_cheque_date }}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form id="form_add_product_item">
        <div class="form_block">
            <div class="form_row">
                <div class="product_name_field input-group">
                    <label class="fL new_item_label" for="product_name">Search Product Name:</label>
                    <div class="fL">
                        <input type="text" id="product_name" class="form-control new_item_text_field product_name_text_field">
                        <span class="input-group-btn text_field_buttons">
                            <button class="btn btn-default text_field_button show_popup_list_button" data-list_id="product_popup_list">...</button>
                        </span>
                    </div>
                </div>
                <div class="qty_field">
                    <label class="new_item_label" for="product_qty">Qty:</label>
                    <input type="text" class="text_field form-control qty_text_field num_item" data-num_flags="+i" id="product_qty" maxlength="5" />
                </div>
                <div class="unit_price_field">
                    <label class="new_item_label" for="product_unit_price">Unit Price:</label>
                    <input type="text" class="text_field form-control unit_price_text_field num_item" data-num_flags="+.2" id="product_unit_price" maxlength="10" />
                </div>
                <button id="add_item_button" class="add_item_button btn btn-default">Add</button>
                <input type="hidden" id="product_upc" />
            </div>

            <div class="form_row">
                <label class="new_item_label">Product Name:</label>
                <span class="product_name_text" id="product_name_text">---</span>
            </div>
        </div>
    </form>

    <div class="list_container">
        <div class="row" style="margin-bottom: 10px; margin-left: 0px">
            <div style="background: #FFFFCC; width: 30px; height: 20px; border: 1px solid black; float: left;"></div>
            <div style="float: left; margin-left: 5px;"> = Quotation items</div>
        </div>

        <div id="item_list" class="item_list">
        	<div class="list_header">
            	<span class="list_checkbox_column"><input type="checkbox" class="list_checkbox_all" id="list_checkbox_all" /></span>
				<span class="list_column col_product_upc">UPC</span>
                <span class="list_column col_product_name">Product Name</span>
                <span class="list_column col_qty">Qty</span>
                <span class="list_column col_unit_price">Unit Price</span>
                <span class="list_column col_item_total">Total</span>
            </div>
            <div class="list_no_items_row" id="list_no_items_row" {{ ($is_update) ? 'style="display: none"' : null }}>(Please add products)</div>
            <div class="list_items" id="list_items">
                @if ($have_list_item)
                    @foreach ($deposit_items_data as $value)
                        <?php extract($value, EXTR_PREFIX_ALL, 'di'); ?>
                    <div class="list_item_row" data-type="old" data-record_id="{{ $di_id }}">
                        <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span>
                        <span class="list_column col_product_upc">{{ $di_barcode }}</span>
                        <span class="list_column col_product_name">{{ $di_product_name }}</span>
                        <span class="list_column col_qty"><input type="text" class="form-control list_text_field list_product_qty num_item" data-num_flags="+i" value="{{ $di_qty }}" maxlength="6" /></span>
                        <span class="list_column col_unit_price"><input type="text" class="noProp form-control list_text_field list_product_unit_price num_item" value="{{ $di_unit_price }}"></span>
                        <span class="list_column col_item_total list_item_total">{{ $di_total_price }}</span>
                        <span class="list_column col_remove_button"><span class="list_delete_single_item_button glyphicon glyphicon-remove"></span></span>
                    </div>
                    @endforeach
                @endif
            </div>
            <div class="list_button_row">
            	<div class="list_buttons_column">
                    @if ($is_allow_delete)
					<button id="list_delete_multi_item_button" class="btn btn-default btn-sm">Remove</button>
                    @endif
				</div>
				<div class="list_total_items_column">
                    <div class="fL w100">
                        <div class="total_label">Total Items:</div>
                        <div id="total_row_items" class="fL">{{ $d_total_items }}</div>
                    </div>
                    <div class="fL w100">
                        <div class="total_label">Total Qty:</div>
                        <div id="total_qty" class="fL">{{ $d_total_qty }}</div>
                    </div>
				</div>
            </div>
        </div>
        <div class="fL crRed bold fs13 w100" id="no_list_item_error_message">At least 1 item must be added to the list</div>
    </div>

    <div class="bottom_block">
        <form id="form_purchase_order_bottom">
            <div class="comment_block">
                <div class="fL w100"><textarea id="remarks" class="form-control remark_textarea" placeholder="Remarks">{{ $d_remarks }}</textarea></div>
            </div>
            <div class="total_amount_block">
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Total:</div>
                    <div class="total_amount_row_text" id="total_amount">$<span id="total_amount_value">{{ $d_total_amount }}</span></div>
                </div>
                <div class="total_amount_row">
                    <div class="total_amount_row_label"><label for="payment_amount">Payment Amount:</label></div>
                    <div class="total_amount_row_text">
                        <span class="fL">- $ </span>
                        <input type="text" name="payment_amount" id="payment_amount" class="form-control payment_amount_text_field input-sm num_item" value="{{ $d_payment_amount }}" />
                    </div>
                </div>
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Sub Total:</div>
                    <div class="total_amount_row_text" id="sub_total_amount">$<span id="sub_total_amount_value">{{ $d_sub_total_amount }}</span></div>
                    <div class="fL w100 crRed fs13 bold" id="sub_total_error_message">Sub total can't be negative</div>
                </div>
            </div>
        </form>
    </div>

    <div class="list_page_buttons_row">
        @if ($record_id === 0 && $is_allow_create || $record_id > 0 && $is_allow_update)
        <button id="form_confirm_button" class="btn btn-default btn-sm list_page_buttons">Confirm</button>
        <button id="reset_all_button" class="btn btn-default btn-sm list_page_buttons">Reset All</button>       
        @endif
        @if ($is_allow_print)
        <button id="print_button" class="btn btn-default btn-sm list_page_buttons" {{ ($record_id < 1) ? 'style="display: none"' : null }}>Print</button>
        @endif
        @if ($is_allow_delete)
        <button id="delete_button" class="btn btn-default btn-sm list_page_buttons" {{ ($record_id > 0) ? null : 'style="display: none"' }}>Delete</button>
        @endif
        <button data-redirect_page="deposit/list" class="btn btn-default btn-sm list_page_buttons redirect_button">Return to List</button>
        @if ($is_allow_create)
        <button class="btn btn-primary btn-sm redirect_button" id="create_new_record_button" data-redirect_page="deposit/edit" {{ ($record_id > 0) ? null : 'style="display: none"' }}>Create New Record</button>
        @endif
    </div>

    <div class="panel panel-success result_alert_box" id="update_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Update deposit order successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="edit_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to edit deposit order. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="delete_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to delete deposit record. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    @if ($record_id > 0)
    <input type="hidden" id="record_id" value="{{ $record_id }}" />
    @endif
</div>
@stop
