<?php extract($brand_list); ?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>

<body>
@section('content')
<link href="{{ Config::get('path.CSS') }}brand.css" media="all" rel="stylesheet" type="text/css">
@include('modules/pop_up/brand_order/brand_order_confirm_block')
@include('modules/pop_up/brand_order/brand_order_result_block')
<div id="goods_in" class="page_content">
    <div class="left_column">
		<div class="left_column_form_block">
			<div class="w100">
				<div class="brand_search_text_field input-group">
					<label class="fL new_item_label" for="brand_search">Brand Search:</label>
					<input type="text" class="w100 form-control brand_search_text_field" id="brand_search" name="brand_search" placeholder="Brand Search" value="{{ Session::get('brand_keyword') }}">					
				</div>
			</div>
		</div>		
		<div class="list_container">
			<div id="item_list" class="item_list">
				<div class="list_header">
					<span class="list_column col_no_row">#</span>
					<span class="list_column col_brand_name">Name</span>
					<span class="list_column col_update_date">Update Date</span>
					<span class="list_column col_update_by">Update By</span>
				</div>
				<div class="brand_item_list" id="brand_item_list">
					@foreach ( $brand_list as $key => $brand )
					<?php extract($brand); ?>
					<div id="list_item_row" class="list_item_row" data-item_id="{{ $id }}">
						<span class="list_column col_no_row">{{ $key+1 }}</span>
						<span class="list_column col_brand_name">{{ $name }}</span>
						<span class="list_column col_update_date">{{ $update_time }}</span>
						<span class="list_column col_update_by">{{ $update_by }}</span>		
						<input type="hidden" class="brand_code_value" name="brand_code_value" value="{{ $id }}">
						<input type="hidden" class="brand_desc_value" name="brand_desc_value" value="{{ $name }}">
						<input type="hidden" class="brand_update_value" name="brand_update_value" value="{{ $update_time }}">
						<input type="hidden" class="brand_by_value" name="brand_by_value" value="{{ $update_by }}">
					</div>
					@endforeach
				</div>
			</div>	
		</div>	
	</div>				
	<div class="right_column">
		<div class="form_block">
			<div id="brand_title" class="form_title">Brand Create:</div>
				<form id="form_brand_create">
					<div class="form_row">
						<label for="brand_name" class="form_label"><sup class="crRed">*</sup>Brand Name:</label>
						<input type="text" class="text_field form-control" id="brand_name" name="brand_name" />
						<div class="form-error_message" id="error_brand_name"></div>
					</div>
					<div class="form_row">
						<label for="brand_remark" class="form_label"><sup class="crRed">*</sup>Brand Remark:</label>
						<textarea class="text_field textarea_field form-control" id="brand_remark" name="brand_remark" rows="3"/></textarea>
						<div class="form-error_message" id="error_brand_remark"></div>
					</div>					
					<div class="form_row">
						<div id="brand_edit" class="product_edit" style="display: none">
							<input type="reset" class="btn btn-default btn-sm product_block_buttons" id="btn-order-reset" value="Cancel">
						@if($is_allow_delete)
							<input type="button" class="btn btn-default btn-sm product_block_buttons" id="btn-order-delete" value="Delete">
						@endif
						@if($is_allow_update)
							<input type="button" class="btn btn-default btn-sm product_block_buttons" id="btn-order-edit" value="Edit">
						@endif
						</div>										
						<div id="brand_submit" class="product_submit">
							<input type="reset" class="btn btn-default btn-sm brand_block_buttons" id="btn-order-reset" value="Reset">
						@if($is_allow_create)
							<input type="button" class="btn btn-default btn-sm brand_block_buttons" id="btn-order-add" value="Create">
						@endif
						</div>						
					</div>	
					<input type="hidden" value="" id="brand_id">
				</form>			
			</div>
		</div>				
	</div>
</div>
<footer class="footer"></footer>
<script src="{{ Config::get('path.ROOT') }}app/js/libs/donetyping.js"></script>	
<script src="{{ Config::get('path.ROOT') }}app/js/libs/shortcut.js"></script>
@stop
</body>
</html>