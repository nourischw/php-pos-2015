@section('content')
<div id="quotation_list" class="page_content">
	<form id="form_quotation_list" data-list_id="quotation_list">
		<div class="form_block">
			<div class="field_column">
				<div class="btn-group form_row" data-toggle="buttons">
					<label class="form_label">Status:</label>
					<label class="btn btn-default btn-xs quotation_status_button active" data-status="0"><input type="radio" value="0" name="search_status" checked />Normal</label>
					<label class="btn btn-default btn-xs quotation_status_button" data-status="1"><input type="radio" value="1" name="search_status" />Voided</label>
				</div>
				<div class="form_row">
					<label class="form_label">Quote Type:</label>
	                <select class="form-control text_field" name="search_quote_type">
	                	<option value="">All</option>
	                    @foreach ($quote_type as $id => $text)
						<option value="{{ $id }}">{{ $text }}</option>
						@endforeach
	                </select>
	                <label class="form_label">Terms:</label>
	                <select class="form-control text_field" name="search_quote_terms">
	                	<option value="">All</option>
	                	@foreach ($quote_terms as $id => $text)
	                	<option value="{{ $id }}">{{ $text }}</option>
	                	@endforeach
	                </select>
	            </div>
	            <div class="form_row" style="margin-bottom: 0px;">
	                <label class="form_label" for="search_quotation_number">Quotation Number:</label>
	                <input type="text" class="form-control text_field" id="search_quotation_number" name="search_quotation_number" />
	                <label class="form_label" for="search_from_date">Quote Date:</label>
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
				<button class="btn btn-default btn-sm search_block_buttons list_reset_button">Clear Search</button>
			</div>
		</div>
	</form>

	@if ($is_allow_create)
	<div class="fL w100" style="margin-bottom: 10px;"><button class="btn btn-primary btn-xs redirect_button" data-redirect_page="quotation/edit">Create New Quotation</button></div>
	@endif

	<div class="fL taC w100 alert alert-info" id="loading_data_message" role="alert">Loading data, please wait</div>
	
	<div class="list_container">
		<div id="item_list" class="item_list">
			<div class="list_header">
				<span class="list_checkbox_column" {{ (!$is_show_checkbox) ? 'style="display: none"' : null }}>
					<input type="checkbox" class="list_checkbox_all" id="list_checkbox_all" />
				</span>
				<span class="list_column col_quotation_number">Quotaton Number</span>
				<span class="list_column col_quote_date">Quote Date</span>
				<span class="list_column col_quote_type">Quote Type</span>
				<span class="list_column col_quote_terms">Terms</span>
				<span class="list_column col_total_items">Items</span>
				<span class="list_column col_total_qty">Total Qty</span>
				<span class="list_column col_amount">Total Amt</span>
				<span class="list_column col_amount">Discount</span>
				<span class="list_column col_amount">Sub Total</span>
			</div>
			<div class="list_no_items_row" {{ ($have_record) ? 'style="display: none"' : null; }}>(No record found)</div>
			<div class="list_items" id="list_items">
			@if ($have_record)
				@foreach ($list_data as $value)
					<?php extract($value, EXTR_PREFIX_ALL, 'q'); ?>
                <div class="list_item_row" id="item_{{ $q_id }}" data-record_id="{{ $q_id }}">
                    <span class="list_checkbox_column" {{ (!$is_show_checkbox) ? 'style="display: none"' : null }}><input type="checkbox" class="list_item_checkbox" /></span>
                    <span class="list_column col_quotation_number">{{ $q_quotation_number }}</span>
                    <span class="list_column col_quote_date">{{ $q_quote_date }}</span>
                    <span class="list_column col_quote_type">{{ $quote_type[$q_quote_type] }}</span>
                    <span class="list_column col_quote_terms">{{ $quote_terms[$q_quote_terms] }}</span>
                    <span class="list_column col_total_items">{{ $q_total_items }}</span>
                    <span class="list_column col_total_qty">{{ $q_total_qty }}</span>
                    <span class="list_column col_amount">{{ $q_total_amount }}</span>
                    <span class="list_column col_amount">{{ $q_discount_amount }}</span>
                    <span class="list_column col_amount">{{ $q_sub_total_amount }}</span>
				    <span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View Quotation Details"></span></span>
                    @if ($is_allow_update)
			        <span class="list_column col_action_buttons" title="Edit Quotation"><span class="list_edit_button noProp glyphicon glyphicon-pencil"></span></span>
			        @endif
			        @if ($is_allow_print)
			        <span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>
			        @endif
			        @if ($is_allow_void)
			        <span class="list_column col_action_buttons" title="Void Quotation"><span class="list_void_quotation_button noProp glyphicon glyphicon-erase"></span></span>
			        @endif
			        @if ($is_allow_delete)
			        <span class="list_column col_action_buttons" title="Delete Quotation"><span class="list_delete_single_item_button noProp glyphicon glyphicon-trash"></span></span>
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
					<ul class="pagination pagination-sm fR list_pagination_bar" id="page_bar" data-list_id="purchase_order_list">
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