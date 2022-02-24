@section('content')
<div id="deposit_list" class="page_content">
	<form id="form_deposit_list" data-list_id="deposit_list">
		<div class="form_block">
			<div class="field_column">
				<div class="btn-group form_row" data-toggle="buttons">
					<label class="form_label">Status:</label>
					<label class="btn btn-default btn-xs de_status_button active" data-status="0"><input type="radio" value="0" name="search_status" checked />Normal</label>
					<label class="btn btn-default btn-xs de_status_button" data-status="1"><input type="radio" value="1" name="search_status" />Voided</label>
				</div>
				<div class="form_row">
					<label class="form_label" for="search_deposit_number">Deposit Number:</label>
					<input type="text" class="form-control text_field" id="search_deposit_number" name="search_deposit_number" />
					<label class="form_label" for="search_deposit_terms">Deposit Terms:</label>
                    <select class="form-control text_field" id="search_deposit_terms" name="search_deposit_terms">
                    	<option value="">All</option>
                        @foreach ($deposit_terms as $id => $terms)
						<option value="{{ $id }}">{{ $terms }}</option>
						@endforeach
                    </select>
                    <label class="form_label" for="search_payment_type">Payment Type:</label>
                    <select class="form-control text_field" id="search_payment_type" name="search_payment_type">
                    	<option value="">All</option>
                    	@foreach ($payment_type as $id => $type)
                    	<option value="{{ $id }}">{{ $type }}</option>
                    	@endforeach
                    </select>
                </div>

                <div class="form_row" style="margin-bottom: 0px;">
                	<label class="form_label" for="search_quotataion_number">Quotation Number:</label>
                	<input type="text" class="form-control text_field" name="search_quotation_number" id="search_quotation_number" />
					<label class="form_label" for="search_from_date">Deposit Date:</label>
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
	<div class="fL w100" style="margin-bottom: 10px;"><button class="btn btn-primary btn-xs redirect_button" data-redirect_page="deposit/edit">Create New Deposit</button></div>
	@endif

	<div class="fL taC w100 alert alert-info" id="loading_data_message" role="alert">Loading data, please wait</div>

	<div class="list_container">
		<div id="item_list" class="item_list">
			<div class="list_header">
				<span class="list_checkbox_column">
					<input type="checkbox" class="list_checkbox_all" id="list_checkbox_all" />
				</span>
				<span class="list_column col_deposit_number">Deposit Number</span>
				<span class="list_column col_deposit_date">Deposit Date</span>
				<span class="list_column col_quotation_number">Quotation Number</span>
				<span class="list_column col_deposit_terms">Deposit Terms</span>
				<span class="list_column col_payment_type">Payment Type</span>
				<span class="list_column col_total_items">Items</span>
				<span class="list_column col_total_qty">Qty</span>
				<span class="list_column col_amount">Total Amt</span>
				<span class="list_column col_amount">Payment Amt</span>
				<span class="list_column col_amount">Sub Total</span>
			</div>
			<div class="list_no_items_row" {{ ($have_record) ? 'style="display: none"' : null; }}>(No record found)</div>
			<div class="list_items" id="list_items">
			@if ($have_record)
				@foreach ($list_data as $value)
					<?php extract($value, EXTR_PREFIX_ALL, 'd'); ?>
                <div class="list_item_row" id="item_{{ $d_id }}" data-record_id="{{ $d_id }}">
                    <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span>
                    <span class="list_column col_deposit_number">{{ $d_deposit_number }}</span>
                    <span class="list_column col_deposit_date">{{ $d_deposit_date }}</span>
                    <span class="list_column col_quotation_number">{{ ($d_quotation_number != '') ? $d_quotation_number : '---'; }}</span>
                    <span class="list_column col_deposit_terms">{{ $deposit_terms[$d_deposit_terms] }}</span>
                    <span class="list_column col_payment_type">{{ $payment_type[$d_payment_type] }}</span>
                    <span class="list_column col_total_items">{{ $d_total_items }}</span>
                    <span class="list_column col_total_qty">{{ $d_total_qty }}</span>
                    <span class="list_column col_amount">{{ $d_total_amount }}</span>
                    <span class="list_column col_amount">{{ $d_payment_amount }}</span>
                    <span class="list_column col_amount">{{ $d_sub_total_amount }}</span>
                    <span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View Deposit Details"></span></span>
                    @if ($is_allow_update)
			        <span class="list_column col_action_buttons" title="Edit Deposit"><span class="list_edit_button noProp glyphicon glyphicon-pencil"></span></span>
					@endif
					@if ($is_allow_void)
					<span class="list_column col_action_buttons" title="Void Deposit"><span class="list_void_button noProp glyphicon glyphicon-erase"></span></span>
			        @endif
			        @if ($is_allow_print)
			        <span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>
			        @endif
			        @if ($is_allow_delete)
			        <span class="list_column col_action_buttons" title="Delete Deposit"><span class="list_delete_single_item_button noProp glyphicon glyphicon-trash"></span></span>
					@endif
				</div>
				@endforeach
			@endif
			</div>
			<div class="list_button_row">
				<div class="button_row_left_column">
					<div class="list_buttons">
						@if ($is_allow_void)
						<button id="list_void_multi_button" class="fL btn btn-default btn-sm list_page_buttons" data-type="void">Void</button>
						@endif
						@if ($is_allow_unvoid)
						<button id="list_unvoid_multi_button" class="fL btn btn-default btn-sm list_page_buttons" data-type="unvoid">Unvoid</button>
						@endif
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
