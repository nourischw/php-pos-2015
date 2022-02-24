@section('content')
<div id="stock_transfer_list" class="page_content">
	<form id="form_stock_transfer_list" data-list_id="stock_transfer_list">
		<div class="form_block">
			<div class="field_column">
				<div class="btn-group form_row" data-toggle="buttons">
					<label class="form_label">Status:</label>
					<label class="btn btn-default btn-xs st_status_button active" data-status="1"><input type="radio" value="1" name="search_status" />Processing</label>
					<label class="btn btn-default btn-xs st_status_button" data-status="2"><input type="radio" value="2" name="search_status" />Pending</label>
					<label class="btn btn-default btn-xs st_status_button" data-status="4"><input type="radio" value="4" name="search_status" />Delivered</label>
					<label class="btn btn-default btn-xs st_status_button" data-status="3"><input type="radio" value="3" name="search_status" />Finished</label>
                    <label class="btn btn-default btn-xs st_status_button" data-status="5"><input type="radio" value="5" name="search_status" />Cancelled</label>
				</div>
				<div class="form_row">
					<label class="form_label" for="search_stock_transfer_number">Order Number:</label>
					<input type="text" class="form-control text_field" id="search_stock_transfer_number" name="search_stock_transfer_number" />
					<label class="form_label" for="search_from_shop">From Shop:</label>
                    <select class="form-control text_field" id="search_from_shop" name="search_from_shop">
                    	<option value="">All</option>
                        @foreach ($shop_list as $shop)
							<?php extract($shop); ?>
						<option value="{{ $id }}">{{ $code }}</option>
						@endforeach
                    </select>
					<label class="form_label" for="search_to_shop">To Shop:</label>
                    <select class="form-control text_field" id="search_to_shop" name="search_to_shop">
                    	<option value="">All</option>
                        @foreach ($shop_list as $shop)
							<?php extract($shop); ?>
						<option value="{{ $id }}">{{ $code }}</option>
						@endforeach
                    </select>
                </div>
                <div class="form_row" style="margin-bottom: 0px;">
					<label class="form_label">Date Out:</label>
                    <div class="input-group">
                        <div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
                        <input type="text" class="form-control date_field datepicker" name="search_from_date_out" />
                        <div class="input-group-addon clear_icon"><span class="glyphicon glyphicon-erase"></span></div>
                    </div>
                    <span class="fL" style="margin: 0px 5px;"> to </span>
                    <div class="input-group">
                        <div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
                        <input type="text" class="form-control date_field datepicker" name="search_to_date_out" />
                        <div class="input-group-addon clear_icon"><span class="glyphicon glyphicon-erase"></span></div>
                    </div>
				</div>
			</div>

			<div class="button_column">
				<button class="btn btn-default btn-sm search_block_buttons list_search_button">Search</button><br />
				<button class="btn btn-default btn-sm search_block_buttons list_reset_button">Clear Search</button>
			</div>
		</div>
	</form>

	@if ($is_allow_create)
	<div class="fL w100" style="margin-bottom: 10px;"><button class="btn btn-primary btn-xs redirect_button" data-redirect_page="stock_transfer/edit">Create New Stock Transfer order</button></div>
	@endif

	<div class="fL taC w100 alert alert-info" id="loading_data_message" role="alert">Loading data, please wait</div>
	
	<div class="list_container">
		<div id="item_list" class="item_list">
			<div class="list_header">
				<span class="list_checkbox_column" {{ (!$is_show_checkbox) ? 'style="display: none"' : null }}>
					<input type="checkbox" class="list_checkbox_all" id="list_checkbox_all" />
				</span>
				<span class="list_column col_transfer_number">TX Number</span>
				<span class="list_column col_date">Date Out</span>
				<span class="list_column col_date col_date_in" style="display: none">Date In</span>
				<span class="list_column col_shop_code">From Shop</span>
				<span class="list_column col_shop_code">To Shop</span>
				<span class="list_column col_request_by">Request by</span>
				<span class="list_column col_total_items">Items</span>
				<span class="list_column col_total_qty">Total Qty</span>
			</div>
			<div class="list_no_items_row" {{ ($have_record) ? 'style="display: none"' : null; }}>(No record found)</div>
			<div class="list_items" id="list_items">
			@if ($have_record)
				@foreach ($list_data as $value)
					<?php extract($value, EXTR_PREFIX_ALL, 'st'); ?>
                <div class="list_item_row" id="item_{{ $st_id }}" data-record_id="{{ $st_id }}">
					<span class="list_column col_transfer_number">{{ $st_stock_transfer_number }}</span>
					<span class="list_column col_date">{{ $st_date_out }}</span>
					<span class="list_column col_shop_code">{{ $st_from_shop_code }}</span>
					<span class="list_column col_shop_code">{{ $st_to_shop_code }}</span>
					<span class="list_column col_request_by">{{ $st_request_by }}</span>
					<span class="list_column col_total_items">{{ $st_total_items }}</span>
					<span class="list_column col_total_qty">{{ $st_total_qty }}</span>
				    <span class="list_column col_action_buttons" title="View Stock Transfer Details"><span class="list_details_button noProp glyphicon glyphicon-eye-open"></span></span>

					@if ($is_allow_print)
				    <span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>
                	@endif
				    @if ($is_allow_cancel)
					<span class="list_column col_action_buttons" title="Cancel Transfer Order"><span class="list_cancel_button noProp glyphicon glyphicon-remove"></span></span>
					@endif
					@if ($is_allow_view_log)
					<span class="list_column col_action_buttons" title="View Log"><span class="list_view_log_button noProp glyphicon glyphicon-menu-hamburger"></span></span>
					@endif
                </div>
				@endforeach
			@endif
			</div>
			<div class="list_button_row">
				<div class="button_row_left_column">
					<div class="list_buttons">
						@if ($is_allow_delete)
						<button id="list_delete_multi_item_button" class="fL btn btn-default btn-sm list_delete_button" data-type="delete">Delete</button>
						@endif
					</div>
					<div class="total_records_column">
						<strong>Total records found: </strong>
						<span id="total_records">{{ $total_records }}</span>
					</div>
				</div>
				<div class="paging_bar_column" {{ ($total_pages < 2) ? 'style="display: none"' : null }}>
					<ul class="pagination pagination-sm fR list_pagination_bar" id="page_bar" data-list_id="stock_transfer_list">
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
	
    <div class="panel panel-success result_alert_box" id="cancel_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Cancel stock transfer successfully.<br />
			Stock items is returned back to the original shop<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>
	
    <div class="panel panel-danger result_alert_box" id="cancel_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to cancel stock transfer order. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

</div>
@stop