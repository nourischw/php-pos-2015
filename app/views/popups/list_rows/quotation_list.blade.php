<?php 
    $quote_type_list = Config::get("quote_type");
    $quote_terms_list = Config::get("payment_type");
?>
@foreach($list_data as $rows)
	<?php extract($rows);?>
<div class="popup_list_item_row" data-quotation_id="{{ $id }}">
	<span class="list_column col_quotation_number">{{ $quotation_number }}</span>
	<span class="list_column col_quote_date">{{ $quote_date }}</span>
    <span class="list_column col_quote_type">{{ $quote_type_list[$quote_type] }}</span>
    <span class="list_column col_quote_terms">{{ $quote_terms_list[$quote_terms] }}</span>
	<span class="list_column col_total_items">{{ $total_items }}</span>
	<span class="list_column col_total_qty">{{ $total_qty }}</span>
	<span class="list_column col_amount">{{ $total_amount }}</span>
	<span class="list_column col_amount">{{ $discount_amount }}</span>
	<span class="list_column col_amount col_quotation_sub_total_amount">{{ $sub_total_amount }}</span>
	<span class="list_column col_shop_code">{{ $code }}</span>
</div>
@endforeach
<input type="hidden" id="quotation_popup_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="quotation_popup_list_total_pages" value="{{ $total_pages }}" />