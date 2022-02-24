@section('content')
<?php extract($sales_invoice_data[0], EXTR_PREFIX_ALL, 'si'); ?>
<div id="sales_invoice_deatail" class="page_content">
    <div class="id_row">
        <div class="id_label">Sales Invoice Number:</div>
        {{ $si_sales_invoice_number }}
    </div>

    <div class="left_column">
        <div class="list_container" id="list_sales_invoice_deatail">
			<div class="fL color_message_block">
				<div class="fL item_color">
					<div class="fL item_type item_type_d">&nbsp;</div>
					<div class="fL">Deposit Item</div>
				</div>
				<div class="fL item_color">
					<div class="fL item_type item_type_q">&nbsp;</div>
					<div class="fL">Quotation Item</div>
				</div>			
			</div>		
            <div id="fL item_list" class="item_list">
                <div class="list_header">
                    <span class="list_column col_product_upc">UPC</span>
                    <span class="list_column col_product_name">Product Name</span>
                    <span class="list_column col_serial_number">Serial Number</span>
                    <span class="list_column col_unit_price">Unit Price</span>
                    <span class="list_column col_qty">Qty</span>
                    <span class="list_column col_discount">discount</span>
                    <span class="list_column col_item_total">Total</span>
                </div>
                <div class="list_items" id="list_items">
                    @foreach ($sales_invoice_items_data as $value)
                    <?php
						extract($value, EXTR_PREFIX_ALL, 'sii'); 
						$deposit_class = ($sii_deposit_id) != 0 ? "deposit_item" : null;
						$quotation_class = ($sii_quotation_id) != 0 ? "quotation_item" : null;
					?>
                    <div class="list_item_row {{ $deposit_class }} {{ $quotation_class }}">
                        <span class="list_column col_product_upc">{{ $sii_product_code }}</span>
                        <span class="list_column col_product_name">{{ $sii_product_name }}</span>
                        <span class="list_column col_serial_number">{{ $sii_serial_number }}</span>
                        <span class="list_column col_unit_price">${{ $sii_unit_price }}</span>
                        <span class="list_column col_qty">{{ $sii_qty }}</span>
                        <span class="list_column col_discount">-${{ $sii_discount }}</span>
                        <span class="list_column col_item_total">${{ $sii_total_price }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="list_total_row">
                    <div class="total_field_column total_items_field">
                        <div class="fL total_label">Total Items:</div>
                        <div class="fL">{{ $total_items }}</div>
                    </div>
                    <div class="total_field_column">
                        <div class="fL total_label">Total Qty:</div>
                        <div class="fL">{{ $total_qty }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom_block">
            <div class="remark_block">
                <strong class="fs16">Remarks:</strong><br />
                <div class="fL w100">{{ $si_remark }}</div>
            </div>
            <div class="total_amount_block">
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Total:</div>
                    <div class="total_amount_row_text">${{ $si_total_amount }}</div>
                </div>
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Deposit:</div>
                    <div class="total_amount_row_text">-${{ $si_deposit_payment_amount }}</div>
                </div>
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Discount:</div>
                    <div class="total_amount_row_text">
                      <?php
                        if($si_discount_type != '0'){
                          if($si_discount_type == '1'){
                            echo "-$" . $si_discount;
                          }
                          if($si_discount_type == '2'){
                            echo "-" . $si_discount . "%";
                          }
                        }else{
                          echo "---";
                        }
                      ?>
                    </div>
                </div>
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Net Total:</div>
                    <div class="total_amount_row_text">${{ $si_net_total_amount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="right_column">
        <div class="form_block">
            <div class="form_title">Sales Invoice Info</div>
            <div class="form_row">
                <div class="form_label">Status:</div>
                <div class="fL" id="status_text">{{ $si_status_text }}</div>
            </div>
            <div class="form_row">
                <div class="form_label">Order Date:</div>
                {{ $si_create_time }}
            </div>
            <div class="form_row">
                <div class="form_label">Staff:</div>
                {{ $si_sales_code }}
            </div>
            <div class="form_row">
                <div class="form_label">Quotation:</div>
                {{ (!empty($si_quotation_no)) ? $si_quotation_no : '---' }}
            </div>
            <div class="form_row">
                <div class="form_label">Deposit:</div>
                {{ (!empty($si_deposit_no)) ? $si_deposit_no : '---' }}
            </div>
            <div class="form_row">
                <div class="form_label">Request by:</div>
                {{ $si_cashier_code }}
            </div>

            <div class="form_title">Payment Method</div>
            <div class="form_row">
                <div class="form_label">Terms:</div>
                {{ $si_payment_term_name }}
            </div>
            @foreach ($payment_method_list as $payment)
            <?php extract($payment, EXTR_PREFIX_ALL, 'p'); ?>
            <div class="form_row">
                <div class="form_label">{{ $p_payment_name }}</div>
                ${{ $p_payment_amount }}
            </div>
            @endforeach

            <div class="form_title">Record Info</div>
            <div class="form_row">
                <div class="form_label">Create Date:</div>
                {{ $si_create_time }}
            </div>
            <div class="form_row">
                <div class="form_label">Last Update:</div>
                {{ $si_last_update_time }}
            </div>
            <div class="form_row">
                <div class="form_label">Updated By:</div>
                {{ $si_last_update_by }}
            </div>
        </div>
    </div>

    <div class="list_page_buttons_row">
        <div class="list_page_buttons_column">
            @if ($is_allow_print)
            <button data-redirect_page="sales_invoice/print/{{ $record_id }}" class="btn btn-default btn-sm redirect_button">Print</button>
			@endif
            <button data-redirect_page="sales_invoice/list" class="btn btn-default btn-sm list_page_buttons redirect_button">Return to List</button>
            @if ($is_allow_create)
            <button data-redirect_page="sales_invoice/order" class="btn btn-primary btn-sm redirect_button" style="margin-left: 20px;">Create New Sales Invoice</button>
			@endif
        </div>
    </div>

    <div class="panel panel-success result_alert_box" id="update_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Update Sales Invoice successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="update_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to update Sales Invoice. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <input type="hidden" id="record_id" value="{{ $record_id }}" />
</div>
@stop
