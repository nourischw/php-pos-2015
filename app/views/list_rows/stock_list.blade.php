<?php extract($stock_list); ?>
@if ($have_records)
    @foreach($list_data as $rows)
    	<?php extract($rows); ?>
    <div class="list_item_row" id="item_{{ $stock_id }}" data-record_id="{{ $stock_id }}">
        <span class="list_column col_stock_id">{{ $stock_id }}</span>
        <span class="list_column col_shop_code">{{ $shop_code }}</span>
        <span class="list_column col_product_upc">{{ $product_upc }}</span>
        <span class="list_column col_product_name">{{ $product_name }}</span>
        <span class="list_column col_serial_number">{{ $serial_number }}</span>
        <span class="list_column col_remain_qty">{{ $remain_qty }}</span>
        <span class="list_column col_action_buttons" title="View Log"><span class="list_view_log_button noProp glyphicon glyphicon-menu-hamburger"></span></span>
    </div>
    @endforeach
@endif
<input type="hidden" id="stock_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="stock_list_total_pages" value="{{ $total_pages }}" />