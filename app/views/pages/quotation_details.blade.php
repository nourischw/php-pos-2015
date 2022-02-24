@section('content')
<?php extract($quotation_data, EXTR_PREFIX_ALL, 'q'); ?>
<div id="quotation_details" class="page_content">
    <div class="id_row">
        <div class="id_label">Quotation Number:</div>
        {{ $q_quotation_number }}
    </div>
    
    <div class="left_column">
        <div class="list_container" id="list_quotation_details">
            <div id="item_list" class="item_list">
                <div class="list_header">
                    <span class="list_column col_product_upc">UPC</span>
                    <span class="list_column col_product_name">Product Name</span>
                    <span class="list_column col_qty">Qty</span>
                    <span class="list_column col_unit_price">Unit Price</span>
                    <span class="list_column col_item_total">Total</span>
                </div>
                <div class="list_items" id="list_items">
                    @foreach ($quotation_items_data as $value)
                        <?php extract($value, EXTR_PREFIX_ALL, 'qi'); ?>
                    <div class="list_item_row">
                        <span class="list_column col_product_upc">{{ $qi_barcode }}</span>
                        <span class="list_column col_product_name">{{ $qi_product_name }}</span>
                        <span class="list_column col_qty">{{ $qi_qty }}</span>
                        <span class="list_column col_unit_price">{{ $qi_unit_price }}</span>
                        <span class="list_column col_item_total list_item_total">{{ $qi_total_price }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="list_total_row">
                    <div class="total_field_column total_items_field">
                        <div class="fL total_label">Total Items:</div>
                        <div class="fL">{{ $q_total_items }}</div>
                    </div>
                    <div class="total_field_column">
                        <div class="fL total_label">Total Qty:</div>
                        <div class="fL">{{ $q_total_qty }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bottom_block">
            <div class="remark_block">
                <strong class="fs16">Comments:</strong><br />
                <div class="fL w100">{{ $q_comment }}</div>
            </div>
            <div class="total_amount_block">
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Total:</div>
                    <div class="total_amount_row_text">${{ $q_total_amount }}</div>
                </div>
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Discount:</div>
                    <div class="total_amount_row_text">- ${{ $q_discount_amount }}</div>
                </div>
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Sub Total:</div>
                    <div class="total_amount_row_text">${{ $q_sub_total_amount }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="right_column">
        <div class="form_block">
            <div class="form_title">Quotation Info</div>
            <div class="form_row">
                <div class="form_label">Status:</div>
                <div class="fL" id="status_text">{{ $q_status_text }}</div>
            </div>
            <div class="form_row">
                <div class="form_label">Quote Date:</div>
                {{ $q_quote_date }}
            </div>
            <div class="form_row">
                <div class="form_label">Quote:</div>
                {{ $q_quote_type }}
            </div>
            <div class="form_row">
                <div class="form_label">Terms:</div>
                {{ $q_quote_terms }}
            </div>
            <div class="form_row">
                <div class="form_label">Staff Code:</div>
                {{ $q_staff_code }}
            </div>
            
            <div class="form_title">Remarks</div>
            <div class="form_row">
                {{ $q_remarks}}
            </div>

            <div class="form_title">Record Info</div>
            <div class="form_row">
                <div class="form_label">Create Date:</div>
                {{ $q_create_time }}
            </div>
            <div class="form_row">
                <div class="form_label">Last Update:</div>
                {{ $q_last_update }}
            </div>
            <div class="form_row">
                <div class="form_label">Updated By:</div>
                {{ $q_last_update_by }}
            </div>
        </div>
    </div>

    <div class="list_page_buttons_row">
        @if ($is_allow_print)
        <button id="print_button" class="btn btn-default btn-sm list_page_buttons">Print</button>
        @endif
        @if ($is_allow_update)
        <button data-redirect_page="quotation/edit/{{ $q_id }}" class="btn btn-default btn-sm redirect_button" style="width: 100px">Update</button>
        @endif
        @if ($is_allow_delete)
        <button id="remove_button" class="btn btn-default btn-sm list_page_buttons">Delete</button>
        @endif
        @if ($is_allow_void)
        <button id="void_button" class="btn btn-default btn-sm list_page_buttons" {{ ($q_status == 1) ? 'style="display: none;"' : null; }}>Void</button>
        @endif
        @if ($is_allow_unvoid)
        <button id="unvoid_button" class="btn btn-default btn-sm list_page_buttons" {{ ($q_status == 0) ? 'style="display: none;"' : null; }}>Unvoid</button>
        @endif
        <button data-redirect_page="quotation/list" class="btn btn-default btn-sm list_page_buttons redirect_button">Return to List</button>
        @if ($is_allow_create)
        <button data-redirect_page="quotation/edit" class="btn btn-primary btn-sm redirect_button" style="margin-left: 20px;">Create New Quotation</button>
        @endif
    </div>

    <div class="panel panel-success result_alert_box" id="update_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Update quotation successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="update_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to update quotation. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <input type="hidden" id="record_id" value="{{ $record_id }}" />
</div>
@stop