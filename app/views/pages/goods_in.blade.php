<!DOCTYPE html>
<html>
<head>

</head>

<body>
@section('content')
@include('popups.product_list')
@include('popups.purchase_order_list')
@include('popups.supplier_list')    
<link href="{{ Config::get('path.CSS') }}goods_in.css" media="all" rel="stylesheet" type="text/css">
<div id="goods_in" class="page_content">
    <div class="left_column">
		<div class="fs18">Create Goods In Item:</div>
        <div class="left_column_form_block">
			<div class="fL w100">
				<div class="product_upc_field input-group">
					<label class="fL new_item_label" for="product_upc">Product UPC:</label>
					<div class="fL">
						<input type="text" id="product_upc" class="form-control new_item_text_field validateItem product_upc_text_field">
						<span class="input-group-btn text_field_buttons">
							<button id="show_product_popup_list" class="btn btn-default text_field_button show_popup_list_button" data-list_id="product_popup_list">...</button>
							<button class="btn btn-default text_field_button posA get_product_info_button"><span class="glyphicon glyphicon-search"></span></button>
						</span>
					</div>
					<div class="form-error_message" id="error_product_upc"></div>
				</div>
				<div class="qty_field">
					<label class="new_item_label" for="product_qty">Qty:</label>
					<input type="number" class="new_item_text_field_g form-control qty_text_field num_item" data-num_flags="+i" id="product_qty" maxlength="5" />
					<div class="form-error_message" id="error_product_qty"></div>
				</div>
				<div class="unit_price_field">
					<label class="new_item_label" for="product_unit_price">Unit Price:</label>
					<input type="text" class="new_item_text_field_g form-control unit_price_text_field validateItem num_item" data-num_flags="+.2" id="product_unit_price" maxlength="10" />
					<div class="form-error_message" id="error_product_unit_price"></div>
				</div>
				<button id="add_item_button" class="add_item_button btn btn-default">Add</button>
			</div>
		
			<div class="fL w100">
				<label class="new_item_label">Product Name:</label>
				<span class="product_name_text" id="product_name_text">---</span>
				<label class="new_item_label_imei">Require Imei:</label>
				<span class="product_require_imei" id="product_require_imei">---</span>
				<input type="hidden" class="get_product_id">
			</div>
		</div>
		
		<div class="list_container">
			<div id="item_list" class="item_list">
				<div class="list_header">
					<span class="list_column col_no_row">#</span>
					<span class="list_column col_product_upc">UPC</span>
					<span class="list_column col_product_name">Product Name</span>
					<span class="list_column col_qty">Qty</span>
					<span class="list_column col_unit_price">Unit Price</span>
					<span class="list_column col_item_total">Total</span>
					<span class="list_column col_require_imei">Imei</span>
				</div>
				<div class="list_no_items_row" id="list_no_items_row">(Please Add / Load PO)</div>
				<div class="list_items" id="list_items"></div>
			</div>
			<div class="fL crRed bold fs13 w100" id="no_list_item_error_message">At least 1 item must be added to the list</div>
		</div>

		<div class="bottom_block">
			<div class="remark_block">
				<div class="fL w100"><textarea id="remarks" class="form-control remark_textarea" placeholder="Remarks"></textarea></div>
			</div>
			<div class="total_amount_block">
				<div class="total_amount_row">
					<div class="total_amount_row_label">Qty:</div>
					<div class="total_amount_row_text" id="total_qty"><span id="total_qty_value">0</span></div>
				</div>
				<div class="total_amount_row">
					<div class="total_amount_row_label">Total:</div>
					<div class="total_amount_row_text" id="total_amount">$<span id="total_amount_value">0.00</span></div>
					<div class="fL w100 crRed fs13 bold net_total_error_message" id="net_total_error_message">Net total can't be negative</div>
				</div>
			</div>
		</div>
	</div>
    <div class="right_column">
		<div class="form_block">
			<div class="form_title">Load Purchase Order</div>
			<div class="form_row input-group">
				<label class="form_label form_label_title">PO Refer:</label>
				<div class="fL">
					<input type="text" id="po_code" class="po_text_field form-control validateItem" name="gi_po_numbuer">
					<div class="input-group-btn text_field_buttons group_btn">
						<button id="show_purchase_order_popup_list" class="btn btn-default text_field_button show_popup_list_button" data-list_id="purchase_order_popup_list">...</button>
						<button class="btn btn-default text_field_button get_po_info_button" id="get_po_info_button"><span class="glyphicon glyphicon-search"></span></button>
						<button class="btn btn-default text_field_button get_po_remove_button" id="get_po_remove_button"><span class="glyphicon glyphicon-remove"></span></button>						
					</div>
				</div>
			</div>		
			<!--form id="form_purchaser_order" -->
				<div id="create_new_po" style="display: none;">
					<div class="form_title">Purchase Order Info</div>
					<div class="form_row">
						<label for="order_date" class="form_label"><sup class="crRed">*</sup>Order Date:</label>
						<div class="input-group">
							<div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
							<input type="text" class="text_field form-control validateItem" id="order_date" name="order_date" />
						</div>
						<div class="form-error_message" id="error_order_date"></div>
					</div>
					<div class="form_row">
						<label for="staff_code" class="form_label">Staff: </label>
						<select class="text_field form-control" id="staff_code">
							@foreach ($staff_list as $index => $staff)
								<?php extract($staff); ?>
							<option value="{{ $staff_code }}" {{ ($staff_code === $staff_code) ? 'selected' : null }}>{{ $staff_code }}</option>
							@endforeach
						</select>
						<div class="form-error_message" id="error_staff_id"></div>
					</div>
					<div class="form_row">
						<label for="deposit_no" class="form_label">Deposit No:</label>
						<input type="text" class="text_field form-control" id="deposit_no" name="deposit_no" />
					</div>
					<div class="form_row">
						<label for="ship_to" class="form_label"><sup class="crRed">*</sup>Ship To:</label>
						<select class="text_field form-control" id="ship_to">
							@foreach ($shop_list as $shop)
								<?php extract($shop); ?>
							<option value="{{ $id }}" {{ (Session::get('shop_code') === $code) ? 'selected' : null }}>{{ $code }}</option>
							@endforeach
						</select>
						<div class="form-error_message" id="error_ship_to"></div>
					</div>
					<div class="form_row">
						<label for="request_by" class="form_label"><sup class="crRed">*</sup>Request by:</label>
						<select class="text_field form-control" id="request_by">
							@foreach ($staff_list as $staff)
								<?php extract($staff); ?>
							<option value="{{ $staff_code }}" {{ (Session::get('staff_id') === $staff_code) ? 'selected' : null }}>{{ $staff_code }}</option>
							@endforeach
						</select>
						<div class="form-error_message" id="error_request_by"></div>
					</div>
					<div class="form_row">
                    <label for="payment_type" class="form_label">Payment:</label>
                    <select class="text_field form-control" id="payment_type">
                        @foreach ($payment_type as $index => $payment)
                        <option value="{{ $index }}" {{ ($index === $po_payment_type) ? 'selected' : null; }}>{{ $index }} - {{ $payment }}</option>
                        @endforeach
                    </select>
					
						<div class="form-error_message" id="error_payment_type"></div>
					</div>		
				</div>		
			<!--/form-->
				
			<div class="form_title">Goods in Info</div>
			<form id="form_goods_in">
				<div class="form_row">
					<label for="goods_in_to" class="form_label"><sup class="crRed">*</sup>Goods in to shop:</label>
					<select class="text_field form-control validateItem" id="goods_in_to" name="gi_goods_in_to">
						<option value="" selected="selected" class="select_list_first_option">Please select</option>
						@foreach ($shop_list as $shop)
							<?php extract($shop); ?>
						<option value="{{ $id }}">{{ $code }}</option>
						@endforeach
					</select>
					<div class="form-error_message" id="error_goods_in_to"></div>
				</div>			
				<div class="form_row">
					<label for="consignment" class="form_label">Consignment: </label>
					<select class="text_field form-control validateItem" id="consignment">
						<option selected="selected" value="N">N</option>
						<option value="Y">Y</option>
					</select>
				</div>
				<div class="form_row">
					<label for="invoice_date" class="form_label">Invoice Date:</label>
					<div class="input-group">
						<div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
						<input type="text" class="text_field form-control" id="invoice_date" name="gi_invoice_date" />
					</div>
					<div class="form-error_message" id="error_invoice_date"></div>
				</div>	
				<div class="form_row">
					<label for="invoice_no" class="form_label">Invoice No:</label>
					<input type="text" class="text_field form-control" id="invoice_no" name="gi_invoice_no" />
					<div class="form-error_message" id="error_invoice_no"></div>
				</div>
			</form>
			<div class="form_title">Supplier</div>
			<div class="form_row input-group">
				<label class="form_label form_label_title"><sup class="crRed">*</sup>Code:</label>
				<div class="fL">
					<input type="text" id="supplier_code" class="supplier_text_field form-control validateItem" name="gi_supplier_code">
					<div class="input-group-btn text_field_buttons group_btn">
						<button id="show_supplier_popup_list" class="btn btn-default text_field_button show_popup_list_button" data-list_id="supplier_popup_list">...</button>
						<button class="btn btn-default text_field_button get_supplier_info_button" id="get_supplier_info_button"><span class="glyphicon glyphicon-search"></span></button>
						<button class="btn btn-default text_field_button get_supplier_remove_button" id="get_supplier_remove_button"><span class="glyphicon glyphicon-remove"></span></button>						
					</div>					
				</div>
				<div class="form-error_message" id="error_supplier_code"></div>
			</div>
			<div class="form_row">
				<label class="form_label">Shop Name:</label>
				<span id="supplier_name">---</span>
			</div>
			<div class="form_row">
				<label class="form_label">Mobile:</label>
				<span id="supplier_mobile">---</span>
			</div>
			<div class="form_row">
				<label class="form_label">Fax:</label>
				<span id="supplier_fax">---</span>
			</div>
			<div class="form_row">
				<label class="form_label">Email:</label>
				<span id="supplier_email">---</span>
			</div>
		</div>
		<input type="hidden" id="get_supplier_id" name="get_supplier_id" value="" />
    </div>	
    <div class="submit_button_row">
        <button id="confirm_goods_in_button" class="btn btn-default btn-sm">Confirm</button>
		<button id="reset_all_button" class="btn btn-default btn-sm">Reset All</button>
		<button data-redirect_page="goods_in/list" class="btn btn-default btn-sm list_page_buttons redirect_button">Return to List</button>		
    </div>	
<script src="{{ Config::get('path.ROOT') }}app/js/libs/shortcut.js"></script>	
</div>
@stop
</body>
</html>