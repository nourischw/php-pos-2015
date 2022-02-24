@section('content')
<div id="staff_group_list" class="page_content">
	<form id="form_staff_group_list" data-list_id="staff_group_list">
		<div class="form_block">
			<div class="field_column">
	            <div class="form_row" style="margin-bottom: 0px;">
	                <label class="form_label" for="search_staff_group_name">Group Name:</label>
					<input type="text" class="form-control text_field" id="search_staff_group_name" name="search_staff_group_name" />
				</div>
			</div>
			<div class="button_column">
				<button class="btn btn-default btn-sm search_block_buttons list_search_button">Search</button><br />
				<button class="btn btn-default btn-sm search_block_buttons list_reset_button">Clear Search</button>
			</div>
		</div>
	</form>

	@if ($is_allow_create)
	<div class="fL w100" style="margin-bottom: 10px;"><button class="btn btn-primary btn-xs redirect_button" data-redirect_page="staff_group/edit">Create New Staff Group</button></div>
	@endif

	<div class="fL taC w100 alert alert-info" id="loading_data_message" role="alert">Loading data, please wait</div>
	
	<div style="color: red; line-height: 30px;">
		Notice: Only normal group (without <span class="glyphicon glyphicon-star"></span>) or no group members' staff group can be deleted
	</div>
	<div class="list_container">
		<div id="item_list" class="item_list">
			<div class="list_header">
				<span class="list_column col_staff_group_name">Group Name</span>
				<span class="list_column col_staff_group_description">Description</span>
				<span class="list_column col_member">Members</span>
			</div>
			<div class="list_no_items_row" {{ ($have_record) ? 'style="display: none"' : null; }}>(No record found)</div>
			<div class="list_items" id="list_items">
			@if ($have_record)
				@foreach ($list_data as $value)
					<?php extract($value, EXTR_PREFIX_ALL, 'sg'); ?>
                <div class="list_item_row" id="item_{{ $sg_id }}" data-record_id="{{ $sg_id }}">
                    <span class="list_column col_staff_group_name">
                    	@if ($sg_is_primary)
                    	<span class="glyphicon glyphicon-star"></span>
                    	@endif
                    	{{ $sg_name }}
                    </span>
                    <span class="list_column col_staff_group_description">{{ $sg_description }}</span>
                    <span class="list_column col_members">{{ $sg_members }}</span>
			        @if ($sg_id != 1)
				    <span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View Staff Group Details"></span></span>
			        	@if ($is_allow_update)
			        <span class="list_column col_action_buttons" title="Edit Staff Group"><span class="list_edit_button noProp glyphicon glyphicon-pencil"></span></span>
			        	@endif
			        @endif
			        @if ($sg_is_primary != 1 && $sg_members === 0 && $is_allow_delete)
			        <span class="list_column col_action_buttons" title="Remove Staff Group"><span class="list_delete_single_item_button noProp glyphicon glyphicon-trash"></span></span>
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
					<ul class="pagination pagination-sm fR list_pagination_bar" id="page_bar" data-list_id="staff_group_list">
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