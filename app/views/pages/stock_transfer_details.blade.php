@section('content')
<?php extract($stock_transfer_data, EXTR_PREFIX_ALL, 'st'); ?>
@if ($st_status == 1)
    @include('popups.pages.stock_transfer_confirm_deliver')
@endif
<div id="stock_transfer_details" class="page_content">
	<div class="id_row">
		<div class="id_label">Stock Transfer No.:</div>
		{{ $st_stock_transfer_number }}
	</div>

    <div class="left_column">
        <div class="list_container">
            <div id="item_list" class="item_list">
            	<div class="list_header">
					<span class="list_column col_product_upc">UPC</span>
                    <span class="list_column col_product_name">Product Name</span>
                    <span class="list_column col_qty">Qty</span>
                </div>
                <div class="list_items" id="list_items">
                    @foreach ($stock_transfer_items_data as $value)
                        <?php extract($value, EXTR_PREFIX_ALL, 'sti'); ?>
                    <div class="list_item_row">
                        <span class="list_column col_product_upc">{{ $sti_barcode }}</span>
                        <span class="list_column col_product_name">{{ $sti_product_name }}
                            @if (!empty($sti_serial_number))
                            <br /><span class="product_sn">S/N: {{ $sti_serial_number }}</span>
                            @endif
                        </span>
                        <span class="list_column col_qty">{{ $sti_qty }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="list_button_row">
                	<div class="list_buttons_column">&nbsp;</div>
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
        </div>
    </div>

    <div class="right_column">
        <div class="form_block">
        	<div class="form_title">Stock Transfer Info</div>
            <div class="form_row">
                <div class="form_label">Status:</div>
                <span id="stock_transfer_status">{{ $st_status_text }}</span>
            </div>
            <div class="form_row">
                <div class="form_label">Date Out:</div>
                {{ $st_date_out }}
            </div>
            <div class="form_row">
                <div class="form_label">From Shop:</div>
                <span id="from_shop_code">{{ $st_from_shop_code }}</span>
            </div>
			<div class="form_row">
				<div class="form_label">To Shop:</div>
                <span id="to_shop_code">{{ $st_to_shop_code }}</span>
			</div>
        	<div class="form_row">
                <div class="form_label">Staff: </div>
                {{ $st_issue_staff }}
            </div>
        	<div class="form_row">
                <div class="form_label">Request by:</div>
                {{ $st_request_by }}
            </div>
            <div><strong>Remarks:</strong></div>
            <div>{{ $st_remarks }}</div>
        </div>

        <div id="delivery_info_block" class="form_block {{ ($st_status == 1) ? 'hide' : null }}">
            <div class="form_title">Delivery Info</div>
            <div class="form_row">
                <label for="date_in" class="form_label">Date In:</label>
                <div class="delivery_info_field" id="date_in_text">
                    {{ ($st_status == 3 || $st_status == 4) ? $st_date_in : '---' }}
                </div>
            </div>
            <div class="form_row">
                <label for="deliver_by" class="form_label">Deliver by:</label>
                <div class="delivery_info_field" id="deliver_by_text">
                    {{ ($st_status == 3 || $st_status == 4) ? $st_deliver_by : '---' }}
                </div>
            </div>
            <div class="form_row">
                <label for="receive_by" class="form_label">Received by:</label>
                <div class="delivery_info_field" id="receive_by_text">
                    {{ ($st_status == 3 || $st_status == 4) ? $st_receive_by : '---' }}
                </div>
            </div>
        </div>

		@if ($is_allow_confirm)
		<div id="retransfer_stock_items_block" class="form_block {{ ($st_status != 4) ? 'hide' : '' }}">
			<div class="form_title">Re-Transfer Stock Items</div>
			<label class="add_stock_form_label">New To Shop:</label><br />
			<select class="text_field form-control" id="new_to_shop">
				@foreach ($shop_list as $shop)
					<?php extract($shop); ?>
				<option value="{{ $id }}" {{ ($id == $current_shop_id) ? "disabled" : "" }}>{{ $code }}</option>
				@endforeach
			</select><br /><br />
			<button id="confirm_retransfer_button" class="btn btn-default btn-sm">Confirm Re-transfer</button>
		</div>
		@endif
    </div>

    <div class="submit_button_row">
		@if ($st_status != 5)
			@if ($is_allow_confirm_delivery)
			<button id="confirm_deliver_button" class="btn btn-default btn-sm list_page_buttons {{ ($st_status != 1) ? 'hide' : '' }}">Confirm Deliver</button>
			@endif
			@if ($is_allow_finish)
			<button id="mark_finish_button" class="btn btn-default btn-sm list_page_buttons {{ ($st_status != 4) ? 'hide' : '' }}">Mark Finished</button>
			@endif
			@if ($is_allow_cancel)
			<button id="cancel_button" class="btn btn-default btn-sm list_page_buttons {{ ($st_status != 1) ? 'hide' : '' }}">Cancel Transfer</button>
			@endif
			@if ($is_allow_print)
			<button id="print_button" class="btn btn-default btn-sm list_page_buttons">Print</button>
			@endif
		@endif
        <button data-redirect_page="stock_transfer/list" class="btn btn-default btn-sm list_page_buttons redirect_button">Return to List</button>
        @if ($is_allow_create)
        <button data-redirect_page="stock_transfer/edit" class="btn btn-primary btn-sm redirect_button" style="margin-left: 20px;">Create New Stock Transfer</button>
        @endif
    </div>

    <div class="panel panel-success result_alert_box" id="update_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Update stock transfer successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="update_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to update stock transfer. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-success result_alert_box" id="finish_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Finish stock transfer request successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="finish_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to finish stock transfer request. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-success result_alert_box" id="retransfer_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Re-transfer stock transfer request successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="retransfer_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to re-transfer stock transfer request. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-success result_alert_box" id="cancel_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Cancel stock transfer successfully.<br />
			Stock items is returned back to the original shop<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="cancel_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to cancel stock transfer order. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <input type="hidden" id="record_id" value="{{ $record_id }}" />
</div>
@stop
