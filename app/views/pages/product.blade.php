<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
    @section('content')
    <link href="{{ Config::get('path.CSS') }}product.css" media="all" rel="stylesheet" type="text/css">
	@include('popups.brand_list')
	<div id="product" class="page_content">
		<input type="hidden" id="currentPage" value="<?php echo $page; ?>">
		<input type="hidden" id="lastPage" value="<?php echo $total_pages; ?>">
		<div class="left_column">
			<div class="left_column_form_block">
				<div class="w100">
					<div class="product_search_text_field input-group">
						<label class="fL new_item_label" for="product_upc">Product Search:</label>
						<input type="text" class="w100 form-control product_search_text_field" id="product_search" name="product_search" placeholder="Product Search" value="{{ Session::get('product_keyword') }}">
					</div>
				</div>
			</div>
			<div class="list_container">
				<div id="item_list" class="item_list">
					<div class="list_header">
						<span class="list_checkbox_column">
							#
						</span>
						<span class="list_column col_product_upc">Product UPC</span>
						<span class="list_column col_product_name">Product Name</span>
						<span class="list_column col_product_unit_price">Unit Price</span>
						<span class="list_column col_brand_name">Brand</span>
						<span class="list_column col_category">Category</span>
					</div>
					<div class="list_no_items_row" {{ ($have_record) ? 'style="display: none"' : null; }}>(No record found)</div>	
					<div class="product_item_list" id="product_item_list">
					@if($have_record)
						@foreach ($products as $key => $item)
							<?php extract($item); ?>
							<div class="list_item_row" data-item_id="{{ $id }}">
								<span class="list_checkbox_column">{{ $key+1 }}</span>
								<span class="list_column col_product_upc">{{ $barcode }}</span>
								<span class="list_column col_product_name">{{ $name }}</span>
								<span class="list_column col_product_unit_price">${{ $unit_price }}</span>
								<span class="list_column col_brand_name">{{ $brand_name }}</span>
								<span class="list_column col_category">{{ $category }}</span>
							</div>
						@endforeach
					@endif
					</div>
				</div>
				<div class="modal-footer" style="border: 0px;">
					<div class="list_pagination">
						<ul class="pagination pagination-sm list_pagination_bar" id="page_bar">
							@if ($total_pages > 1)
							<li class="prev_buttons"><a href="{{ Config::get('path.ROOT') }}product/1" id="" aria-label="First" class="page_button"data-type="first"><span aria-hidden="true">&laquo;</span></a></li>
							<li class="prev_buttons"><a href="{{ Config::get('path.ROOT') }}product/{{ $page -1 }}" id="" aria-label="Previous" class="page_button" data-type="prev"><span aria-hidden="true">&lsaquo;</span></a></li>
							@for ($i = 1; $i <= $total_pages; $i++)
							<li class="list_page_row" data-page="{{ $i }}"><a href="{{ Config::get('path.ROOT') }}product/{{ $i }}" class="page_button" data-type="page">{{ $i }}</a></li>
							@endfor
							<li class="next_buttons"><a href="{{ Config::get('path.ROOT') }}product/{{ $page +1 }}" aria-label="Next" class="page_button" data-type="next"><span aria-hidden="true">&rsaquo;</span></a></li>
							<li class="next_buttons"><a href="{{ Config::get('path.ROOT') }}product/{{ $total_pages }}" aria-label="Last" class="page_button" data-type="last"><span aria-hidden="true">&raquo;</span></a></li>
							@endif
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="right_column">
			<div class="form_block">
				<div id="form_title" class="form_title">Product Create</div>
				<div class="form_row product_images_block" style="display: none;">
					<label for="Product_images" class="form_label">Product Images:</label>
					<span id="product_images "class="product_images"></span>
					<button class="btn btn-default btn-xs zoom_images_btn"><span class="glyphicon glyphicon-new-window"></span></button>
					<button class="btn btn-default btn-xs remove_images_btn"><span class="glyphicon glyphicon-remove-circle"></span></button>
					<button class="btn btn-default btn-xs return_images_btn" style="display: none;"><span class="glyphicon glyphicon-repeat"></span></button>
					<input type="hidden" class="remove_images">
				</div>				
				<div class="form_row">
					<label for="product_upc" class="form_label"><sup class="crRed">*</sup>Product UPC:</label>
					<input type="text" class="text_field form-control validateItem" id="product_upc" name="product_upc" />
					<div class="fR fs14 bold crRed check_product_upc" id="check_product_upc"></div>
					<div class="form-error_message" id="error_product_upc"></div>
				</div>
				<div class="form_row input-group">
					<label for="product_brand" class="form_label"><sup class="crRed">*</sup>Product Brand:</label>
					<div class="fL">
						<input type="text" id="product_brand" class="brand_text_field form-control validateItem" name="product_brand">
						<input type="hidden" id="product_brand_id" name="product_brand_id">
						<span class="input-group-btn text_field_buttons">
							<button id="search_brand_button" class="btn btn-default text_field_button show_popup_list_button" data-list_id="brand_popup_list">...</button>
							<!--button class="btn btn-default text_field_button posA" id="get_supplier_info_button"><span class="glyphicon glyphicon-search"></span></button-->
							<button id="get_brand_remove_button" class="btn btn-default text_field_button posA" id="get_brand_remove_button"><span class="glyphicon glyphicon-remove"></span></button>
						</span>
					</div>
					<div class="form-error_message" id="error_product_brand"></div>
				</div>
				<div class="form_row">
					<label for="product_name" class="form_label"><sup class="crRed">*</sup>Product Name:</label>
					<input type="text" class="text_field form-control validateItem" id="product_name" name="product_name" />
					<div class="form-error_message" id="error_product_name"></div>
				</div>
				<div class="form_row">
					<label for="product_unit_price" class="form_label"><sup class="crRed">*</sup>Unit Price:</label>
					<input type="text" class="text_field form-control validateItem" id="unit_price" name="unit_price" />
					<div class="form-error_message" id="error_unit_price"></div>
				</div>
				<div class="form_row">
					<label for="product_reference_cost" class="form_label"><sup class="crRed">*</sup>Reference Cost:</label>
					<input type="text" class="text_field form-control validateItem" id="reference_cost" name="reference_cost" />
					<div class="form-error_message" id="error_reference_cost"></div>
				</div>
				<div class="form_row">
					<label for="product_spec" class="form_label"><sup class="crRed">*</sup>Product Spec:</label>
					<textarea class="text_field textarea_field form-control validateItem" id="product_spec" name="product_spec" rows="3"/></textarea>
					<div class="form-error_message" id="error_product_spec"></div>
				</div>
				<div class="form_row">
					<label for="product_remark" class="form_label"><sup class="crRed">*</sup>Product Remark:</label>
					<textarea class="text_field textarea_field form-control validateItem" id="product_remark" name="product_remark" rows="3"/></textarea>
					<div class="form-error_message" id="error_product_remark"></div>
				</div>
				<div class="form_row">
					<label for="product_category" class="form_label"><sup class="crRed">*</sup>Product Category:</label>
					<!--input type="text" class="text_field form-control validateItem" id="product_category" name="product_category" /-->
					<select class="text_field form-control validateItem" id="product_category" name="product_category">
						<option value="" selected="selected">All</option>
						@foreach ($categories as $item)
							<?php extract($item); ?>
							<option value="{{ $id }}">{{ $name }}</option>
						@endforeach
					</select>
					<div class="form-error_message" id="error_product_category"></div>
				</div>
				
				<div class="form_row">
					<label for="upload_images" class="form_label"></sup>Upload Images:</label>
					<input type="file" id="upload_product_images" class="form-control upload_product_images" name="upload_product_images">
				</div>				
				<div class="form_row">
					<label for="required_imei" class="form_label"><sup class="crRed"></sup>Require imei:</label>
					<input type="checkbox" id="required_imei" class="btn btn-default required_imei" name="required_imei" value="0">
				</div>					
				<div class="form_row">
					<div id="product_edit" class="product_edit" style="display: none">
						<input type="reset" class="btn btn-default btn-sm product_block_buttons btn-order-reset" id="btn-order-reset" value="Cancel">
					@if($is_allow_delete)
						<input type="button" class="btn btn-default btn-sm product_block_buttons" id="btn-order-delete" value="Delete">
					@endif
					@if($is_allow_update)
						<input type="button" class="btn btn-default btn-sm product_block_buttons" id="btn-order-edit" value="Edit">
					@endif
					</div>
					<div id="product_submit" class="product_submit">
						<input type="reset" class="btn btn-default btn-sm product_block_buttons btn-order-reset" id="btn-order-reset" value="Reset All">
					@if($is_allow_create)
						<input type="submit" class="btn btn-default btn-sm product_block_buttons" id="btn-order-add" value="Create">
					@endif
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="../app/js/libs/donetyping.js"></script>
	<script src="../app/js/libs/shortcut.js"></script>
	<script src="../app/js/app/product_page_list.js"></script>

    <footer class=""></footer>
    @stop
</body>
</html>
