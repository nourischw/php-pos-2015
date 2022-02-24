@if (!empty($list_data))
@foreach($list_data as $rows)
	<?php extract($rows);?>
<div class="popup_list_item_row" data-stock_id="{{ $stock_id }}" data-product_id="{{ $product_id }}">
	<span class="list_column col_product_upc">{{ $product_upc }}</span>
	<span class="list_column col_product_name">{{ $product_name }}</span>
	<span class="list_column col_serial_number">{{ (!empty($serial_number)) ? $serial_number : "---"; }}</span>
	<span class="list_column col_qty">{{ $remain_qty }}</span>
	<span class="list_column col_unit_price">{{ $unit_price }}</span>
</div>
@endforeach
@endif
<input type="hidden" id="stock_popup_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="stock_popup_list_total_pages" value="{{ $total_pages }}" />