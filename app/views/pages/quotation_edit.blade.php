@section('content')
@include('popups.product_list')
<?php $is_update = ($record_id > 0) ? true : false ?>
<div id="quotation_edit" class="page_content">
	<div class="id_row">
		<div class="id_label">Quotation No.:</div>
        <div class="fL" id="id_text">{{ ($record_id > 0) ? $q_quotation_number : "(New Quotation record)" }}</div>
	</div>

    <form id="form_quotation_edit">
        <div class="form_block_row">
            <div class="left_column">
                <div class="form_block">
                	<div class="form_title">Quotation Info</div>
                    <div class="form_row">
                        <label class="form_label" for="quote_date">Quote Date:</label>
                        <div class="input-group">
                            <div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
                            <input type="text" class="form-control datepicker calendar_text_field" id="quote_date" value="{{ $q_quote_date }}" />
                        </div>
                    </div>
                    <div class="form_row">
                        <label class="form_label" for="quote_type">Quote Type:</label>
                        <select class="text_field form-control" id="quote_type">
                            @foreach ($quote_type as $id => $text)
                            <option value="{{ $id }}" {{ ($id === $q_quote_type) ? 'selected' : null }}>{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form_row">
                        <label class="form_label" for="quote_terms">Terms:</label>
                        <select class="text_field form-control" id="quote_terms">
                            @foreach ($quote_terms as $id => $text)
                            <option value="{{ $id }}" {{ ($id === $q_quote_terms) ? 'selected' : null }}>{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form_row">
                        <label class="form_label" for="staff_id">Staff ID:</label>
                        <select class="text_field form-control" id="staff_id">
                            @foreach ($staff_list as $index => $staff)
                                <?php extract($staff); ?>
                            <option value="{{ $id }}" {{ ($id === $q_staff_id) ? 'selected' : null }}>{{ $staff_code }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($is_update)
                    <div class="form_row">
                        <label class="form_label">Status:</label>
                        {{ ($q_status == 1) ? 'Voided' : 'Normal' }}
                    </div>
                    @endif
                </div>
    		</div>
            <div class="right_column">
                <div class="form_block">
                    <div class="form_title">Remarks</div>
                    <div class="form_row"><textarea class="form-control remark_block" id="remarks" placeholder="Remarks">{{ $q_remarks }}</textarea></div>
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
                    <input type="text" class="text_field form-control product_qty num_item" data-num_flags="+i" id="product_qty" maxlength="5" />
                </div>
                <div class="unit_price_field">
                    <label class="new_item_label" for="product_unit_price">Unit Price:</label>
                    <input type="text" class="text_field form-control unit_price_text_field num_item validateItem" data-num_flags="+.2" id="product_unit_price" maxlength="10" />
                    <span class="form-error_message" id="error_product_unit_price"></span>
                </div>
                <button id="add_item_button" class="add_item_button btn btn-default">Add</button>
            </div>
        
            <div class="form_row">
                <label class="new_item_label">Product Name:</label>
                <span class="product_name_text" id="product_name_text">---</span>
            </div>
            <div class="form_row">
                <span class="form-error_message" id="error_product_name"></span>
            </div>
        </div>
        <input type="hidden" id="product_upc" />
    </form>

    <div class="list_container">
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
                    @foreach ($quotation_items_data as $value)
                        <?php extract($value, EXTR_PREFIX_ALL, 'qi'); ?>
                    <div class="list_item_row" data-type="old" data-record_id="{{ $qi_id }}" data-product_id="{{ $qi_product_id }}">
                        <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span>
                        <span class="list_column col_product_upc">{{ $qi_barcode }}</span>
                        <span class="list_column col_product_name">{{ $qi_product_name }}</span>
                        <span class="list_column col_qty"><input type="text" class="form-control list_text_field list_product_qty num_item" data-num_flags="+i" value="{{ $qi_qty }}" maxlength="6" /></span>
                        <span class="list_column col_unit_price"><input type="text" class="noProp form-control list_text_field list_product_unit_price num_item" value="{{ $qi_unit_price }}"></span>
                        <span class="list_column col_item_total list_item_total">{{ $qi_total_price }}</span>
                        <span class="list_column col_remove_button"><span class="list_delete_single_item_button glyphicon glyphicon-remove"></span></span>
                    </div>
                    @endforeach
                @endif
            </div>
            <div class="list_button_row">
            	<div class="list_buttons_column">
					<button id="list_delete_multi_item_button" class="btn btn-default btn-sm">Remove</button>
				</div>
				<div class="list_total_items_column">
                    <div class="fL w100">
                        <div class="total_label">Total Items:</div>
                        <div id="total_row_items" class="fL">{{ $q_total_items }}</div>
                    </div>
                    <div class="fL w100">
                        <div class="total_label">Total Qty:</div>
                        <div id="total_qty" class="fL">{{ $q_total_qty }}</div>
                    </div>
				</div>
            </div>
        </div>
        <div class="fL crRed bold fs13 w100" id="no_list_item_error_message">At least 1 item must be added to the list</div>
    </div>

    <div class="bottom_block">
        <form id="form_purchase_order_bottom">
            <div class="comment_block">
                <div class="fL w100"><textarea id="comment" class="form-control comment_textarea" placeholder="Comment">{{ $q_comment }}</textarea></div>
            </div>
            <div class="total_amount_block">
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Total:</div>
                    <div class="total_amount_row_text" id="total_amount">$<span id="total_amount_value">{{ $q_total_amount }}</span></div>
                </div>
                <div class="total_amount_row">
                    <div class="total_amount_row_label"><label for="discount_amount">Discount:</label></div>
                    <div class="total_amount_row_text">
                        <span class="fL">- $ </span>
                        <input type="text" name="discount_amount" id="discount_amount" class="form-control discount_text_field input-sm" value="{{ $q_discount_amount }}" />
                    </div>
                </div>
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Sub Total:</div>
                    <div class="total_amount_row_text" id="sub_total_amount">$<span id="sub_total_amount_value">{{ $q_sub_total_amount }}</span></div>
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
        <button data-redirect_page="quotation/list" class="btn btn-default btn-sm list_page_buttons redirect_button">Return to List</button>
        @if ($is_allow_create)
        <button class="btn btn-primary btn-sm redirect_button" id="create_new_record_button" data-redirect_page="quotation/edit" {{ ($record_id > 0) ? null : 'style="display: none"' }}>Create New Record</button>
        @endif
    </div>

    <div class="panel panel-success result_alert_box" id="update_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Update quotation order successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="edit_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to edit quotation order. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="delete_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to delete quotation record. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    @if ($record_id > 0)
    <input type="hidden" id="record_id" value="{{ $record_id }}" />
    @endif
</div>
@stop