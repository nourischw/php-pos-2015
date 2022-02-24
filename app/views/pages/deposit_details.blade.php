@section('content')
<?php extract($deposit_data, EXTR_PREFIX_ALL, 'd'); ?>
<div id="deposit_details" class="page_content">
    <div class="id_row">
        <div class="id_label">Deposit Number:</div>
        {{ $d_deposit_number }}
    </div>
    
    <div class="left_column">
        <div class="list_container" id="list_deposit_details">
            <div id="item_list" class="item_list">
                <div class="list_header">
                    <span class="list_column col_product_upc">UPC</span>
                    <span class="list_column col_product_name">Product Name</span>
                    <span class="list_column col_qty">Qty</span>
                    <span class="list_column col_unit_price">Unit Price</span>
                    <span class="list_column col_item_total">Total</span>
                </div>
                <div class="list_items" id="list_items">
                    @foreach ($deposit_items_data as $value)
                        <?php extract($value, EXTR_PREFIX_ALL, 'di'); ?>
                    <div class="list_item_row">
                        <span class="list_column col_product_upc">{{ $di_barcode }}</span>
                        <span class="list_column col_product_name">{{ $di_product_name }}</span>
                        <span class="list_column col_qty">{{ $di_qty }}</span>
                        <span class="list_column col_unit_price">{{ $di_unit_price }}</span>
                        <span class="list_column col_item_total list_item_total">{{ $di_total_price }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="list_total_row">
                    <div class="total_field_column total_items_field">
                        <div class="fL total_label">Total Items:</div>
                        <div class="fL">{{ $d_total_items }}</div>
                    </div>
                    <div class="total_field_column">
                        <div class="fL total_label">Total Qty:</div>
                        <div class="fL">{{ $d_total_qty }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bottom_block">
            <div class="total_amount_block">
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Total:</div>
                    <div class="total_amount_row_text">${{ $d_total_amount }}</div>
                </div>
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Payment Amt:</div>
                    <div class="total_amount_row_text">- ${{ $d_payment_amount }}</div>
                </div>
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Sub Total:</div>
                    <div class="total_amount_row_text">${{ $d_sub_total_amount }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="right_column">
        <div class="form_block">
            <div class="form_title">Deposit Info</div>
            <div class="form_row">
                <div class="form_label">Status:</div>
                <div class="fL" id="status_text">{{ $d_status_text }}</div>
            </div>
            <div class="form_row">
                <div class="form_label">Date:</div>
                {{ $d_deposit_date }}
            </div>
            <div class="form_row">
                <div class="form_label">Shop Code:</div>
                {{ $d_shop_code }}
            </div>
            <div class="form_row">
                <div class="form_label">Staff Code:</div>
                {{ $d_staff_code }}
            </div>
            <div class="form_row">
                <div class="form_label">Quot No.:</div>
                {{ $d_quotation_number }}
            </div>
            <div class="form_title">Payment Info</div>
            <div class="form_row">
                <div class="form_label">Dep Terms:</div>
                {{ $deposit_terms[$d_deposit_terms] }}
            </div>
            <div class="form_row">
                <div class="form_label">Pay Type:</div>
                {{ $payment_type[$d_payment_type] }}
            </div>
            <div class="form_row">
                <div class="form_label">Cheque No.:</div>
                {{ $d_cheque_number }}
            </div>
            <div class="form_row">
                <div class="form_label">Cheq Date:</div>
                {{ $d_cheque_date }}
            </div>

            <div class="form_title">Remarks</div>
            <div class="form_row">
                {{ $d_remarks}}
            </div>

            <div class="form_title">Record Info</div>
            <div class="form_row">
                <div class="form_label">Create Date:</div>
                {{ $d_create_time }}
            </div>
            <div class="form_row">
                <div class="form_label">Last Update:</div>
                {{ $d_last_update }}
            </div>
            <div class="form_row">
                <div class="form_label">Updated By:</div>
                {{ $d_last_update_by }}
            </div>
        </div>
    </div>

    <div class="list_page_buttons_row">
        <div class="list_page_buttons_column">
            @if ($is_allow_print)
            <button id="print_button" class="btn btn-default btn-sm list_page_buttons">Print</button>
            @endif
            @if ($is_allow_update)
            <button data-redirect_page="deposit/edit/{{ $d_id }}" class="btn btn-default btn-sm btn-sm redirect_button" style="width: 100px;">Update</button>
            @endif
            @if ($is_allow_void)
            <button id="void_button" class="btn btn-default btn-sm list_page_buttons" {{ ($d_status === 1) ? 'style="display: none;"' : null; }}>Void</button>
            @endif
            @if ($is_allow_unvoid)
            <button id="unvoid_button" class="btn btn-default btn-sm list_page_buttons" {{ ($d_status === 0) ? 'style="display: none;"' : null; }}>Unvoid</button>
            @endif
            @if ($is_allow_delete)
            <button id="remove_button" class="btn btn-default btn-sm list_page_buttons">Delete</button>
            @endif
            <button data-redirect_page="deposit/list" class="btn btn-default btn-sm list_page_buttons redirect_button">Return to List</button>
            @if ($is_allow_create)
            <button data-redirect_page="deposit/edit" class="btn btn-primary btn-sm redirect_button" style="margin-left: 20px;">Create New Deposit</button>
            @endif
        </div>
    </div>

    <div class="panel panel-success result_alert_box" id="update_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Update deposit successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="update_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to update deposit. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <input type="hidden" id="record_id" value="{{ $record_id }}" />
</div>
@stop