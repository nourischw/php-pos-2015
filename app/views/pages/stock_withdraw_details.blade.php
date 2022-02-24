@section('content')
<?php extract($stock_withdraw_data, EXTR_PREFIX_ALL, 'sw'); ?>
@if ($sw_status == 1)
    @include('popups.pages.stock_withdraw_finish')
@endif
<div id="stock_withdraw_details" class="page_content">
	<div class="id_row">
		<div class="id_label">Stock Withdraw ID:</div>
		{{ $sw_id }}
	</div>

    <div class="left_column">
        <div class="list_container">
            <div id="item_list" class="item_list">
            	<div class="list_header">
                    <span class="list_column col_gi_code">GI Code</span>
					<span class="list_column col_product_upc">UPC</span>
                    <span class="list_column col_product_name">Product Name</span>
                    <span class="list_column col_qty">Qty</span>
                    <span class="list_column col_price">Price</span>
                    <span class="list_column col_price">Total</span>
                </div>
                <div class="list_items" id="list_items">
                    @foreach ($stock_withdraw_items_data as $value)
                        <?php extract($value, EXTR_PREFIX_ALL, 'swi'); ?>
                    <div class="list_item_row">
                        <span class="list_column col_gi_code">{{ $swi_gi_code }}</span>
                        <span class="list_column col_product_upc">{{ $swi_barcode }}</span>
                        <span class="list_column col_product_name">{{ $swi_product_name }}
                            @if (!empty($swi_serial_number))
                            <br /><span class="product_sn">S/N: {{ $swi_serial_number }}</span>
                            @endif
                        </span>
                        <span class="list_column col_qty">{{ $swi_qty }}</span>
                        <span class="list_column col_price">{{ $swi_price }}</span>
                        <span class="list_column col_price">{{ $swi_total_price }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="list_button_row">
                	<div class="list_buttons_column">&nbsp;</div>
					<div class="list_total_items_column">
						<div class="fL w100">
							<div class="total_label">Total Items:</div>
							<div id="total_list_items" class="fL">{{ $sw_total_items }}</div>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="right_column">
        <div class="form_block">
        	<div class="form_title">Stock Withdraw Info</div>
            <div class="form_row">
                <div class="form_label">Status:</div>
                <span id="stock_transfer_status">{{ $sw_status_text }}</span>
            </div>
            <div class="form_row">
                <div class="form_label">Date:</div>
                {{ $sw_withdraw_date }}
            </div>
        	<div class="form_row">
                <div class="form_label">Request by:</div>
                {{ $sw_create_by }}
            </div>
            <div class="form_row">
                <div class="form_label">Request at:</div>
                {{ $sw_create_date }}
            </div>
            <div class="form_row finish_row {{ ($sw_status == 1) ? 'hide' : '' }}">
                <div class="form_label">Finish by:</div>
                <span id="finished_by_text">{{ $sw_finished_by }}</span>
            </div>
            <div class="form_row finish_row {{ ($sw_status == 1) ? 'hide' : '' }}">
                <div class="form_label">Finish at:</div>
                <span id="finished_date_text">{{ $sw_finished_date }}</span>
            </div>
            <div><strong>Remarks:</strong></div>
            <div>{{ $sw_remarks }}</div>
        </div>
    </div>
	
    <div class="bottom_block">
        <div class="remark_block"></div>
        <div class="total_amount_block">
            <div class="total_amount_row">
                <div class="total_amount_row_label">Total:</div>
                <div class="total_amount_row_text" id="total_amount">${{ $sw_total_amount }}</div>
            </div>
        </div>
    </div>

    <div class="submit_button_row">
		@if ($sw_status != 2)
			@if ($is_allow_finish)
			<button id="confirm_button" class="btn btn-default btn-sm list_page_buttons">Mark Finished</button>
			@endif
			@if ($is_allow_delete)
			<button id="delete_button" class="btn btn-default btn-sm list_page_buttons">Delete</button>
			@endif
		@endif
        <button data-redirect_page="stock_withdraw/list" class="btn btn-default btn-sm list_page_buttons redirect_button">Return to List</button>
        @if ($is_allow_create)
        <button data-redirect_page="stock_withdraw/edit" class="btn btn-primary btn-sm redirect_button" style="margin-left: 20px;">Create New Stock Withdraw</button>
        @endif
    </div>

    <div class="panel panel-success result_alert_box" id="update_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Update stock withdraw record successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="update_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to update stock withdraw record. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>
	
    <div class="panel panel-danger result_alert_box" id="delete_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to delete stock withdraw order. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>
	
    <input type="hidden" id="record_id" value="{{ $record_id }}" />
</div>
@stop