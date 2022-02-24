<?php extract($category_list); ?>
@section('content')
<link href="{{ Config::get('path.CSS') }}category.css" media="all" rel="stylesheet" type="text/css">
@include('modules/pop_up/category_order/category_order_confirm_block')
@include('modules/pop_up/category_order/category_order_result_block')
<div id="category" class="page_content">
    <div class="left_column">
		<div class="left_column_form_block">
			<div class="w100">
				<div class="category_search_text_field input-group">
					<label class="fL new_item_label" for="category_search">Category Search:</label>
					<input type="text" class="w100 form-control category_search_text_field" id="category_search" name="category_search" placeholder="Category Search" value="{{ Session::get('category_keyword') }}">					
				</div>
			</div>
		</div>		
		<div class="list_container">
			<div id="item_list" class="item_list">
				<div class="list_header">
					<span class="list_column col_no_row">#</span>
					<span class="list_column col_category_name">Name</span>
					<span class="list_column col_update_date">Last Update</span>
				</div>
				<div class="category_item_list" id="category_item_list">
					@foreach ( $category_list as $key => $category )
					<?php extract($category); ?>
					<div id="list_item_row" class="list_item_row" data-item_id="{{ $id }}">
						<span class="list_column col_no_row">{{ $key+1 }}</span>
						<span class="list_column col_category_name">{{ $name }}</span>
						<span class="list_column col_update_date">{{ $last_update }}</span>
					</div>
					@endforeach
				</div>
			</div>	
		</div>	
	</div>				
	<div class="right_column">
		<div class="form_block">
			<div id="category_title" class="form_title">Category Create:</div>
				<form id="form_category_create">
					<div class="form_row">
						<label for="category_name" class="form_label"><sup class="crRed">*</sup>Category Name:</label>
						<input type="text" class="text_field form-control" id="category_name" name="category_name" />
						<div class="form-error_message" id="error_category_name"></div>
					</div>			
					<div class="form_row">
						<div id="category_edit" class="product_edit" style="display: none">
							<input type="reset" class="btn btn-default btn-sm product_block_buttons" id="btn-order-reset" value="Cancel">
						@if($is_allow_delete)
							<input type="button" class="btn btn-default btn-sm product_block_buttons" id="btn-order-delete" value="Delete">
						@endif
						@if($is_allow_update)
							<input type="button" class="btn btn-default btn-sm product_block_buttons" id="btn-order-edit" value="Edit">
						@endif
						</div>										
						<div id="category_submit" class="product_submit">
							<input type="reset" class="btn btn-default btn-sm category_block_buttons" id="btn-order-reset" value="Reset">
						@if($is_allow_create)
							<input type="button" class="btn btn-default btn-sm category_block_buttons" id="btn-order-add" value="Create">
						@endif
						</div>						
					</div>	
					<input type="hidden" value="" id="category_id">
				</form>			
			</div>
		</div>				
	</div>
</div>
<footer class="footer"></footer>
<script src="{{ Config::get('path.ROOT') }}app/js/libs/donetyping.js"></script>	
<script src="{{ Config::get('path.ROOT') }}app/js/libs/shortcut.js"></script>
@stop