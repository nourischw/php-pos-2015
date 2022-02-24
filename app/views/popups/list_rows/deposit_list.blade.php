<?php 
    $deposit_terms_list = Config::get("payment_type");
?>
@foreach($list_data as $rows)
	<?php extract($rows);?>
<div class="popup_list_item_row" data-deposit_id="{{ $id }}">
	<span class="list_column col_deposit_number">{{ $deposit_number }}</span>
	<span class="list_column col_deposit_date">{{ $deposit_date }}</span>
    <span class="list_column col_deposit_terms">{{ $deposit_terms_list[$deposit_terms] }}</span>
	<span class="list_column col_total_items">{{ $total_items }}</span>
	<span class="list_column col_total_qty">{{ $total_qty }}</span>
	<span class="list_column col_amount">{{ $total_amount }}</span>
	<span class="list_column col_amount col_deposit_payment_amount">{{ $payment_amount }}</span>
	<span class="list_column col_amount">{{ $sub_total_amount }}</span>
	<span class="list_column col_shop_code">{{ $shop_code }}</span>
</div>
@endforeach
<input type="hidden" id="deposit_popup_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="deposit_popup_list_total_pages" value="{{ $total_pages }}" />