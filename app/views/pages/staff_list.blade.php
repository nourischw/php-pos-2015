@section('content')
<div id="staff_list" class="page_content">
	<form id="form_staff_list" data-list_id="staff_list">
		<div class="form_block">
			<div class="field_column">
				<div class="form_row">
					<label class="form_label" for="search_staff_shop">Staff Shop:</label>
                    <select class="form-control text_field" id="search_shop_id" name="search_shop_id">
                    	<option value="">All</option>
                        @foreach ($shop_list as $shop)
							<?php extract($shop); ?>
						<option value="{{ $id }}">{{ $code }}</option>
						@endforeach
                    </select>
                    <label class="form_label" for="search_staff_group">Staff Group:</label>
                    <select class="form-control text_field" id="search_staff_group" name="search_staff_group">
                    	<option value="">All</option>
                        @foreach ($staff_group as $value)
						<option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
						@endforeach
                    </select>
				</div>
	            <div class="form_row" style="margin-bottom: 0px;">
	                <label class="form_label" for="search_staff_code">Staff Code:</label>
	                <input type="text" class="form-control text_field" id="search_staff_code" name="search_staff_code" />
	                <label class="form_label" for="search_staff_name">Staff Name:</label>
					<input type="text" class="form-control text_field" id="search_staff_name" name="search_staff_name" />
				</div>
			</div>
			<div class="button_column">
				<button class="btn btn-default btn-sm search_block_buttons list_search_button">Search</button><br />
				<button class="btn btn-default btn-sm search_block_buttons list_reset_button">Clear Search</button>
			</div>
		</div>
	</form>

	@if ($is_allow_create)
	<div class="fL w100" style="margin-bottom: 10px;"><button class="btn btn-primary btn-xs redirect_button" data-redirect_page="staff/edit">Create New Staff</button></div>
	@endif

	<div class="fL taC w100 alert alert-info" id="loading_data_message" role="alert">Loading data, please wait</div>
	
	<div class="list_container">
		<div id="item_list" class="item_list">
			<div class="list_header">
				<span class="list_checkbox_column">
					<input type="checkbox" class="list_checkbox_all" id="list_checkbox_all" />
				</span>
				<span class="list_column col_staff_code">Staff Code</span>
				<span class="list_column col_staff_name">Staff Name</span>
				<span class="list_column col_staff_group">Staff Group</span>
				<span class="list_column col_shop_code">Shop</span>
			</div>
			<div class="list_no_items_row" {{ ($have_record) ? 'style="display: none"' : null; }}>(No record found)</div>
			<div class="list_items" id="list_items">
			@if ($have_record)
				@foreach ($list_data as $value)
					<?php extract($value, EXTR_PREFIX_ALL, 'st'); ?>
                <div class="list_item_row" id="item_{{ $st_id }}" data-record_id="{{ $st_id }}">
                    <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span>
                    <span class="list_column col_staff_code">{{ $st_staff_code }}</span>
                    <span class="list_column col_staff_name">{{ $st_name }}</span>
                    <span class="list_column col_staff_group">{{ $st_staff_group_name }}</span>
                    <span class="list_column col_shop_code">{{ $st_shop_code }}</span>
				    <span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View Staff Details"></span></span>
                    @if ($is_allow_update)
			        <span class="list_column col_action_buttons" title="Edit Staff"><span class="list_edit_button noProp glyphicon glyphicon-pencil"></span></span>
			        @endif
			        @if ($is_allow_reset_password)
			        <span class="list_column col_action_buttons" title="Reset Password"><span class="list_reset_password_button noProp glyphicon glyphicon-edit"></span></span>
			        @endif
			        @if ($is_allow_delete)
			        <span class="list_column col_action_buttons" title="Remove Staff"><span class="list_delete_single_item_button noProp glyphicon glyphicon-trash"></span></span>
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
					<ul class="pagination pagination-sm fR list_pagination_bar" id="page_bar" data-list_id="staff_list">
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

@if ($is_allow_reset_password)
<form id="form_reset_staff_password" method="post" action="{{ Config::get("path.ROOT") }}staff/reset_password">
	<input type="hidden" name="record_id" id="record_id" />
	<input type="hidden" name="from_list" value="1" />
</form>
@endif

@stop