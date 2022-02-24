@section('content')
@include('popups.product_list')
@include('popups.supplier_list')
<div id="purchase_order_edit" class="page_content">
	<div class="id_row">
		<div class="id_label">Purchase Order Number:</div>
        <div class="fL" id="id_text">{{ ($record_id > 0) ? $po_purchase_order_number : "(New Purchase order)" }}</div>
	</div>

    <div class="left_column">
        <div class="left_column_form_block">
            <form id="form_add_product_item">
				<div class="fL w100">
                    <div class="product_name_field input-group">
                        <label class="fL new_item_label" for="product_name">Search Product Name:</label>
                        <div class="fL">
                            <input type="text" id="product_name" class="form-control new_item_text_field product_name_text_field">
                            <span class="input-group-btn text_field_buttons">
                                <button class="btn btn-default text_field_button show_popup_list_button" id="show_product_popup_list_button" data-list_id="product_popup_list">...</button>
                            </span>
                        </div>
                    </div>
					<div class="qty_field">
						<label class="new_item_label" for="product_qty">Qty:</label>
						<input type="text" class="new_item_text_field form-control qty_text_field num_item" data-num_flags="+i" id="product_qty" maxlength="5" />
					</div>
					<div class="unit_price_field">
						<label class="new_item_label" for="product_unit_price">Unit Price:</label>
						<input type="text" class="new_item_text_field form-control unit_price_text_field num_item" data-num_flags="+.2" id="product_unit_price" maxlength="10" />
					</div>
					<button id="add_item_button" class="add_item_button btn btn-default">Add</button>
				</div>

				<div class="fL w100">
					<label class="new_item_label">Product Name:</label>
					<span class="product_name_text" id="product_name_text">---</span>
				</div>
                <input type="hidden" id="product_upc" />
            </form>
        </div>

        <div class="list_container" id="list_purchase_order_edit">
            <div id="item_list" class="item_list">
            	<div class="list_header">
                	<span class="list_checkbox_column">
						<input type="checkbox" class="list_checkbox_all" id="list_checkbox_all" />
					</span>
                    <span class="list_column col_product_upc">UPC</span>
                    <span class="list_column col_product_name">Product Name</span>
                    <span class="list_column col_qty">Qty</span>
                    <span class="list_column col_unit_price">Unit Price</span>
    				<span class="list_column col_item_total">Total</span>
                </div>
                <div class="list_no_items_row" id="list_no_items_row" {{ ($have_list_items) ? 'style="display: none"' : null }}>(Please add products)</div>
                <div class="list_items" id="list_items">
                    @if ($have_list_items)
                        @foreach ($purchase_order_items_data as $value)
                            <?php extract($value, EXTR_PREFIX_ALL, 'poi'); ?>
                        <div class="list_item_row" data-type="old" data-record_id="{{ $poi_id }}">
                            <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span>
                            <span class="list_column col_product_upc">{{ $poi_barcode }}</span>
                            <span class="list_column col_product_name">{{ $poi_name }}</span>
                            <span class="list_column col_qty"><input type"text" class="form-control list_text_field list_product_qty num_item" value="{{ $poi_qty }}"></span>
                            <span class="list_column col_unit_price"><input type"text" class="form-control list_text_field list_product_unit_price num_item" value="{{ $poi_unit_price }}"></span>
                            <span class="list_column col_item_total list_item_total">{{ $poi_total_price }}</span>
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
							<div id="total_row_items" class="fL">{{ $po_total_items }}</div>
						</div>
						<div class="fL w100">
							<div class="total_label">Total Qty:</div>
							<div id="total_qty" class="fL">{{ $po_total_qty }}</div>
						</div>
					</div>
                </div>
            </div>
            <div class="fL crRed bold fs13 w100" id="no_list_item_error_message">At least 1 item must be added to the list</div>
        </div>

        <div class="bottom_block">
            <form id="form_purchase_order_bottom">
                <div class="remark_block">
                    <div class="fL w100"><textarea id="remarks" class="form-control remark_textarea" placeholder="Remarks">{{ $po_remarks }}</textarea></div>
                </div>
                <div class="total_amount_block">
                	<div class="total_amount_row">
                    	<div class="total_amount_row_label">Total:</div>
                        <div class="total_amount_row_text" id="total_amount">$<span id="total_amount_value">{{ $po_total_amount }}</span></div>
                    </div>
                	<div class="total_amount_row">
                    	<div class="total_amount_row_label"><label for="discount_amount">Discount:</label></div>
                        <div class="total_amount_row_text">
    						<span class="fL">- $ </span>
    						<input type="text" name="discount_amount" id="discount_amount" class="form-control discount_text_field input-sm" value="{{ $po_discount_amount }}" />
    					</div>
                    </div>
                	<div class="total_amount_row">
                    	<div class="total_amount_row_label">Net Total:</div>
                        <div class="total_amount_row_text" id="net_total_amount">$<span id="net_total_amount_value">{{ $po_net_amount }}</span></div>
                        <div class="fL w100 crRed fs13 bold net_total_error_message" id="net_total_error_message">Net total can't be negative</div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="right_column">
        <form id="form_purchase_order_info">
            <div class="form_block">
            	<div class="form_title">Purchase Order Info</div>

            	<div class="form_row">
                    <label for="order_date" class="form_label">Order Date:</label>
                    <div class="input-group">
                        <div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
                        <input type="text" class="text_field form-control datepicker" id="order_date" value="{{ $po_order_date }}" />
                    </div>
                    <div class="form-error_message" id="error_order_date"></div>
                </div>
            	<div class="form_row">
                    <label for="staff_code" class="form_label">Staff: </label>
                    <select class="text_field form-control" id="staff_code">
                        @foreach ($staff_list as $index => $staff)
							<?php extract($staff); ?>
                        <option value="{{ $staff_code }}" {{ ($po_staff_code === $staff_code) ? 'selected' : null }}>{{ $staff_code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form_row">
                	<label for="deposit_no" class="form_label">Deposit No.:</label>
                    <input type="text" class="text_field form-control" id="deposit_no" name="deposit_no" value="{{ $po_deposit_no }}" />
                </div>
                <div class="form_row">
                	<label for="ship_to" class="form_label">Ship To:</label>
                    <select class="text_field form-control" id="ship_to">
                        @foreach ($shop_list as $shop)
							<?php extract($shop); ?>
						<option value="{{ $id }}" {{ ($code === 'MCYWH001') ? 'selected' : null; }}>{{ $code }}</option>
						@endforeach
                    </select>
                </div>
            	<div class="form_row">
                    <label for="request_by" class="form_label">Request by:</label>
                    <select class="text_field form-control" id="request_by">
                        @foreach ($staff_list as $staff)
							<?php extract($staff); ?>
                        <option value="{{ $staff_code }}" {{ ($po_request_by === $staff_code) ? 'selected' : null }}>{{ $staff_code }}</option>
                        @endforeach
                    </select>
                </div>
            	<div class="form_row">
                    <label for="payment_type" class="form_label">Payment:</label>
                    <select class="text_field form-control" id="payment_type">
                        @foreach ($payment_type_list as $index => $type)
                        <option value="{{ $index }}" {{ ($index === $po_payment_type) ? 'selected' : null; }}>{{ $index }} - {{ $type }}</option>
                        @endforeach
                    </select>
                    <div class="form-error_message" id="error_payment_type"></div>
                </div>

            	<div class="form_title">Supplier</div>
                <div class="form_row input-group">
                    <label class="form_label"><sup class="crRed">*</sup>Code:</label>
                    <div class="fL">
                        <input type="text" id="supplier_code" class="supplier_text_field form-control validateItem" value="{{ $po_supplier_code }}">
                        <span class="input-group-btn text_field_buttons">
                            <button class="btn btn-default text_field_button show_popup_list_button" id="show_supplier_popup_list_button" data-list_id="supplier_popup_list">...</button>
                            <button class="btn btn-default text_field_button posA" id="get_supplier_info_button"><span class="glyphicon glyphicon-search"></span></button>
                        </span>
                    </div>
                    <div class="form-error_message" id="error_supplier_code"></div>
                </div>
                <div class="w100 fL crRed bold fs12" id="no_valid_supplier_error_message">Must select a valid supplier</div>
                <div class="form_row">
                    <div class="form_label"><label class="form_label">Shop Name:</label></div>
                    <div class="fL" id="supplier_shop_name">{{ (!empty($po_supplier_name)) ? $po_supplier_name : '---' }}</div>
                </div>
                <div class="form_row">
                	<label class="form_label">Mobile:</label>
                    <span id="supplier_mobile">{{ (!empty($po_supplier_mobile)) ? $po_supplier_mobile : '---' }}</span>
                </div>
                <div class="form_row">
                	<label class="form_label">Fax:</label>
                    <span id="supplier_fax">{{ (!empty($po_supplier_fax)) ? $po_supplier_fax : '---' }}</span>
                </div>
                <div class="form_row">
                	<label class="form_label">Email:</label>
                    <span id="supplier_email">{{ (!empty($po_supplier_email)) ? $po_supplier_email : '---' }}</span>
                </div>
            </div>
            <input type="hidden" id="supplier_id" name="supplier_id" value="{{ $po_supplier_id }}" />
        </form>
    </div>

    <div class="list_page_buttons_row">
        @if ($record_id === 0 && $is_allow_create || $record_id > 0 && $is_allow_update)
        <button id="temp_save_button" class="btn btn-default btn-sm list_page_buttons">Temp Save</button>
        @endif
        @if ($is_allow_confirm)
        <button id="confirm_button" class="btn btn-default btn-sm list_page_buttons">Confirm</button>
        @endif
		<button id="reset_all_button" class="btn btn-default btn-sm list_page_buttons">Reset All</button>
        @if ($is_allow_delete)
        <button id="delete_button" class="btn btn-default btn-sm list_page_buttons" {{ ($record_id > 0) ? null : 'style="display: none"' }}>Delete</button>
        @endif
        <button class="btn btn-default btn-sm list_page_buttons redirect_button" data-redirect_page="purchase_order/list">Return to List</button>
        @if ($is_allow_create)
        <button class="btn btn-primary btn-sm redirect_button" id="create_new_record_button" data-redirect_page="purchase_order/edit" {{ ($record_id > 0) ? null : 'style="display: none"' }}>Create New Record</button>
        @endif
    </div>

    <div class="panel panel-success result_alert_box" id="edit_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Edit purchase order successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="edit_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to edit purchase order. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="delete_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to delete purchase order record. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    @if ($record_id > 0)
    <input type="hidden" id="record_id" value="{{ $record_id }}" />
    @endif
</div>
@stop
