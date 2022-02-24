@if (!empty($list_data))
    @foreach ($list_data as $value)
        <?php extract($value, EXTR_PREFIX_ALL, 'stock'); ?>
    <div class="list_item_row" id="item_{{ $stock_id }}" data-record_id="{{ $stock_id }}">
        <span class="list_column col_shop_code">{{ $stock_shop_code }}</span>
        <span class="list_column col_product_barcode">{{ $stock_product_barcode }}</span>
        <span class="list_column col_product_name">{{ $stock_product_name }}</span>
        <span class="list_column col_serial_number">{{ $stock_serial_number }}</span>
    </div>
    @endforeach
@endif
<input type="hidden" id="sn_transaction_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="sn_transaction_list_total_pages" value="{{ $total_pages }}" />
