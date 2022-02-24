@section('content')
<div id="goods_in_list" class="page_content">
	<form id="form_goods_in_list" data-list_id="goods_in_list">
		<div class="form_block">
			<div class="field_column">
				<div class="form_row" style="margin-bottom: 0px;">
					<label class="form_label" for="search_gi_number">GI Number:</label>
					<input type="text" class="form-control text_field" id="search_gi_number" name="search_goods_in_number" />
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
				<div class="form_row" style="margin-bottom: 0px;">
					<div class="fL w100" style="margin: 13px; margin-bottom: 0px;"><button class="btn btn-primary btn-xs redirect_button" data-redirect_page="goods_in/order">Create New Goods In</button></div>
				</div>
			</div>

			<div class="button_column">
				<button class="btn btn-default btn-sm search_block_buttons list_search_button">Search</button><br />
				<button class="btn btn-default btn-sm search_block_buttons list_reset_button">Clear Search</button>
			</div>
		</div>
	</form>

	<div class="fL taC w100 alert alert-info" id="loading_data_message" role="alert">Loading data, please wait</div>
	
	<div class="list_container">
		<div id="item_list" class="item_list">
			<div class="list_header">
				<span class="list_checkbox_column">
					<input type="checkbox" class="list_checkbox_all" id="list_checkbox_all" />
				</span>
				<span class="list_column col_gi_number">GI Number</span>
				<span class="list_column col_create_date">Create Date</span>
				<span class="list_column col_shop_code">Goods In to</span>
				<span class="list_column col_request_by">Request by</span>
				<span class="list_column col_total_items">Items</span>
				<span class="list_column col_total_qty">Total Qty</span>
				<span class="list_column col_invoice_no">Invoice Number</span>
			</div>
			<div class="list_no_items_row" {{ ($have_record) ? 'style="display: none"' : null; }}>(No record found)</div>
			<div class="list_items" id="list_items">
			@if ($have_record)
				@foreach ($list_data as $value)
					<?php extract($value, EXTR_PREFIX_ALL, 'gi'); ?>
                <div class="list_item_row" id="item_{{ $gi_id }}" data-record_id="{{ $gi_id }}">
                    <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span>
                    <span class="list_column col_gi_number">{{ $gi_goods_in_number }}</span>
                    <span class="list_column col_create_date">{{ $gi_create_time }}</span>
                    <span class="list_column col_shop_code">{{ $gi_goods_in_to }}</span>
                    <span class="list_column col_request_by">{{ $gi_request_by }}</span>
                    <span class="list_column col_total_items">{{ $gi_total_items }}</span>
                    <span class="list_column col_total_qty">{{ $gi_total_qty }}</span>
                    <span class="list_column col_invoice_no">{{ $gi_invoice_no }}</span>
				    <span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View GI Details"></span></span>
					@if ($is_allow_print)
				    <span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>
					@endif
				</div>
				@endforeach
			@endif
			<div class="list_button_row">
				<div class="button_row_left_column">
					<div class="total_records_column">
						<strong>Total records found: </strong>
						<span id="total_records">{{ $total_records }}</span>
					</div>
				</div>			
				<div class="paging_bar_column" {{ ($total_pages < 2) ? 'style="display: none"' : null }}>
					<ul class="pagination pagination-sm fR list_pagination_bar" id="page_bar" data-list_id="goods_in_list">
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