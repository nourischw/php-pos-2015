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
<?php extract($sales_invoice_edit_list); ?>
<?php extract($sales_invoice_list, EXTR_PREFIX_ALL, "si"); ?>
<?php extract($sales_invoice_deposit_list, EXTR_PREFIX_ALL, "dp"); ?>
<?php extract($sales_invoice_quotation_list, EXTR_PREFIX_ALL, "qo"); ?>
<link href="{{ Config::get('path.CSS') }}sales_invoice_edit.css" media="all" rel="stylesheet" type="text/css">
<div id="sales_invoice_edit" class="page_content">
	<input type="hidden" id="sales_invoice_edit_id" value="{{ $record_id }}">
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
				<div id="item_list" class="item_list">
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
					<div class="list_items" id="list_items">
					<input type="hidden" class="remove_item_ids" value="">
					@foreach($sales_invoice_item_list as $key => $sales_invoice_item_lists)
					<?php 
						extract($sales_invoice_item_lists, EXTR_PREFIX_ALL, "sii"); 
						$row_text = "-";
						$deposit_class = null;
						$quotation_class = null;
						$out_of_stock_class = null;
						if($sii_deposit_id != 0 ){
							$deposit_class = "deposit_item";
							$row_text = "D";
						}else if($sii_quotation_id != 0 ){
							$row_text = "Q";
							$quotation_class = "quotation_item";
						}else if($sii_total_qty == 0){
							$row_text = "O";
							$out_of_stock_class = "out_of_stock_item";							
						}
					?>
						<div class="list_item_row {{ $deposit_class }} {{ $quotation_class }} {{ $out_of_stock_class }}" data-item_id="{{ $sii_id }}" data-stock-id="{{ $sii_stock_id }}" data-deposit-id="{{ $sii_deposit_id }}" data-product-id="{{ $sii_product_id }}">
							<input type="hidden" id="check_shop_item" class="check_shop_item" value="{{ $sii_shop_code }}">
							<span class="list_column col_no_row">{{ $row_text }}</span>
							<span class="list_column col_product_upc get_product_upc">{{ $sii_product_upc }}</span>
							<span class="list_column col_product_name get_product_name">{{ $sii_product_name }}</span>
							<span class="list_column col_serial_number get_serial_number">{{ $sii_serial_number }}</span>
							<span class="list_column col_product_unit_price get_product_unit_price">${{ $sii_unit_price }}</span>
							<input type="number" class="list_column col_product_qty get_product_qty" value="{{ $sii_qty }}" {{ ($sii_deposit_id || $sii_quotation_id) != '0' ? 'disabled' : null }}>
							<input type="number" class="list_column col_product_discount get_product_discount" value="{{ $sii_discount }}" {{ ($sii_deposit_id || $sii_quotation_id) != '0' ? 'disabled' : null }}>
							<span class="list_column col_product_total count_product_total">${{ $sii_total_price }}</span>
							<?php echo ($sii_deposit_id || $sii_quotation_id) == "0" ? '<span class="list_column col_remove_button"><span class="list_delete_single_item_button glyphicon glyphicon-remove"></span></span>' : null; ?>
							<input type="hidden" class="product_item" value="{{ $sii_id }}">
							<input type="hidden" class="product_total_qty" value="{{ $sii_total_qty }}">
							<?php echo ($sii_deposit_id || $sii_quotation_id) != "0" ? "<input type='hidden' class='deposit_item_price' value='$sii_total_price'>" : null; ?>
						</div>
					@endforeach
					</div>
				</div>
				<div class="fL crRed bold fs13 w100" id="no_list_item_error_message">At least 1 item must be added to the list</div>
			</div>

			<div class="bottom_block">
				<div class="remark_block">
					<textarea id="remarks" class="form-control remark_textarea" placeholder="Remarks">{{ $si_remark }}</textarea>
				</div>
				<div class="total_amount_block">
					<div class="total_amount_row">
						<div class="total_amount_row_label">Total:</div>
						<div class="total_amount_row_text" id="total_amount">$<span id="total_amount_value">{{ $si_total_amount }}</span></div>
					</div>
					<div class="{{ (empty($si_deposit_id)) ? 'dIn' : null}} total_deposit_row">
						<div class="total_deposit_row_label">Deposit:</div>
						<div class="total_deposit_row_text" id="total_deposit">-$<span id="total_deposit_value">{{ $si_deposit_payment_amount }}</span></div>
						<input type="hidden" id="old_cal_deposit_id" value="{{ $si_deposit_id }}">
						<input type="hidden" id="old_cal_quotation_id" value="{{ $si_quotation_id }}">
						<input type="hidden" id="cal_deposit_id" value="{{ $si_deposit_id }}">
						<input type="hidden" id="cal_quotation_id" value="{{ $si_quotation_id }}">
						<input type="hidden" id="cal_deposit_payment_amount" value="{{ $si_deposit_payment_amount }}">
					</div>
					<div class="total_amount_row">
						<div class="total_amount_row_label">Discount:</div>
						<div class="total_amount_row_text" id="total_discount">
							<span id="total_discount_value">{{ ($si_discount_type == '1') ? '-$' : '-' }}{{ $si_discount }}{{ ($si_discount_type == '2') ? '%' : '' }}</span>
						</div>
						<input type="hidden" id="cal_discount_type">
					</div>
					<div class="total_amount_row">
						<div class="total_amount_row_label">Net Total:</div>
						<div class="total_amount_row_text" id="net_total_amount">$<span id="net_total_amount_value">{{ $si_net_total_amount }}</span></div>
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
							<div class="fL" id="from_shop_name_text"></div>
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
									<option value="{{ $index }}" {{ ($index == $si_term) ? 'selected' : null; }}>{{ $type }}</option>
									@endforeach
								</select>
								<div class="form-error_message" id="error_payment_term"></div>
							</div>
							<div class="form_row">
								<label class="form_label">Add Type:</label>
								<select class="text_field form-control payment_type" id="payment_type">
									@foreach ($payment_type_list as $index => $type)
										<option value="{{ $index }}">{{ $type }}</option>
									@endforeach
								</select>
								<span class="input-group-btn text_field_buttons">
										<button class="btn btn-default text_field_button" id="add_payment_type"><span class="glyphicon glyphicon-plus"></span></button>
								</span>
							</div>
						</div>
						<div class="payment_list_block">
							<div class="form_title">Payment List</div>
							<div class="form_row">
								<div class="fL w100 payment_block">
									<div class="payment_block_topic">
										<label class="fL payment_type_type_field">Type</label>
										<label class="fL payment_type_amt_field">Amount</label>
										<label class="fL payment_type_no_field"></label>
									</div>
								</div>
								<div id="payment_list" class="fL w100 payment_list">
									<input type="hidden" class="remove_payment_ids" value="">
									@foreach ($sales_invoice_payment_list as $sales_invoice_payment_lists)
									<?php extract($sales_invoice_payment_lists, EXTR_PREFIX_ALL, "pm"); ?>
									<div id="payment_list_row" class="fL payment_list_row" data-payment-id="{{ $pm_id }}" data-payment-type-id="{{ $pm_payment_type }}">
										<label class="fL payment_type_type_field">{{ $pm_payment_type_name }}</label>
										<label class="fL payment_type_amt_field">
											<input type="text" class="product_text_field_spec form-control payment_type_amount payment_type_amount_text_field" style="display: none;" data-payment-num="{{ $pm_id }}" value="{{ $pm_amount }}">
											<span class="payment_type_amount payment_type_amount_text" data-payment-num="{{ $pm_id }}">{{ $pm_amount }}</span>
										</label>
										
										<label class="fL payment_type_no_field">
											<span class="payment_list_action_button payment_list_ok_single_item_button glyphicon glyphicon-ok" style="display: none;"></span>
											<span class="payment_list_action_button payment_list_edit_single_item_button glyphicon glyphicon-pencil"></span>
										</label>
										<label class="fL payment_type_no_field"><span class="payment_list_action_button payment_list_delete_single_item_button glyphicon glyphicon-remove"></span></label>
										<input type="hidden" id="payment_item" class="payment_item" value="{{ $pm_id }}">
										<input type="hidden" id="payment_type_type_id" class="payment_type_type_id" value="{{ $pm_payment_type }}">
									</div>
									@endforeach						
								</div>
							</div>						
							<div class="fL w100">
								<div class="payment_require"></div>
								<label class="fL payment_combine_type_field">Remain Amount:</label>
								<label class="fL payment_type_amt_field">
									<span class="crRed payment_cal_last_total">$0</span>
								</label>
							</div>			
							<div class="fL w100">
								<div class="payment_combine"></div>
								<label class="fL payment_combine_type_field">Combine Total:</label>
								<label class="fL payment_type_amt_field">
									<span class="crR payment_cal_total">$0</span>
								</label>
							</div>										
							<input type="hidden" id="payment_list_total" value="0.00">
							<div class="fL crRed bold fs13 w100" id="no_payment_list_over_error_message" class="no_payment_list_over_error_message" style="display: none;">Payment Amount over than Net Total</div>
							<div class="fL crRed bold fs13 w100" id="no_payment_list_item_error_message" class="no_payment_list_item_error_message" style="display: none;">At least 1 Payment Method</div>
							<div class="fL crRed bold fs13 w100" id="no_payment_list_require_error_message" class="no_payment_list_require_error_message" style="display: none;">Payment Amount require</div>
							<!--div class="form_row">
								<label class="form_label">Tender Amount:</label>
								<input type="text" id="tender_amount" class="product_text_field form-control">
							</div-->
						</div>
					</div>
				</div>
				<div class="">
					<div class="form_title">Discount</div>
					<div class="form_row">
						<div class="fL w100">
							<span class="fL discount_type">
								<input type="radio" class="total_discount_type" name="discount_type" value="1" {{ ($si_discount_type == '1') ? 'checked' : null; }}>實數
								<input type="radio" class="total_discount_type" name="discount_type" value="2" {{ ($si_discount_type == '2') ? 'checked' : null; }}>-%
							</span>
							<input type="text" id="total_discount_amount" class="discount_amount_text_field form-control" name="discount_amount" value="{{ $si_discount }}">
							<span class="input-group-btn text_field_buttons">
								<button class="btn btn-default text_field_button" id="total_discount_button"><span class="glyphicon glyphicon-plus"></span></button>
								<button class="btn btn-default text_field_button posA" id="reset_discount_button"><span class="glyphicon glyphicon-remove"></span></button>
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
							<input type="text" id="quotation_code" class="fL quotation_text_field form-control" name="quotation_code" value="{{ ($qo_quotation_number != "") ? $qo_quotation_number : null  }}">
							<span class="input-group-btn text_field_buttons">
								<button id="show_quotation_popup_list_button" class="btn btn-default text_field_button quotation_pop_btn show_popup_list_button {{ (!empty($si_quotation_id)) ? 'dIn' : null; }}" data-list_id="quotation_popup_list">...</button>
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
							<input type="text" id="deposit_code" class="deposit_text_field form-control" name="deposit_code" value="{{ ($dp_deposit_number != "") ? $dp_deposit_number : null  }}">
							<span class="input-group-btn text_field_buttons">
								<button id="show_deposit_popup_list_button" class="btn btn-default text_field_button deposit_pop_btn show_popup_list_button {{ (!empty($si_deposit_id)) ? 'dIn' : null }}" data-list_id="deposit_popup_list">...</button>
								<button class="btn btn-default text_field_button" id="reset_deposit_button"><span class="glyphicon glyphicon-remove"></span></button>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="list_page_buttons_row">
	        @if ($record_id === 0 && $is_allow_create || $record_id > 0 && $is_allow_update)
	        <button id="temp_save_btn" class="btn btn-default btn-sm list_page_buttons">Temp Save</button>
	        @endif
			<!--button id="preview_btn" class="btn btn-default btn-sm list_page_buttons">Preview</button-->
			@if ($is_allow_confirm)
			<button id="confirm_btn" class="btn btn-default btn-sm list_page_buttons">[F8] Confirm</button>
			@endif
			@if ($is_allow_void)
			<button id="void_btn" class="btn btn-default btn-sm list_page_buttons">Void</button>
			@endif
			<button class="btn btn-default btn-sm list_page_buttons redirect_button" data-redirect_page="sales_invoice/list">Return to List</button>
			<input type="hidden" id="status_type">
		</div>
    </div>
</div>
<script src="{{ Config::get('path.ROOT') }}app/js/libs/donetyping.js"></script>
<script src="{{ Config::get('path.ROOT') }}app/js/libs/shortcut.js"></script>
@stop
</body>
</html>
