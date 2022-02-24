<?php
    $search_brand_id = null;
    $search_brand_name = null;
    if (isset($search_params)) {
        extract($search_params);
    }
    extract($brands);
?>
<link href="{{ Config::get('path.CSS') }}list.css" media="all" rel="stylesheet" type="text/css">
<div class="modal fade popup_list" id="brand_list_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 1000px">
        <div class="modal-content">
            <div class="modal-header" style="border: 0px; height: 45px; padding-top: 10px; padding-bottom: 0px;">
                <div class="page_title">Brand List</div>
                <div class="close_popup_button"><img src="{{ Config::get('path.IMAGES') }}close_button.png" id="close_popup_button" /></div>
            </div>
			<div class="modal-body">
				<div class="search_block">
					<div class="form_row">
						<label class="form_label">Brand Search:</label>
						<div class="fL form_field"><input type="text" id="search_brand_name" name="search_brand_name" class="w100 form-control text_field" value="{{ $search_brand_name }}" /></div>
					</div>
					<!--div class="form_button_row">
						<input type="submit" value="Search" />
						<input type="reset" class="clear_search_button" value="Clear" />
					</div-->
				</div>
				<div id="item_list" class="item_list">
					<div class="list_header">
						<span class="list_column col_brand_id list_order_item" data-order_items="ID">ID</span>
						<span class="list_column col_brand_name list_order_item" data-order_items="name">Name</span>
						<span class="list_column col_brand_remark list_order_item" data-order_items="name">Remark</span>
						<span class="list_column col_brand_update_by list_order_item" data-order_items="name">Update by</span>
					</div>

					<div class="popup_list_items" id="popup_list_items">
						@foreach ($brand_list as $value)
							<?php 
								extract($value); 
								if($remark == ''){
									$remark = "...";
								}
							?>
						<div class="popup_list_item_row" data-brand_id="{{ $id }}">
							<span class="list_column col_brand_id">{{ $id }}</span>
							<span class="list_column col_brand_name">{{ $name }}</span>
							<span class="list_column col_brand_remark">{{ $remark }}</span>
							<span class="list_column col_brand_update_by">{{ $update_by }}</span>
						</div>
						@endforeach
					</div>
				</div>
			</div>

			<div class="modal-footer" style="border: 0px;">
				<div class="list_pagination search_brand">
					<ul class="pagination pagination-sm list_pagination_bar" id="page_bar">
						@if ($brand_list_total_pages > 1)
						<li class="prev_buttons"><a href="#" id="" aria-label="First" class="page_button"data-type="first"><span aria-hidden="true">&laquo;</span></a></li>
						<li class="prev_buttons"><a href="#" id="" aria-label="Previous" class="page_button" data-type="prev"><span aria-hidden="true">&lsaquo;</span></a></li>
						@for ($i = 1; $i <= $brand_list_total_pages; $i++)
						<li class="list_page_row brand_list" data-brand-page="{{ $i }}"><a href="#" class="page_button" data-type="page">{{ $i }}</a></li>
						@endfor
						<li class="next_buttons"><a href="#" aria-label="Next" class="page_button" data-type="next"><span aria-hidden="true">&rsaquo;</span></a></li>
						<li class="next_buttons"><a href="#" aria-label="Last" class="page_button" data-type="last"><span aria-hidden="true">&raquo;</span></a></li>
						@endif
					</ul>
				</div>
			</div>
        </div>
    </div>
</div>