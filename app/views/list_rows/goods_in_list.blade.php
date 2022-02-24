<?php 
	extract($goods_in_list); 
	$is_show_checkbox = false;
?>
@if (!empty($list_data))
    <?php
        $list_action_buttons = null; 
		$list_action_buttons = '
			<span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View PO Details"></span></span>';
		
		if ($is_allow_print):
		$list_action_buttons .= '
			<span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>';
		endif;
    ?>
    @foreach($list_data as $rows)
    	<?php extract($rows, EXTR_PREFIX_ALL, 'gi'); ?>
<div class="list_item_row" id="item_{{ $gi_id }}" data-record_id="{{ $gi_id }}">
		<span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span>
		<span class="list_column col_gi_number">{{ $gi_goods_in_number }}</span>
		<span class="list_column col_create_date">{{ $gi_create_time }}</span>
		<span class="list_column col_shop_code">{{ $gi_goods_in_to }}</span>
		<span class="list_column col_request_by">{{ $gi_request_by }}</span>
		<span class="list_column col_total_items">{{ $gi_total_items }}</span>
		<span class="list_column col_total_qty">{{ $gi_total_qty }}</span>
		<span class="list_column col_invoice_no">{{ $gi_invoice_no }}</span>
    {{ $list_action_buttons }}
</div>
    @endforeach
@endif
<input type="hidden" id="purchase_order_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="purchase_order_list_total_pages" value="{{ $total_pages }}" />
