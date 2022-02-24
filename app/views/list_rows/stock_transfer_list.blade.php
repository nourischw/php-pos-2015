<?php
    extract($stock_transfer_list);
    $is_show_checkbox = false;
?>
@if ($have_records)
<?php
    $list_action_buttons = null;
	if ($status != 2) {
		$list_action_buttons .= '
        <span class="list_column col_action_buttons" title="View Stock Transfer"><span class="list_details_button noProp glyphicon glyphicon-eye-open"></span></span>
		';
	}
	
    switch ($status) {
    case 1:
        if ($is_allow_print):
            $list_action_buttons .= '
        <span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>
            ';
        endif;
        if ($is_allow_cancel):
            $list_action_buttons .= '
        <span class="list_column col_action_buttons" title="Cancel Transfer Order"><span class="list_cancel_button noProp glyphicon glyphicon-remove"></span></span>
            ';
        endif;
        break;
		
    case 2:
        if ($is_allow_update):
            $list_action_buttons = '
        <span class="list_column col_action_buttons" title="Edit Stock Transfer Record"><span class="list_edit_button noProp glyphicon glyphicon-pencil"></span></span>
            ';
        endif;
        if ($is_allow_delete):
            $list_action_buttons .= '
        <span class="list_column col_action_buttons" title="Delete Transfer Order"><span class="list_delete_single_item_button noProp glyphicon glyphicon-trash"></span></span>
            ';
            $is_show_checkbox = true;
        endif;
        break;
		
    case 3:
        if ($is_allow_print):
            $list_action_buttons .= '
        <span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>
            ';
        endif;
        break;
		
	case 4:
        if ($is_allow_print):
            $list_action_buttons .= '
        <span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>
            ';
        endif;
		break;
    }
?>
    @foreach($list_data as $rows)
    	<?php extract($rows); ?>
    <div class="list_item_row" id="item_{{ $id }}" data-record_id="{{ $id }}">
        @if ($is_show_checkbox)
        <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span>
        @endif
        <span class="list_column col_transfer_number">{{ $stock_transfer_number }}</span>
        <span class="list_column col_date">{{ $date_out }}</span>
        @if ($status === 3)
        <span class="list_column col_date">{{ $date_in }}</span>
        @endif
        <span class="list_column col_shop_code">{{ $from_shop_code }}</span>
        <span class="list_column col_shop_code">{{ $to_shop_code }}</span>
        <span class="list_column col_request_by">{{ $request_by }}</span>
        <span class="list_column col_total_items">{{ $total_items }}</span>
        <span class="list_column col_total_qty">{{ $total_qty }}</span>
        {{ $list_action_buttons }}
    </div>
    @endforeach
@endif
<input type="hidden" id="is_show_checkbox" value="{{ ($is_show_checkbox) ? 1 : 0 }}" />
<input type="hidden" id="stock_transfer_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="stock_transfer_list_total_pages" value="{{ $total_pages }}" />