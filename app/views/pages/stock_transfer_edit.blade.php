@section('content')
@include('popups.stock_list')
<?php $is_update = ($record_id > 0) ? true : false ?>
<div id="stock_transfer" class="page_content">
	<div class="id_row">
		<div class="id_label">Stock Transfer No.:</div>
        <div class="fL" id="id_text">{{ ($record_id > 0) ? $st_stock_transfer_number : "(New Stock Transfer record)" }}</div>
	</div>

    <div class="left_column">
        <div class="left_column_form_block">
        	<div class="form_title">From Shop Info</div>

			<div class="from_shop_info_row">
				<label class="add_stock_form_label"><sup class="crRed">*</sup>From Shop:</label>
				<div id="select_from_shop_block" {{ ($is_update) ? 'style="display: none"' : null }}>
					<select class="text_field form-control" id="from_shop">
						@foreach ($shop_list as $shop)
							<?php extract($shop); ?>
						<option value="{{ $id }}" {{ ($code == 'MCYWH001') ? 'selected' : null }}>{{ $code }}</option>
						@endforeach
					</select>
					<button id="select_from_shop_button" class="fL btn btn-default btn-xs" style="margin-left: 10px; height: 25px;">Select</button>
					<div class="fL crRed bold" style="margin-left: 10px;">Please select From Shop first</div>
				</div>
				<div id="from_shop_info">
					<div class="fL" id="from_shop_name_text">
                        {{ ($is_update) ? $st_from_shop_code : '---' }}
                    </div>
					<button id="change_from_shop_button" class="btn btn-default btn-xs">Change</button>
				</div>
			</div>
			<input type="hidden" id="from_shop_id" {{ ($is_update) ? 'value="' . $st_from_shop_id . '"' : null }} />
			<div class="fL">
				<button class="btn btn-default btn-sm stock_block_buttons show_popup_list_button" id="search_stock_popup_list_button" data-list_id="stock_popup_list" />Search Product</button>
			</div>
			
			<div class="fL w100 crRed bold" id="item_already_exist_message">Item already exist in the list</div>
		</div>
		
        <div class="list_container">
            <div id="item_list" class="item_list">
            	<div class="list_header">
                	<span class="list_checkbox_column"><input type="checkbox" class="list_checkbox_all" id="list_checkbox_all" /></span>
					<span class="list_column col_product_upc">UPC</span>
                    <span class="list_column col_product_name">Product Name</span>
                    <span class="list_column col_qty">Qty</span>
                    <span class="list_column col_remain">Remain</span>
                </div>
                <div class="list_no_items_row" id="list_no_items_row">(Please add products)</div>
                <div class="list_items" id="list_items">
                    @if ($have_list_item)
                        @foreach ($stock_transfer_items_data as $value)
                            <?php 
								extract($value, EXTR_PREFIX_ALL, 'sti'); 
								$sti_qty = ($sti_qty > $sti_remain_qty) ? $sti_remain_qty : $sti_qty;
								$is_invalid = ($sti_remain_qty <= 0) ? true : false;
							?>
                        <div class="list_item_row {{ ($is_invalid) ? 'invalid_item' : '' }}" id="stock_id_{{ $sti_stock_id }}" data-type="old" data-record_id="{{ $sti_id }}" data-stock_id="{{ $sti_stock_id }}">
                            <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span>
                            <span class="list_column col_product_upc">{{ $sti_barcode }}</span>
                            <span class="list_column col_product_name">{{ $sti_product_name }}
                                @if (!empty($sti_serial_number))
                                <br /><span class="product_sn">S/N: {{ $sti_serial_number }}</span>
                                @endif
                            </span>
                            <span class="list_column col_qty">
								@if ($is_invalid)
								---
								@else
								<input type="text" class="form-control list_transfer_qty num_item" data-num_flags="+i" value="{{ $sti_qty }}" maxlength="6" />
								@endif
							</span>
                            <span class="list_column col_remain">{{ $sti_remain_qty }}</span>
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
                            <div id="total_list_items" class="fL">{{ $st_total_items }}</div>
                        </div>
                        <div class="fL w100">
                            <div class="total_label">Total Qty:</div>
                            <div id="total_qty" class="fL">{{ $st_total_qty }}</div>
                        </div>
					</div>
                </div>
            </div>
            <div class="fL crRed bold fs13 w100" id="no_list_item_error_message">At least 1 item must be added to the list</div>
			<div class="fL crRed bold fs13 w100 hide" id="item_qty_changed_message">
				Some items' remain qty is zero or requested transfer qty is exceed the stock item's remain qty.<br />
				Please remove the out of stock list items and/or modify the request transfer qty of the list item
			</div>
        </div>
    </div>
    
    <div class="right_column">
        <form id="form_stock_transfer_info">
            <div class="form_block">
            	<div class="form_title">Stock Transfer Info</div>
                <div class="form_row">
                    <label for="transfer_date" class="form_label">Date Out:</label>
                    <div class="input-group">
                        <div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
                        <input type="text" class="text_field form-control datepicker" id="date_out" value="{{ $st_date_out }}" />
                    </div>
                    <div class="form-error_message" id="error_transfer_date"></div>
                </div>
				<div class="form_row">
					<label for="to_shop" class="form_label">To Shop:</label>
					<select class="text_field form-control" id="to_shop">
						@foreach ($shop_list as $shop)
							<?php extract($shop); ?>
						<option value="{{ $id }}" {{ ($id === $st_to_shop_id) ? 'selected' : null }}>{{ $code }}</option>
						@endforeach
					</select>
					<div class="form-error_message" id="error_to_shop"></div>
				</div>
            	<div class="form_row">
                    <label for="staff_id" class="form_label">Staff:</label>
                    <select class="text_field form-control" id="staff_id">
                        @foreach ($staff_list as $index => $staff)
							<?php extract($staff); ?>
                        <option value="{{ $id }}" {{ ($id === $st_staff_id) ? 'selected' : null }}>{{ $staff_code }}</option>
                        @endforeach
                    </select>
                    <div class="form-error_message" id="error_staff_id"></div>
                </div>
            	<div class="form_row">
                    <label for="request_by" class="form_label">Request by:</label>
                    <select class="text_field form-control" id="request_by">
                        @foreach ($staff_list as $index => $staff)
							<?php extract($staff); ?>
                        <option value="{{ $staff_code }}" {{ ($staff_code === $st_request_by) ? 'selected' : null }}>{{ $staff_code }}</option>
                        @endforeach
                    </select>
                    <div class="form-error_message" id="error_request_by"></div>
                </div>
                <div><strong>Remarks:</strong></div>
                <div><textarea id="remarks" class="form-control remark_textarea" placeholder="Remarks">{{ $st_remarks }}</textarea></div>
            </div>
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
        <button class="btn btn-default btn-sm list_page_buttons redirect_button" data-redirect_page="stock_transfer/list">Return to List</button>
        @if ($is_allow_create)
        <button class="btn btn-primary btn-sm redirect_button" id="create_new_record_button" data-redirect_page="stock_transfer/edit" {{ ($record_id > 0) ? null : 'style="display: none"' }}>Create New Record</button>
        @endif
    </div>

    <div class="panel panel-success result_alert_box" id="edit_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Create stock transfer order successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
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
            Failed to create stock transfer order. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="delete_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to delete stock transfer record. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    @if ($record_id > 0)
    <input type="hidden" id="record_id" value="{{ $record_id }}" />
    @endif
</div>
@stop