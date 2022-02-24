@if ($have_records)
	<?php extract($stock_transport_log_list); ?>
    @foreach($list_data as $rows)
	<?php
		extract($rows);
		$type_text = null;
		switch($type) {
			case 1:
				$type_text = "Goods In";
				break;

			case 2:
				$type_text = "Sales Invoice";
				break;

			case 3:
				$type_text = "Stock Transfer";
				break;

			case 4:
				$type_text = "Stock Withdraw";
				break;

			default:
				break;
		}
	?>
		
    <div class="list_item_row">
        <span class="list_column col_date">{{ $log_time }}</span>
        <span class="list_column col_log_type">{{ $type_text }}</span>
        <span class="list_column col_log_desc">{{ $desc }}</span>
    </div>
    @endforeach
@endif
<input type="hidden" id="stock_transport_log_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="stock_transport_log_list_total_pages" value="{{ $total_pages }}" />
