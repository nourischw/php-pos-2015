@section('content')
<div id="stock_withdraw_list" class="page_content">
	<form id="form_stock_withdraw_list" data-list_id="stock_withdraw_list">
		<div class="form_block">
			<div class="field_column">
				<div class="btn-group form_row" data-toggle="buttons">
					<label class="form_label">Status:</label>
					<label class="btn btn-default btn-xs status_button active" data-status="1"><input type="radio" value="1" name="search_status" />Processing</label>
					<label class="btn btn-default btn-xs status_button" data-status="2"><input type="radio" value="2" name="search_status" />Finished</label>
				</div>
				<div class="form_row">
					<label class="form_label" for="search_stock_withdraw_id">Record ID:</label>
					<input type="text" class="form-control text_field" id="search_stock_withdraw_id" name="search_stock_withdraw_id" />
					<label class="form_label" for="search_shop_id">Supplier Code:</label>
                    <select class="form-control text_field" id="search_supplier_id" name="search_supplier_id">
                    	<option value="">All</option>
                        @foreach ($supplier_list as $supplier)
							<?php extract($supplier); ?>
						<option value="{{ $id }}">{{ $code }} - {{ $name }}</option>
						@endforeach
                    </select>
                </div>
                <div class="form_row" style="margin-bottom: 0px;">
					<label class="form_label">Date period:</label>
                    <div class="input-group">
                        <div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
                        <input type="text" class="form-control date_field datepicker" name="search_from_date" />
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
				<button class="btn btn-default btn-sm search_block_buttons list_reset_button">Clear Search</button>
			</div>
		</div>
	</form>

	@if ($is_allow_create)
	<div class="fL w100" style="margin-bottom: 10px;"><button class="btn btn-primary btn-xs redirect_button" data-redirect_page="stock_withdraw/edit">Create New Stock Withdraw order</button></div>
	@endif

	<div class="fL taC w100 alert alert-info" id="loading_data_message" role="alert">Loading data, please wait</div>
	
	<div class="list_container">
		<div id="item_list" class="item_list">
			<div class="list_header">
				<span class="list_column col_id">ID</span>
				<span class="list_column col_date">Date</span>
				<span class="list_column col_supplier_code">Supplier Code</span>
				<span class="list_column col_create_by">Request by</span>
				<span class="list_column col_total_items">Total Items</span>
				<span class="list_column col_total_amount">Total Amount</span>
			</div>
			<div class="list_no_items_row" {{ ($have_record) ? 'style="display: none"' : null; }}>(No record found)</div>
			<div class="list_items" id="list_items">
			@if ($have_record)
				@foreach ($list_data as $value)
					<?php extract($value, EXTR_PREFIX_ALL, 'sw'); ?>
                <div class="list_item_row" id="item_{{ $sw_id }}" data-record_id="{{ $sw_id }}">
					<span class="list_column col_id">{{ $sw_id }}</span>
					<span class="list_column col_date">{{ $sw_withdraw_date }}</span>
					<span class="list_column col_supplier_code">{{ $sw_supplier_code }}</span>
					<span class="list_column col_create_by">{{ $sw_create_by }}</span>
					<span class="list_column col_total_items">{{ $sw_total_items }}</span>
					<span class="list_column col_total_amount">{{ $sw_total_amount }}</span>
				    <span class="list_column col_action_buttons" title="View Stock Withdraw Details"><span class="list_details_button noProp glyphicon glyphicon-eye-open"></span></span>
					@if ($is_allow_delete)
				    <span class="list_column col_action_buttons" title="Delete Record"><span class="list_delete_button noProp glyphicon glyphicon-trash"></span></span>
                	@endif
                </div>
				@endforeach
			@endif
			</div>
			<div class="list_button_row">
				<div class="button_row_left_column">
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
	
    <div class="panel panel-success result_alert_box" id="delete_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Delete stock withdraw record successfully.<br />
			Stock items is returned back to the original shop<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>
	
    <div class="panel panel-danger result_alert_box" id="delete_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to delete stock withdraw record. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>
</div>
@stop