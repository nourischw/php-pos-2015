<?php 
    extract($purchase_order_list);
    $is_show_checkbox = false;
?>
@if ($have_records)
    <?php
        $list_action_buttons = null;
        switch ($status) {
        case 1:
            $list_action_buttons = '
        <span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View PO Details"></span></span>
            ';
            if ($is_allow_print):
            $list_action_buttons .= '
        <span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>
            ';
            endif;
            if ($is_allow_void):
            $list_action_buttons .= '
        <span class="list_column col_action_buttons" title="Void PO"><span class="list_void_po_button noProp glyphicon glyphicon-erase"></span></span>
            ';
            $is_show_checkbox = true;
            endif;
            break;
        case 2:
            if ($is_allow_update):
            $list_action_buttons = '
        <span class="list_column col_action_buttons"><span class="list_edit_button noProp glyphicon glyphicon-pencil" title="Edit PO"></span></span>
            ';
            endif;
            if ($is_allow_confirm):
            $list_action_buttons .= '
        <span class="list_column col_action_buttons" title="Confirm PO"><span class="list_confirm_po_button noProp glyphicon glyphicon-ok"></span></span>
            ';
            $is_show_checkbox = true;
            endif;
            if ($is_allow_delete):
            $list_action_buttons .= '
        <span class="list_column col_action_buttons" title="Delete PO"><span class="list_delete_single_item_button noProp glyphicon glyphicon-trash"></span></span>
            ';
            $is_show_checkbox = true;
            endif;
            break;
        case 3:
            $list_action_buttons = '
        <span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View PO Details"></span></span>
            ';
            if ($is_allow_unvoid):
            $list_action_buttons .= '
        <span class="list_column col_action_buttons" title="Unvoid PO"><span class="list_unvoid_po_button noProp glyphicon glyphicon-repeat"></span></span>
            ';
            $is_show_checkbox = true;
            endif;
            break;
        case 4:
            $list_action_buttons = '
        <span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View PO Details"></span></span>
            ';
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
    <span class="list_column col_po_number">{{ $purchase_order_number }}</span>
    <span class="list_column col_create_date">{{ $create_time }}</span>
    <span class="list_column col_shop_code">{{ $shop_code }}</span>
    <span class="list_column col_ship_to">{{ $ship_to }}</span>
    <span class="list_column col_request_by">{{ $request_by }}</span>
    <span class="list_column col_total_items">{{ $total_items }}</span>
    <span class="list_column col_total_qty">{{ $total_qty }}</span>
    <span class="list_column col_amount">{{ $total_amount }}</span>
    <span class="list_column col_amount">{{ $discount_amount }}</span>
    <span class="list_column col_amount">{{ $net_amount }}</span>
    {{ $list_action_buttons }}
</div>
    @endforeach
@endif
<input type="hidden" id="is_show_checkbox" value="{{ ($is_show_checkbox) ? 1 : 0 }}" />
<input type="hidden" id="purchase_order_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="purchase_order_list_total_pages" value="{{ $total_pages }}" />