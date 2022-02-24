@section('content')
<div id="stock_list" class="page_content">
	<form id="form_stock_list" data-list_id="stock_list">
		<div class="form_block">
			<div class="field_column">
				<div class="form_row">
					<label class="form_label" for="search_from_shop">Shop Code:</label>
                    <select class="form-control text_field" id="search_shop" name="search_shop">
                    	<option value="">All</option>
                        @foreach ($shop_list as $shop)
							<?php extract($shop); ?>
						<option value="{{ $id }}">{{ $code }}</option>
						@endforeach
                    </select>
                </div>

				<div class="form_row">
					<label class="form_label">Product UPC:</label>
					<div class="fL form_field"><input type="text" id="search_product_upc" name="search_product_upc" class="form-control text_field" /></div>
					<label class="form_label">Product Name:</label>
					<div class="fL form_field"><input type="text" name="search_product_name" class="form-control text_field" /></div>
					<label class="form_label">Serial Number:</label>
					<div class="fL form_field"><input type="text" id="search_serial_number" name="search_serial_number" class="form-control text_field" /></div>
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
				<span class="list_column col_stock_id">Stock ID</span>
				<span class="list_column col_shop_code">Shop Code</span>
				<span class="list_column col_product_upc">Product UPC</span>
				<span class="list_column col_product_name">Product Name</span>
				<span class="list_column col_serial_number">Serial Number</span>
				<span class="list_column col_remain_qty">Remain Qty</span>
				<span class="list_column col_action_buttons"></span>
			</div>
			<div class="list_no_items_row" {{ ($have_record) ? 'style="display: none"' : null; }}>(No record found)</div>
			<div class="list_items" id="list_items">
			@if ($have_record)
				@foreach ($list_data as $value)
					<?php extract($value, EXTR_PREFIX_ALL, 'stl'); ?>
                <div class="list_item_row" id="item_{{ $stl_stock_id }}" data-record_id="{{ $stl_stock_id }}">
                	<span class="list_column col_stock_id">{{ $stl_stock_id }}</span>
					<span class="list_column col_shop_code">{{ $stl_shop_code }}</span>
					<span class="list_column col_product_upc">{{ $stl_product_upc }}</span>
					<span class="list_column col_product_name">{{ $stl_product_name }}</span>
					<span class="list_column col_serial_number">{{ ($stl_serial_number != null) ? $stl_serial_number : "---" }}</span>
					<span class="list_column col_remain_qty">{{ $stl_remain_qty }}</span>
					<span class="list_column col_action_buttons" title="View Log"><span class="list_view_log_button noProp glyphicon glyphicon-menu-hamburger"></span></span>
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
					<ul class="pagination pagination-sm fR list_pagination_bar" id="page_bar" data-list_id="stock_list">
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