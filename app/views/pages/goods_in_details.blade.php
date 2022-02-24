@section('content')
<?php extract($goods_in_data, EXTR_PREFIX_ALL, 'gi'); ?>
<div id="goods_in_edit" class="page_content">
    <div class="id_row">
        <div class="id_label">Goods In Number:</div>
        {{ $gi_goods_in_number }}
    </div>
    
    <div class="left_column">
        <div class="list_container" id="list_goods_in_edit">
            <div id="item_list" class="item_list">
                <div class="list_header">
                    <span class="list_column col_product_upc">UPC</span>
                    <span class="list_column col_product_name">Product Name</span>
                    <span class="list_column col_serial_number">Serial Number</span>
                    <span class="list_column col_qty">Qty</span>
                    <span class="list_column col_actual_price">Actual Price</span>
					<span class="list_column col_item_total">Total Price</span>
                </div>
                <div class="list_items" id="list_items">
                    @foreach ($goods_in_items_data as $value)
                        <?php extract($value, EXTR_PREFIX_ALL, 'gii'); ?>
                    <div class="list_item_row">
                        <span class="list_column col_product_upc">{{ $gii_barcode }}</span>
                        <span class="list_column col_product_name">{{ $gii_name }}</span>
                        <span class="list_column col_serial_number">{{ $gii_serial_number }}</span>
                        <span class="list_column col_qty">{{ $gii_qty }}</span>
                        <span class="list_column col_actual_price">{{ $gii_actual_price }}</span>
                        <span class="list_column col_item_total list_item_total">{{ $gii_total_price }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="list_total_row">
                    <div class="total_field_column total_items_field">
                        <div class="fL total_label">Total Items:</div>
                        <div class="fL">{{ $gi_total_items }}</div>
                    </div>
                    <div class="total_field_column">
                        <div class="fL total_label">Total Qty:</div>
                        <div class="fL">{{ $gi_total_qty }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bottom_block">
            <div class="remark_block">
                <strong class="fs16">Remarks:</strong><br />
                <div class="fL w100">{{ $gi_po_remark }}</div>
            </div>
            <div class="total_amount_block">
                <div class="total_amount_row">
                    <div class="total_amount_row_label">Total:</div>
                    <div class="total_amount_row_text">${{ $gi_total_price }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="right_column">
        <div class="form_block">
            <div class="form_title">Goods In Info</div>
            <div class="form_row">
                <div class="form_label">Status:</div>
                <div class="fL" id="status_text">{{ $gi_status_text }}</div>
            </div>
            <div class="form_row">
                <div class="form_label">Goods In To:</div>
                {{ $gi_goods_in_to }}
            </div>
            <div class="form_row">
                <div class="form_label">Request by:</div>
                {{ $gi_update_by }}
            </div>

            <div class="form_title">Supplier</div>
            <div class="form_row">
                <div class="form_label">Code:</div>
                {{ $gi_supplier_code }}
            </div>
            <div class="form_row">
                <div class="form_label">Shop Name:</div>
                <div class="supplier_shop_name">{{ $gi_supplier_name }}</div>
            </div>
            <div class="form_row">
                <div class="form_label">Mobile:</div>
                {{ (empty($gi_supplier_mobile)) ? '---' : $gi_supplier_mobile }}
            </div>
            <div class="form_row">
                <div class="form_label">Fax:</div>
                {{ (empty($gi_supplier_fax)) ? '---' : $gi_supplier_fax }}
            </div>
            <div class="form_row">
                <div class="form_label">Email:</div>
                {{ (empty($gi_supplier_email)) ? '---' : $gi_supplier_email }}
            </div>

            <div class="form_title">Record Info</div>
            <div class="form_row">
                <div class="form_label">Create Date:</div>
                {{ $gi_create_time }}
            </div>
            <div class="form_row">
                <div class="form_label">Last Update:</div>
                {{ $gi_update_time }}
            </div>
            <div class="form_row">
                <div class="form_label">Updated By:</div>
                 {{ $gi_update_by }}
            </div>
        </div>
    </div>

    <div class="list_page_buttons_row">
        <div class="list_page_buttons_column">
            @if ($is_allow_print)
				<button id="print_button" class="btn btn-default btn-sm list_page_buttons">Print</button>
			@endif
            <button data-redirect_page="goods_in/list" class="btn btn-default btn-sm list_page_buttons redirect_button">Return to List</button>
        </div>
    </div>

    <input type="hidden" id="record_id" value="{{ $record_id }}" />
</div>
@stop