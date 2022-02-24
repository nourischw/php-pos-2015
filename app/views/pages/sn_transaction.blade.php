@section('content')
<div id="sn_transaction_list" class="page_content">
    <div class="left_column">
		<form id="form_sn_transaction_list" data-list_id="sn_transaction_list">
			<div class="form_block">
				<div class="field_column">
					<div class="form_row">
						<div class="search_field">
							<label class="form_label" for="search_shop_code">Shop Code:</label>
							<select class="text_field form-control" name="search_shop_id">
								<option value="">All</option>
								@foreach ($shop_list as $shop)
									<?php extract($shop); ?>
								<option value="{{ $id }}">{{ $code }}</option>
								@endforeach
							</select>
						</div>
						<div class="search_field">
							<label class="form_label" for="search_product_name">Product Name:</label>
							<input type="text" class="form-control text_field" name="search_product_name" />
						</div>					
					</div>
					<div class="form_row" style="margin-bottom: 0px;">
						<div class="search_field">
							<label class="form_label" for="search_product_barcode">Barcode:</label>
							<input type="text" class="form-control text_field" name="search_product_barcode" />
						</div>
						<div class="search_field">
							<label class="form_label" for="search_shop">Serial Number:</label>
		                    <input type="text" class="form-control text_field" name="search_serial_number" />
	                    </div>
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
					<span class="list_column col_shop_code">Shop Code</span>
					<span class="list_column col_product_barcode">Barcode</span>
					<span class="list_column col_product_name">Product Name</span>
					<span class="list_column col_serial_number">Serial Number</span>
				</div>
				<div class="list_no_items_row" {{ ($have_record) ? 'style="display: none"' : null; }}>(No record found)</div>
				<div class="list_items" id="list_items">
				@if ($have_record)
					@foreach ($list_data as $value)
						<?php extract($value, EXTR_PREFIX_ALL, 'stock'); ?>
	                <div class="list_item_row" id="item_{{ $stock_id }}" data-record_id="{{ $stock_id }}">
	                	<span class="list_column col_shop_code">{{ $stock_shop_code }}</span>
	                	<span class="list_column col_product_barcode">{{ $stock_product_barcode }}</span>
	                	<span class="list_column col_product_name">{{ $stock_product_name }}</span>
	                	<span class="list_column col_serial_number">{{ $stock_serial_number }}</span>
					</div>
					@endforeach
				@endif
				</div>
				<div class="list_button_row">
					<div class="button_row_left_column">
						<strong>Total records found: </strong>
						<span id="total_records">{{ $total_records }}</span>
					</div>
					<div class="paging_bar_column" {{ ($total_pages < 2) ? 'style="display: none"' : null }}>
						<ul class="pagination pagination-sm fR list_pagination_bar" id="page_bar" data-list_id="sn_transaction_list">
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

	<div class="right_column">
		<form id="form_update_sn">
			<div class="form_block">
            	<div class="form_title">Stock SN Info</div>
                <div class="form_row">
                	<label>Shop:</label><br />
                    <span id="shop_code">---</span>
                </div>
                <div class="form_row">
                	<label>Barcode:</label><br />
                    <span id="product_barcode">---</span>
                </div>
                <div class="form_row">
                	<label>Product Name:</label><br />
                    <span id="product_name">---</span>
                </div>
                <div class="form_row">
                	<label>Serial Number:</label><br />
                    <span id="serial_number">---</span>
                </div>
                <div class="form_title">New Serial Number</div>
                <div class="form_row">
                	<label>New SN:</label><br />
                	<input type="text" class="form-control text_field validateItem" id="new_serial_number" name="new_serial_number" disabled />
                	<div class="form-error_message" id="error_new_serial_number"></div>
                </div>
                <div class="form_row">
                	<input type="button" id="update_sn_button" class="btn btn-default" value="Update" disabled />
                </div>
            </div>
            <input type="hidden" id="stock_id" />
		</form>
	</div>

    <div class="panel panel-success result_alert_box" id="update_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Update Stock's Serial Number successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="update_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to update Stock's Serial Number. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>
</div>
@stop
