<?php 
	extract($sales_invoice_list); 
    $is_show_checkbox = false;
?>
@if ($have_records)
    <?php
        $list_action_buttons = null;
        switch ($status) {
        case 1:
            if ($is_allow_print):
            $list_action_buttons = '
        <span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>
            ';
            endif;
            if ($is_allow_delete):
            $list_action_buttons .= '
        <span class="list_column col_action_buttons" title="Void SI"><span class="list_void_si_button noProp glyphicon glyphicon-erase"></span></span>
            ';
            $is_show_checkbox = true;
            endif;
            break;
        case 2:
            if ($is_allow_update):
            $list_action_buttons = '
        <span class="list_column col_action_buttons"><span class="list_edit_button noProp glyphicon glyphicon-pencil" title="Edit SI"></span></span>
            ';
            endif;
            if ($is_allow_void):
            $list_action_buttons .= '
        <span class="list_column col_action_buttons" title="Void SI"><span class="list_void_si_button noProp glyphicon glyphicon-erase"></span></span>
            ';
            $is_show_checkbox = true;
            endif;
            break;
        case 3:
            break;
        case 4:
            if ($is_allow_print):
            $list_action_buttons = '
        <span class="list_column col_action_buttons" title="Download PDF"><span class="list_download_button noProp glyphicon glyphicon-save"></span></span>
            ';
            endif;
            if ($is_allow_void):
            $list_action_buttons .= '
        <span class="list_column col_action_buttons" title="Void SI"><span class="list_void_si_button noProp glyphicon glyphicon-erase"></span></span>
            ';
            $is_show_checkbox = true;
            endif;
            break;
        }
    ?>
    @foreach($list_data as $rows)
    	<?php extract($rows, EXTR_PREFIX_ALL, 'si'); ?>
<div class="list_item_row" id="item_{{ $si_id }}" data-record_id="{{ $si_id }}">
    @if ($is_show_checkbox)
    <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span>
    @endif
	<span class="list_column col_sales_order_number">{{ $si_sales_invoice_number }}</span>
	<span class="list_column col_create_date">{{ $si_create_time }}</span>
	<span class="list_column col_shop_code">{{ $si_code }}</span>
	<span class="list_column col_request_by">{{ $si_last_update_by }}</span>
	<span class="list_column col_amount">{{ $si_total_amount }}</span>
	<span class="list_column col_total_qty">{{ $si_total_item }}</span>
	<span class="list_column col_total_qty">{{ $si_total_qty }}</span>
	<span class="list_column col_amount">{{ $si_net_total_amount }}</span>
    <span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View Sales Invoice Details"></span></span>
    {{ $list_action_buttons }}
</div>
    @endforeach
@endif
<div class="list_button_row">
	<div class="button_row_left_column">
		<div class="list_buttons">
			@if (Session::get('sales_list_status') != 3)
                @if ($is_allow_void)
				<button id="list_void_multi_si_button" class="fL btn btn-default btn-sm list_page_buttons" data-type="void">Void</button>
				@endif
                <!--button id="list_confirm_multi_si_button" class="fL btn btn-default btn-sm list_page_buttons" data-type="confirm">Confirm</button-->
			@endif
		</div>
		<div class="total_records_column">
			<strong>Total records found: </strong>
			<span id="total_records">{{ $total_records }}</span>
		</div>
	</div>
	<div class="paging_bar_column" {{ ($total_pages < 2) ? 'style="display: none"' : null }}>
		<ul class="pagination pagination-sm fR list_pagination_bar" id="page_bar" data-list_id="sales_invoice_list">
			<li class="disabled"><a href="#" aria-label="First" class="page_button disable" data-type="first"><span aria-hidden="true">&laquo;</span></a></li>
			<li class="disabled"><a href="#" aria-label="Previous" class="page_button disable" data-type="prev"><span aria-hidden="true">&lsaquo;</span></a></li>
			@for ($i = 1; $i <= $end_page; $i++)
			<li class="list_pages {{ ($page === $i) ? 'active' : null }}"><a href="#" class="page_button" data-type="page">{{ $i }}</a></li>
			@endfor
			<li {{ ($total_pages > 1 && $page === $total_pages) ? 'class="disable"' : null; }}><a href="#" aria-label="Next" class="page_button {{ ($page > 1 && $page < $end_page) ? 'disable' : null; }}" data-type="next"><span aria-hidden="true">&rsaquo;</span></a></li>
			<li {{ ($total_pages > 1 && $page === $total_pages) ? 'class="disable"' : null; }}><a href="#" aria-label="Last" class="page_button {{ ($page > 1 && $page < $end_page) ? 'disable' : null; }}" data-type="last"><span aria-hidden="true">&raquo;</span></a></li>
		</ul>
	</div>	
</div>
<input type="hidden" id="is_show_checkbox" value="{{ ($is_show_checkbox) ? 1 : 0 }}" />
<input type="hidden" id="sales_invoice_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="sales_invoice_list_total_pages" value="{{ $total_pages }}" />