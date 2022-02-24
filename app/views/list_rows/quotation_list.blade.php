<?php 
    extract($quotation_list); 
    $is_show_checkbox = false;
?>
@if ($have_records)
<?php
    $is_show_checkbox = ($is_allow_delete) ? true : false;
    $list_action_buttons = null; 
    switch ($status) {
    case 0:
        if ($is_allow_void):
            $list_action_buttons = '
        <span class="list_column col_action_buttons" title="Void Quotation"><span class="list_void_quotation_button noProp glyphicon glyphicon-erase"></span></span>
            ';
            $is_show_checkbox = true;
        endif;
        break;
    case 1:
        if ($is_allow_unvoid):
            $list_action_buttons = '
        <span class="list_column col_action_buttons" title="Unvoid Quotation"><span class="list_unvoid_quotation_button noProp glyphicon glyphicon-repeat"></span></span>
            ';
            $is_show_checkbox = true;
        endif;
        break;
    }
?>
    @foreach ($list_data as $value)
        <?php extract($value, EXTR_PREFIX_ALL, 'q'); ?>
        <div class="list_item_row" id="item_{{ $q_id }}" data-record_id="{{ $q_id }}">
            @if ($is_show_checkbox)
            <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span>
            @endif
            <span class="list_column col_quotation_number">{{ $q_quotation_number }}</span>
            <span class="list_column col_quote_date">{{ $q_quote_date }}</span>
            <span class="list_column col_quote_type">{{ $quote_type[$q_quote_type] }}</span>
            <span class="list_column col_quote_terms">{{ $quote_terms[$q_quote_terms] }}</span>
            <span class="list_column col_total_items">{{ $q_total_items }}</span>
            <span class="list_column col_total_qty">{{ $q_total_qty }}</span>
            <span class="list_column col_amount">{{ $q_total_amount }}</span>
            <span class="list_column col_amount">{{ $q_discount_amount }}</span>
            <span class="list_column col_amount">{{ $q_sub_total_amount }}</span>
            <span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View Quotation Details"></span></span>
            @if ($is_allow_update)
            <span class="list_column col_action_buttons" title="Edit Quotation"><span class="list_edit_button noProp glyphicon glyphicon-pencil"></span></span>
            @endif
            @if ($is_allow_print)
            <span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>
            @endif
            {{ $list_action_buttons }}
            @if ($is_allow_delete)
            <span class="list_column col_action_buttons" title="Delete Quotation"><span class="list_delete_single_item_button noProp glyphicon glyphicon-trash"></span></span>
            @endif
        </div>
    @endforeach
@endif
<input type="hidden" id="is_show_checkbox" value="{{ ($is_show_checkbox) ? 1 : 0 }}" />
<input type="hidden" id="quotation_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="quotation_list_total_pages" value="{{ $total_pages }}" />
