@if ($have_record)
	@foreach($list_data as $rows)
		<?php extract($rows); ?>
<div class="list_item_row" data-product_id="{{ $id }}" data-product_image="{{ $product_image }}">
    <span class="list_column col_category">{{ $category_id, ' - ', $category_name }}</span>
    <span class="list_column col_product_upc">{{ $barcode }}</span>
    <span class="list_column col_product_name">{{ $name }}</span>
</div>
	@endforeach
@endif
<input type="hidden" id="stock_level_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="stock_level_list_total_pages" value="{{ $total_pages }}" />
