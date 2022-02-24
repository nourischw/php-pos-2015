@section('content')
<?php extract($purchase_order_data, EXTR_PREFIX_ALL, 'po'); ?>
<div id="purchase_order_edit" class="page_content">
    <div class="id_row">
        <div class="id_label">Purchase Order Number:</div>
        {{ $po_purchase_order_number }}
    </div>
    
    <div class="left_column">
        <div class="list_container" id="list_purchase_order_edit">
            <div id="item_list" class="item_list">
                <div class="list_header">
                    <span class="list_column col_product_upc">UPC</span>
                    <span class="list_column col_product_name">Product Name</span>
                    <span class="list_column col_qty">Qty</span>
                    <span class="list_column col_unit_price">Unit Price</span>
                    <span class="list_column col_item_total">Total</span>
                </div>
                <div class="list_items" id="list_items">
                    @foreach ($purchase_order_items_data as $value)
                        <?php extract($value, EXTR_PREFIX_ALL, 'poi'); ?>
                    <div class="list_item_row">
                        <span class="list_column col_product_upc">{{ $poi_barcode }}</span>
                        <span class="list_column col_product_name">{{ $poi_name }}</span>
                        <span class="list_column col_qty">{{ $poi_qty }}</span>
                        <span class="list_column col_unit_price">{{ $poi_unit_price }}</span>
                        <span class="list_column col_item_total list_item_total">{{ $poi_total_price }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="list_total_row">
                    <div class="total_field_column total_items_field">
                        <div class="fL total_label">Total Items:</div>
                        <div class="fL">{{ $po_total_items }}</div>
                    </div>
                    <div class="total_field_column">
                        <div class="fL total_label">Total Qty:</div>
                        <div class="fL">{{ $po_total_qty }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bottom_block">
            <div class="remark_block">
                <strong class="fs16">Remarks:</strong><br />
                <div class="fL w100">{{ $po_remarks }}</div>
            </div>
            <div class="total_amount_block">
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Total:</div>
                    <div class="total_amount_row_text">${{ $po_total_amount }}</div>
                </div>
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Discount:</div>
                    <div class="total_amount_row_text">- ${{ $po_discount_amount }}</div>
                </div>
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Net Total:</div>
                    <div class="total_amount_row_text">${{ $po_net_amount }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="right_column">
        <div class="form_block">
            <div class="form_title">Purchase Order Info</div>
            <div class="form_row">
                <div class="form_label">Status:</div>
                <div class="fL" id="status_text">{{ $po_status_text }}</div>
            </div>
            <div class="form_row">
                <div class="form_label">Order Date:</div>
                {{ $po_order_date }}
            </div>
            <div class="form_row">
                <div class="form_label">Staff:</div>
                {{ $po_staff_code }}
            </div>
            <div class="form_row">
                <div class="form_label">Deposit No.:</div>
                {{ (!empty($po_deposit_no)) ? $po_deposit_no : '---' }}
            </div>
            <div class="form_row">
                <div class="form_label">Ship To:</div>
                {{ $po_ship_to_shop }}
            </div>
            <div class="form_row">
                <div class="form_label">Request by:</div>
                {{ $po_request_by }}
            </div>
            <div class="form_row">
                <div class="form_label">Payment:</div>
                {{ $po_payment_type_text }}
            </div>

            @if ($po_status === 4)
            <div class="form_row" id="deliver_by_row">
                <div class="form_label">Delvery By:</div>
                <div id="deliver_by_text">{{ $po_deliver_by }}</div>
            </div>
            @endif

            <div class="form_title">Supplier</div>
            <div class="form_row">
                <div class="form_label">Code:</div>
                {{ $po_supplier_code }}
            </div>
            <div class="form_row">
                <div class="form_label">Shop Name:</div>
                <div class="supplier_shop_name">{{ $po_supplier_name }}</div>
            </div>
            <div class="form_row">
                <div class="form_label">Mobile:</div>
                {{ (empty($po_supplier_mobile)) ? '---' : $po_supplier_mobile }}
            </div>
            <div class="form_row">
                <div class="form_label">Fax:</div>
                {{ (empty($po_supplier_fax)) ? '---' : $po_supplier_fax }}
            </div>
            <div class="form_row">
                <div class="form_label">Email:</div>
                {{ (empty($po_supplier_email)) ? '---' : $po_supplier_email }}
            </div>

            <div class="form_title">Record Info</div>
            <div class="form_row">
                <div class="form_label">Create Date:</div>
                {{ $po_create_time }}
            </div>
            <div class="form_row">
                <div class="form_label">Last Update:</div>
                {{ $po_last_update }}
            </div>
            <div class="form_row">
                <div class="form_label">Updated By:</div>
                {{ $po_last_update_by }}
            </div>
        </div>
    </div>

    <div class="list_page_buttons_row">
        <div class="list_page_buttons_column">
            @if ($is_allow_print)
            <button id="print_button" class="btn btn-default btn-sm list_page_buttons">Print</button>
            @endif
            @if ($po_status !== 4)
                @if ($is_allow_void)
            <button id="void_button" class="btn btn-default btn-sm list_page_buttons" {{ ($po_status === 3) ? 'style="display: none;"' : null; }}>Void</button>
                @endif
                @if ($is_allow_unvoid)
            <button id="unvoid_button" class="btn btn-default btn-sm list_page_buttons" {{ ($po_status === 1) ? 'style="display: none;"' : null; }}>Unvoid</button>
                @endif
            @endif
            <button data-redirect_page="purchase_order/list" class="btn btn-default btn-sm list_page_buttons redirect_button">Return to List</button>
            @if ($is_allow_create)
            <button data-redirect_page="purchase_order/edit" class="btn btn-primary btn-sm redirect_button" style="margin-left: 20px;">Create New Purchase Order</button>
            @endif
        </div>
    </div>

    <div class="panel panel-success result_alert_box" id="update_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Update purchase order successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="update_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to update purchase order. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <input type="hidden" id="record_id" value="{{ $record_id }}" />
</div>
@stop