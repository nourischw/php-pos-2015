@foreach($list_data as $rows)
	<?php extract($rows); ?>
<div class="popup_list_item_row" data-supplier_id="{{ $id }}">
    <span class="list_column col_code">{{ $code }}</span>
    <span class="list_column col_name">{{ $name }}</span>
    <span class="list_column col_mobile">{{ $mobile }}</span>
    <span class="list_column col_fax">{{ $fax }}</span>
    <span class="list_column col_email">{{ $email }}</span>
</div>
@endforeach
<input type="hidden" id="supplier_popup_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="supplier_popup_list_total_pages" value="{{ $total_pages }}" />