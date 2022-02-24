<?php
    extract($stock_withdraw_list);
?>
@if ($have_records)
<?php
    $list_action_buttons = ($status == 1 && $is_allow_delete) ?
    '<span class="list_column col_action_buttons" title="Cancel Transfer Order"><span class="list_delete_button noProp glyphicon glyphicon-remove"></span></span>' : null;
?>
    @foreach($list_data as $rows)
    	<?php extract($rows); ?>
    <div class="list_item_row" id="item_{{ $id }}" data-record_id="{{ $id }}">
        <span class="list_column col_id">{{ $id }}</span>
        <span class="list_column col_date">{{ $withdraw_date }}</span>
        <span class="list_column col_supplier_code">{{ $supplier_code }}</span>
        <span class="list_column col_create_by">{{ $create_by }}</span>
        <span class="list_column col_total_items">{{ $total_items }}</span>
        <span class="list_column col_total_amount">{{ $total_amount }}</span>
        <span class="list_column col_action_buttons" title="View Stock Withdraw Details"><span class="list_details_button noProp glyphicon glyphicon-eye-open"></span></span>
        {{ $list_action_buttons }}
    </div>
    @endforeach
@endif
<input type="hidden" id="stock_withdraw_popup_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="stock_withdraw_popup_list_total_pages" value="{{ $total_pages }}" />