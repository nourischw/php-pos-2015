@section('content')
<?php
	$list_action_buttons = null;
	switch (Session::get('sales_list_status')) {
    case 1:
        if ($is_allow_print):
        $list_action_buttons = '
    <span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>
        ';
        endif;
        if ($is_allow_void):
        $list_action_buttons .= '
    <span class="list_column col_action_buttons" title="Void SI"><span class="list_void_si_button noProp glyphicon glyphicon-erase"></span></span>
        ';
        $is_show_checkbox = true;
        endif;
        break;
    case 2:
        if ($is_allow_update):
        $list_action_buttons = '
    <span class="list_column col_action_buttons"><span class="list_edit_button noProp glyphicon glyphicon-pencil" title="Edit SI"></span></span>
        ';
        endif;
        if ($is_allow_void):
        $list_action_buttons .= '
    <span class="list_column col_action_buttons" title="Void SI"><span class="list_void_si_button noProp glyphicon glyphicon-erase"></span></span>
        ';
        $is_show_checkbox = true;
        endif;
        break;
    case 3:
        break;
    case 4:
        if ($is_allow_print):
        $list_action_buttons = '
    <span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>
        ';
        endif;
        if ($is_allow_void):
        $list_action_buttons .= '
    <span class="list_column col_action_buttons" title="Void SI"><span class="list_void_si_button noProp glyphicon glyphicon-erase"></span></span>
        ';
        $is_show_checkbox = true;
        endif;
        break;
	}
?>
<div id="sales_invoice_list" class="page_content">
	<form id="form_sales_invoice_list" data-list_id="sales_invoice_list">
		<div class="form_block">
			<div class="field_column">
				<div class="btn-group form_row" data-toggle="buttons">
					<label class="form_label">Status:</label>
					<input type="hidden" id="get_status" value="{{ Session::get('sales_list_status') }}">
					@if ($is_allow_create)
					<label class="btn btn-default btn-xs sales_status_button" data-status="0"><input type="radio" value="0" name="search_status"  />New</label>
					@endif
					<label class="btn btn-default btn-xs sales_status_button" data-status="2"><input type="radio" value="2" name="search_status" />Pending</label>
					<label class="btn btn-default btn-xs sales_status_button" data-status="3"><input type="radio" value="3" name="search_status" />Voided</label>
					<label class="btn btn-default btn-xs sales_status_button active" data-status="1"><input type="radio" value="1" name="search_status" />Finished</label>
				</div>
				<div class="form_row" style="margin-bottom: 0px;">
					<label class="form_label" for="search_invoice_number">Invoice Number:</label>
					<input type="text" class="form-control text_field" id="search_sales_invoice_number" name="search_sales_invoice_number" />
					<label class="form_label" for="search_shop">Shop:</label>
			        <select class="form-control text_field" id="search_shop" name="search_shop">
			          	<option value="">All</option>
			            @foreach ($shop_list as $shop)
							<?php extract($shop); ?>
						<option value="{{ $id }}">{{ $code }}</option>
						@endforeach
			        </select>
					<label class="form_label" for="from_date">Create Date:</label>
		            <div class="input-group">
		                <div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
		                <input type="text" class="form-control date_field datepicker" id="search_from_date" name="search_from_date" />
		                <div class="input-group-addon clear_icon"><span class="glyphicon glyphicon-erase"></span></div>
		            </div>
		            <span class="fL" style="margin: 0px 5px;"> to </span>
		            <div class="input-group">
		                <div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
		                <input type="text" class="form-control date_field datepicker" name="search_to_date" />
		                <div class="input-group-addon clear_icon"><span class="glyphicon glyphicon-erase"></span></div>
		            </div>
				</div>
			</div>

			<div class="button_column">
				<button class="btn btn-default btn-sm search_block_buttons list_search_button">Search</button><br />
				<input type="button" class="btn btn-default btn-sm search_block_buttons list_clear_button" value="Clear Search">
			</div>
		</div>
	</form>
	@if ($is_allow_create)
	<div class="fL w100" style="margin-bottom: 10px;">
		<button class="btn btn-primary btn-xs redirect_button" data-redirect_page="sales_invoice/order">Create New Sales Invoice</button>
	</div>
	@endif
	<div class="fL taC w100 alert alert-info" id="loading_data_message" role="alert">Loading data, please wait</div>

	<div class="list_container">
		<div id="item_list" class="item_list">
			<div class="list_header">
				<span class="list_checkbox_column">
					<input type="checkbox" class="list_checkbox_all" id="list_checkbox_all" />
				</span>
				<span class="list_column col_sales_order_number">Invoice Number</span>
				<span class="list_column col_create_date">Create Date</span>
				<span class="list_column col_shop_code">Shop</span>
				<span class="list_column col_request_by">Request by</span>
				<span class="list_column col_amount">Total Amt</span>
				<span class="list_column col_total_qty">Total Item</span>
				<span class="list_column col_total_qty">Total Qty</span>
				<span class="list_column col_amount">Net Amt</span>
			</div>
			<div class="list_no_items_row" {{ ($have_record) ? 'style="display: none"' : null; }}>(No record found)</div>
			<div class="list_items" id="list_items">
			@if ($have_record)
				@foreach ($list_data as $value)
				<?php extract($value, EXTR_PREFIX_ALL, 'si'); ?>
            <div class="list_item_row" id="item_{{ $si_id }}" data-record_id="{{ $si_id }}">
                <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span>
                <span class="list_column col_sales_order_number">{{ $si_sales_invoice_number }}</span>
                <span class="list_column col_create_date">{{ $si_create_time }}</span>
                <span class="list_column col_shop_code">{{ $si_code }}</span>
                <span class="list_column col_request_by">{{ $si_staff_name }}</span>
                <span class="list_column col_amount">{{ $si_total_amount }}</span>
                <span class="list_column col_total_qty">{{ $si_total_item }}</span>
                <span class="list_column col_total_qty">{{ $si_total_qty }}</span>
                <span class="list_column col_amount">{{ $si_net_total_amount }}</span>
                <span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View Sales Invoice Details"></span></span>
				{{ $list_action_buttons }}
			</div>
				@endforeach
			@endif
			<div class="list_button_row">
				<div class="button_row_left_column">
					<div class="list_buttons">
					@if(Session::get('sales_list_status')!=3)
						@if ($is_allow_void)
						<button id="list_void_multi_si_button" class="fL btn btn-default btn-sm list_page_buttons" data-type="void">Void</button>
						@endif
						@if ($is_allow_confirm)
						<button id="list_confirm_multi_si_button" class="fL btn btn-default btn-sm list_page_buttons" data-type="confirm">Confirm</button>
						@endif
					@endif
					</div>
					<div class="total_records_column">
						<strong>Total records found: </strong>
						<span id="total_records">{{ $total_records }}</span>
					</div>
				</div>
				<div class="paging_bar_column" {{ ($total_pages < 2) ? 'style="display: none"' : null }}>
					<ul class="pagination pagination-sm fR list_pagination_bar" id="page_bar" data-list_id="sales_invoice_list">
						<li class="disabled"><a href="#" aria-label="First" class="page_button disable" data-type="first"><span aria-hidden="true">&laquo;</span></a></li>
						<li class="disabled"><a href="#" aria-label="Previous" class="page_button disable" data-type="prev"><span aria-hidden="true">&lsaquo;</span></a></li>
						@for ($i = 1; $i <= $end_page; $i++)
						<li class="list_pages {{ ($page === $i) ? 'active' : null }}"><a href="#" class="page_button" data-type="page">{{ $i }}</a></li>
						@endfor
						<li {{ ($total_pages > 1 && $page === $total_pages) ? 'class="disable"' : null; }}><a href="#" aria-label="Next" class="page_button {{ ($page > 1 && $page < $end_page) ? 'disable' : null; }}" data-type="next"><span aria-hidden="true">&rsaquo;</span></a></li>
						<li {{ ($total_pages > 1 && $page === $total_pages) ? 'class="disable"' : null; }}><a href="#" aria-label="Last" class="page_button {{ ($page > 1 && $page < $end_page) ? 'disable' : null; }}" data-type="last"><span aria-hidden="true">&raquo;</span></a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
