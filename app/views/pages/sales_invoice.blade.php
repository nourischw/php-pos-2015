<!DOCTYPE html>
<html>
<head></head>
<body>
@section('content')
@include('popups.stock_list')
@include('popups.deposit_list')
@include('popups.quotation_list')
@include('popups.pages.sales_invoice_confirm')
@include('popups.pages.sales_invoice_result')
<?php $is_update = ($record_id > 0) ? true : false ?>
<link href="{{ Config::get('path.CSS') }}sales_invoice.css" media="all" rel="stylesheet" type="text/css">
<div id="sales_invoice" class="page_content">
	<div class="id_row">
		<div class="from_status_info_row">
			<div class="btn-group form_row_toggle" data-toggle="buttons">
				<label class="form_label">Status:</label>
				<label class="btn btn-default btn-xs sales_status_button active" data-status="0"><input type="radio" value="0" name="search_status" checked />New</label>
				<label class="btn btn-default btn-xs sales_status_button" data-status="2"><input type="radio" value="2" name="search_status" />Pending</label>
				<label class="btn btn-default btn-xs sales_status_button" data-status="3"><input type="radio" value="3" name="search_status" />Voided</label>
				<label class="btn btn-default btn-xs sales_status_button" data-status="4"><input type="radio" value="4" name="search_status" />Finished</label>
			</div>
		</div>
	</div>

    <div class="sales_invoice_create">
		<div class="left_column">
			<div class="left_column_form_block">
				<div class="form_row input-group">
					<div class="form_row">
						<label class="form_label">Quick Search:</label>
						<div class="fL btn-group search_type" data-toggle="buttons">
							<label class="btn btn-default btn-xs search_status_button active" data-status="1"><input type="radio" value="1" name="search_status" checked />UPC</label>
							<label class="btn btn-default btn-xs search_status_button" data-status="2"><input type="radio" value="2" name="search_status" />Serial Number</label>
							<input type="hidden" id="quick_search_type" value="1">
						</div>
						<div>
							<input type="text" id="quick_search" class="quick_search product_text_field form-control">
							<span class="input-group-btn text_field_buttons">
								<button class="quick_search btn btn btn-default text_field_button show_popup_list_button" id="show_stock_popup_list_button" data-list_id="stock_popup_list" disabled="">...</button>
								<button class="quick_search btn btn-default text_field_button posA get_search_val" id="get_product_info_button"><span class="glyphicon glyphicon-search"></span></button>
							</span>
						</div>
					</div>
				</div>
				<span class="error_product crRed"></span>
				<input type="hidden" id="from_shop_id" {{ ($is_update) ? 'value="' . $st_from_shop_id . '"' : null }} />
				<div class="fL w100 form_title pre_add_title">
					<span class="fL left_margin">Product UPC</span>
					<span class="fL left_margin">Serial Number</span>
					<span class="fL left_margin_spec" id="have_qty">Qty(?)</span>
					<span class="fL left_margin_spec">Discount(實數)</span>
					<span class="fL left_margin">Unit Price</span>
					<span class="fL left_margin">Total Price</span>
				</div>
				<div class="form_row input-group">
					<span id="product_code" class="fL left_margin">---</span>
					<span id="serial_number" class="fL left_margin">---</span>
					<span class="fL">
						<input type="number" id="product_qty" class="product_text_field_spec form-control left_margin">
						<input type="hidden" id="check_total_qty">
					</span>
					<span class="fL">
						<input type="number" id="product_discount" class="product_text_field_spec form-control left_margin">
					</span>
					<span id="product_unit_price" class="fL left_margin">---</span>
					<span id="product_total_price" class="fL left_margin">---</span>
					<span class="fL">
						<input type="button" class="btn btn-default btn-sm product_block_buttons" id="product_btn_add" value="Add">
					</span>
					<input type="hidden" id="stock_id">
					<input type="hidden" id="product_id">
					<input type="hidden" id="product_name">
					<input type="hidden" id="product_discount_price">
				</div>
			</div>
			

			<div class="list_container">
				<div class="fL item_color">
					<div class="fL item_type item_type_d">&nbsp;</div>
					<div class="fL">Deposit Item</div>
				</div>
				<div class="fL item_color">
					<div class="fL item_type item_type_q">&nbsp;</div>
					<div class="fL">Quotation Item</div>
				</div>
				<div class="fL item_color">
					<div class="fL item_type item_type_o">&nbsp;</div>
					<div class="fL">Out of stock Item</div>
				</div>
				<div id="item_list " class="item_list item_lists">
					<div class="list_header">
						<span class="list_column col_no_row">#</span>
						<span class="list_column col_product_upc">UPC</span>
						<span class="list_column col_product_name">Product Name</span>
						<span class="list_column col_serial_number">Serial Number</span>
						<span class="list_column col_product_unit_price">U.Price</span>
						<span class="list_column col_product_qty">Qty</span>
						<span class="list_column col_product_discount">Dis(-)</span>
						<span class="list_column col_product_total">Total</span>
					</div>
					<div class="list_items" id="list_items"></div>
				</div>
				<div class="fL crRed bold fs13 w100" id="no_list_item_error_message">At least 1 item must be added to the list</div>
			</div>

			<div class="bottom_block">
				<div class="remark_block">
					<textarea id="remarks" class="form-control remark_textarea" placeholder="Remarks"></textarea>
				</div>
				<div class="total_amount_block">
					<div class="total_amount_row">
						<div class="total_amount_row_label">Total:</div>
						<div class="total_amount_row_text" id="total_amount">$<span id="total_amount_value">0.00</span></div>
					</div>
					<div class="dIn total_deposit_row">
						<div class="total_deposit_row_label">Deposit:</div>
						<div class="total_deposit_row_text" id="total_deposit">-$<span id="total_deposit_value">0.00</span></div>
						<input type="hidden" id="cal_deposit_id">
						<input type="hidden" id="cal_quotation_id">
						<input type="hidden" id="cal_deposit_payment_amount">
					</div>
					<div class="total_amount_row">
						<div class="total_amount_row_label">Discount:</div>
						<div class="total_amount_row_text" id="total_discount"><span id="total_discount_value">---</span></div>
						<input type="hidden" id="cal_discount_type">
					</div>
					<div class="total_amount_row">
						<div class="total_amount_row_label">Net Total:</div>
						<div class="total_amount_row_text" id="net_total_amount">$<span id="net_total_amount_value">0.00</span></div>
					</div>
				</div>
			</div>
		</div>

		<div class="right_column">
			<div class="form_block">
				<div class="">
					<div class="form_title">From Shop Info</div>
					<div class="form_row input-group">
						<label class="form_label"><sup class="crRed">*</sup>From Shop:</label>
						<div id="select_from_shop_block" {{ ($is_update) ? 'style="display: none"' : null }}>
							<select class="text_field_shop form-control" id="from_shop">
								@foreach ($shop_list as $shop)
									<?php extract($shop); ?>
								<option value="{{ $id }}" {{ ($code == Session::get('shop_code')) ? 'selected' : null }}>{{ $code }}</option>
								@endforeach
							</select>
							<button id="select_from_shop_button" class="btn btn-default btn-xs" style="margin-left: 10px; height: 25px;">Select</button>
						</div>
						<div id="from_shop_info" style="display:none;">
							<div class="fL" id="from_shop_name_text">{{ ($is_update) ? $st_from_shop_code : '---' }}</div>
							<button id="change_from_shop_button" class="btn btn-default btn-xs">Change</button>
						</div>
					</div>
				</div>
				<div class="">
					<div id="payment_method" class="form_row input-group">
						<div class="fL w100 payment_method_block">
							<div class="form_title">Payment Method</div>
							<div class="form_row">
								<label class="form_label">Payment Term:</label>
								<select class="text_field form-control" id="payment_term">
									@foreach ($payment_term_list as $index => $type)
									<option value="{{ $index }}" {{ ($index == '1') ? 'selected' : null; }}>{{ $type }}</option>
									@endforeach
								</select>
								<div class="form-error_message" id="error_payment_term"></div>
							</div>
							<div class="form_row">
								<label class="form_label">Add Type:</label>
								<select class="text_field form-control payment_type" id="payment_type">
									@foreach ($payment_type_list as $index => $type)
									<option value="{{ $index }}" {{ ($index == '6') ? 'selected' : null; }}>{{ $type }}</option>
									@endforeach
								</select>
								<span class="input-group-btn text_field_buttons">
										<button class="btn btn-default text_field_button" id="add_payment_type"><span class="glyphicon glyphicon-plus"></span></button>
								</span>
							</div>
						</div>
						<div class="fL w100 payment_list_block">
							<div class="form_title">Payment List</div>
							<div class="form_row">
								<div class="fL payment_block">
									<div class="payment_block_topic">
										<label class="fL payment_type_type_field">Type</label>
										<label class="fL payment_type_amt_field">Amount</label>
										<label class="fL payment_type_no_field"></label>
									</div>
								</div>
								<div id="payment_list" class="fL payment_list"></div>
								<div class="fL w100">
									<div class="payment_require"></div>
									<label class="fL payment_combine_type_field">Remain Amount:</label>
									<label class="fL payment_type_amt_field">
										<span class="crRed payment_cal_last_total">$0.00</span>
									</label>
								</div>										
								<div class="fL w100">
									<div class="payment_combine"></div>
									<label class="fL payment_combine_type_field">Combine Total:</label>
									<label class="fL payment_type_amt_field">
										<span class="payment_cal_total">$0.00</span>
									</label>
								</div>								
								<input type="hidden" id="payment_list_total" value="0.00">
								<div class="fL crRed bold fs13 w100" id="no_payment_list_over_error_message" class="no_payment_list_over_error_message" style="display: none;">Payment Amount over than Net Total</div>
								<div class="fL crRed bold fs13 w100" id="no_payment_list_item_error_message" class="no_payment_list_item_error_message" style="display: none;">At least 1 Payment Method</div>
								<div class="fL crRed bold fs13 w100" id="no_payment_list_require_error_message" class="no_payment_list_require_error_message" style="display: none;">Payment Amount require</div>
							</div>
						</div>
							<!--div class="form_row">
								<label class="form_label">Tender Amount:</label>
								<input type="text" id="tender_amount" class="product_text_field form-control">
							</div-->
					</div>
				</div>
				<div class="">
					<div class="form_title">Discount</div>
					<div class="form_row">
						<div class="fL w100">
							<span class="fL discount_type">
								<input type="radio" class="total_discount_type" name="discount_type" value="1">實數
								<input type="radio" class="total_discount_type" name="discount_type" value="2">-%
							</span>
							<input type="text" id="total_discount_amount" class="discount_amount_text_field form-control" name="discount_amount">
							<span class="input-group-btn text_field_buttons">
								<button class="btn btn-default text_field_button" id="total_discount_button"><span class="glyphicon glyphicon-plus"></span></button>
								<button class="btn btn-default text_field_button" id="reset_discount_button"><span class="glyphicon glyphicon-remove"></span></button>
								<input type="hidden" id="total_discount_status">
							</span>
						</div>
					</div>
				</div>
				<div class="">
					<div class="form_title">Quotation</div> 
					<div class="form_row input-group">
						<label class="fL form_label_deposit">Load:</label>
						<div class="fL text_group_button">
							<input type="text" id="quotation_code" class="fL quotation_text_field form-control" name="quotation_code">
							<span class="input-group-btn text_field_buttons">
								<button class="btn btn-default text_field_button quotation_pop_btn show_popup_list_button" id="show_quotation_popup_list_button" data-list_id="quotation_popup_list">...</button>
								<button class="btn btn-default text_field_button" id="reset_quotation_button"><span class="glyphicon glyphicon-remove"></span></button>
							</span>
						</div>
					</div>
				</div>
				<div class="">
					<div class="form_title">Deposit</div>
					<div class="form_row input-group">
						<label class="fL form_label_deposit">Load:</label>
						<div class="fL text_group_button">
							<input type="text" id="deposit_code" class="fL deposit_text_field form-control" name="deposit_code">
							<span class="input-group-btn text_field_buttons">
								<button class="btn btn-default text_field_button deposit_pop_btn show_popup_list_button"  id="show_deposit_popup_list_button" data-list_id="deposit_popup_list">...</button>
								<button class="btn btn-default text_field_button" id="reset_deposit_button"><span class="glyphicon glyphicon-remove"></span></button>
							</span>
						</div>
					</div>
				</div>				
			</div>
		</div>
		<div class="list_page_buttons_row">
			@if ($is_allow_create)
			<button id="temp_save_btn" class="btn btn-default btn-sm list_page_buttons">Temp Save</button>
			<button id="confirm_btn" class="btn btn-default btn-sm list_page_buttons">[F8] Confirm</button>
			@endif
			<input type="hidden" id="status_type">
		</div>
    </div>
</div>
<script src="{{ Config::get('path.ROOT') }}app/js/libs/donetyping.js"></script>
<script src="{{ Config::get('path.ROOT') }}app/js/libs/shortcut.js"></script>
@stop
</body>
</html>
