@foreach($list_data as $rows)
<?php 
	extract($rows); 
	$required_imei_text = ($required_imei) == 1 ? "YES" : "NO";
?>
<div class="popup_list_item_row">
	<span class="list_column col_upc">{{ $upc }}</span>
	<span class="list_column col_name">{{ $name }}</span>
	<span class="list_column col_unit_price">{{ $unit_price }}</span>
	<span class="list_column col_required_imei">{{ $required_imei_text }}</span>
	<input type="hidden" class="product_id_popup" value="{{ $product_id }}">
</div>
@endforeach
<input type="hidden" id="product_popup_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="product_popup_list_total_pages" value="{{ $total_pages }}" />