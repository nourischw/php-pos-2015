@if ($have_record)
	@foreach($list_data as $rows)
		<?php 
			extract($rows);
			$extra_class = null;
			if ($qty < 1) {
				$extra_class = 'no_qty';
			}
			else if ($qty < 5) {
				$extra_class = 'qty_warning';
			}
		?>
<div class="list_item_row {{ $extra_class }}">
    <span class="list_column col_location">{{ $code }}</span>
    <span class="list_column col_qty">{{ $qty }}</span>
</div>
	@endforeach
@endif
<input type="hidden" id="total_shop_records" value="{{ $total_shop_records }}">