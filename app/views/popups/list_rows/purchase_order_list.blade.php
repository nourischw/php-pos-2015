@foreach($list_data as $rows)
	<?php extract($rows); ?>
<div class="popup_list_item_row" data-po_id="{{ $id }}">
    <span id="" class="list_column col_po_no">{{ $purchase_order_number }}</span>
    <span id="" class="list_column col_supplier">{{ $supplier_code }}</span>
    <span id="" class="list_column col_ship_to">{{ $shop_code }}</span>
    <span id="" class="list_column col_qty">{{ $total_qty }}</span>
    <span id="" class="list_column col_amount">{{ $total_amount }}</span>
    <span id="" class="list_column col_update_by">{{ $last_update_by }}</span>
    <span id="" class="list_column col_create_date">{{ $create_time }}</span>
</div>

<input type="hidden" id="purchase_order_popup_list_supplier_id_{{ $id }}" value="{{ $supplier_id }}" />
<input type="hidden" id="purchase_order_popup_list_supplier_name_{{ $id }}" value="{{ $supplier_name }}" />
<input type="hidden" id="purchase_order_popup_list_supplier_mobile_{{ $id }}" value="{{ $supplier_mobile }}" />
<input type="hidden" id="purchase_order_popup_list_supplier_fax_{{ $id }}" value="{{ $supplier_fax }}" />
<input type="hidden" id="purchase_order_popup_list_supplier_email_{{ $id }}" value="{{ $supplier_email }}" />
@endforeach
<input type="hidden" id="purchase_order_popup_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="purchase_order_popup_list_total_pages" value="{{ $total_pages }}" />
