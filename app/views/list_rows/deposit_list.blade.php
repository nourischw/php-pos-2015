<?php 
    extract($deposit_list); 
    $is_show_checkbox = ($is_allow_delete) ? true : false;
?>
@if ($have_records)
    <?php
        $list_action_buttons = null;
        switch ($status) {
        case 0:
            if ($is_allow_void):
                $list_action_buttons = '<span class="list_column col_action_buttons" title="Void Deposit"><span class="list_void_button noProp glyphicon glyphicon-erase"></span></span>';
                $is_show_checkbox = true;
            endif;
            break;
        case 1:
            if ($is_allow_unvoid):
                $list_action_buttons = '<span class="list_column col_action_buttons" title="Unvoid Deposit"><span class="list_unvoid_button noProp glyphicon glyphicon-repeat"></span></span>';
                $is_show_checkbox = true;
            endif;
            break;
        }
    ?>
    @foreach ($list_data as $value)
        <?php extract($value, EXTR_PREFIX_ALL, 'd'); ?>
    <div class="list_item_row" id="item_{{ $d_id }}" data-record_id="{{ $d_id }}">
        @if ($is_show_checkbox)
        <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span>
        @endif
        <span class="list_column col_deposit_number">{{ $d_deposit_number }}</span>
        <span class="list_column col_deposit_date">{{ $d_deposit_date }}</span>
        <span class="list_column col_quotation_number">{{ $d_quotation_number }}</span>
        <span class="list_column col_deposit_terms">{{ $deposit_terms[$d_deposit_terms] }}</span>
        <span class="list_column col_payment_type">{{ $payment_type[$d_payment_type] }}</span>
        <span class="list_column col_total_items">{{ $d_total_items }}</span>
        <span class="list_column col_total_qty">{{ $d_total_qty }}</span>
        <span class="list_column col_amount">{{ $d_total_amount }}</span>
        <span class="list_column col_amount">{{ $d_payment_amount }}</span>
        <span class="list_column col_amount">{{ $d_sub_total_amount }}</span>
        <span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View Deposit Details"></span></span>
        @if ($is_allow_update)
        <span class="list_column col_action_buttons" title="Edit Deposit"><span class="list_edit_button noProp glyphicon glyphicon-pencil"></span></span>
        @endif
        {{ $list_action_buttons }}
        @if ($is_allow_print)
        <span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>
        @endif
        @if ($is_allow_delete)
        <span class="list_column col_action_buttons" title="Delete Deposit"><span class="list_delete_single_item_button noProp glyphicon glyphicon-trash"></span></span>
        @endif
    </div>
    @endforeach
    <input type="hidden" id="is_show_checkbox" value="{{ ($is_show_checkbox) ? 1 : 0 }}" />
    <input type="hidden" id="deposit_list_total_records" value="{{ $total_records }}" />
    <input type="hidden" id="deposit_list_total_pages" value="{{ $total_pages }}" />
@endif