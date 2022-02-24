@section('content')
<div id="stock_transport_log_list" class="page_content">
	<div class="id_row">
		<div class="id_label" style="margin-right: 10px; text-align: right; width: 140px;">Product UPC:</div>
		{{ $stock_data['product_upc'] }}
		<div style="margin-top: 10px;">
			<div class="row_label" style="font-weight: bold; float: left; margin-right: 10px; text-align: right; width: 140px;">Product Name:</div>
			<div class="row_field" style="float: left;">
				{{ $stock_data['product_name'] }}
				@if ($stock_data['serial_number'] != "")
					<span class="product_serial_number" style="margin-left: 10px; font-style: italic;">(S/N: {{ $stock_data['serial_number'] }})</span>
				@endif
			</div>
		</div>
	</div>

	<form id="form_stock_transport_log_list" data-list_id="stock_transport_log_list">
		<div class="form_block">
			<div class="field_column">
				<div class="btn-group form_row" data-toggle="buttons">
					<label class="form_label">Type:</label>
					<label class="btn btn-default btn-xs stl_type_button active" data-status="0"><input type="radio" value="0" name="search_type" />All</label>
					<label class="btn btn-default btn-xs stl_type_button" data-status="1"><input type="radio" value="1" name="search_type" />Goods In</label>
					<label class="btn btn-default btn-xs stl_type_button" data-status="2"><input type="radio" value="2" name="search_type" />Sales Invoice</label>
					<label class="btn btn-default btn-xs stl_type_button" data-status="3"><input type="radio" value="3" name="search_type" />Stock Transfer</label>
                    <label class="btn btn-default btn-xs stl_type_button" data-status="4"><input type="radio" value="4" name="search_type" />Stock Withdraw</label>
				</div>
                <div class="form_row" style="margin-bottom: 0px;">
					<label class="form_label">Log Period:</label>
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
				<button class="btn btn-default btn-sm search_block_buttons list_reset_button">Clear Search</button><br />
				<button class="btn btn-default btn-sm redirect_button" data-redirect_page="stock/list">Return to List</button>
			</div>
		</div>
		<input type="hidden" name="stock_id" value="{{ $stock_id }}" />
	</form>

	<div class="fL taC w100 alert alert-info" id="loading_data_message" role="alert">Loading data, please wait</div>

	<div class="list_container">
		<div id="item_list" class="item_list">
			<div class="list_header">
				<span class="list_column col_date">Log Date</span>
				<span class="list_column col_log_type">Type</span>
				<span class="list_column col_log_desc">Desc</span>
			</div>
			<div class="list_no_items_row" {{ ($have_record) ? 'style="display: none"' : null; }}>(No record found)</div>
			<div class="list_items" id="list_items">
			@if ($have_record)
				@foreach ($list_data as $value)
					<?php extract($value, EXTR_PREFIX_ALL, 'stl'); ?>
                <div class="list_item_row">
                	<span class="list_column col_date">{{ $stl_log_time }}</span>
                	<?php
                		$type = null;
                		switch($stl_type) {
                			case 1:
                				$type = "Goods In";
                				break;

                			case 2:
                				$type = "Sales Invoice";
                				break;

                			case 3:
                				$type = "Stock Transfer";
                				break;

                			case 4:
                				$type = "Stock Withdraw";
                				break;

                			default:
                				break;
                		}
                	?>
					<span class="list_column col_log_type">{{ $type }}</span>
					<span class="list_column col_log_desc">{{ $stl_desc }}</span>
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
					<ul class="pagination pagination-sm fR list_pagination_bar" id="page_bar" data-list_id="stock_transport_log_list">
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
