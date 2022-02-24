<?php 
    extract($staff_list); 
    $is_show_checkbox = ($is_allow_delete) ? true : false;
?>
@if ($have_records)
    @foreach($list_data as $rows)
    	<?php extract($rows); ?>
    <div class="list_item_row" id="item_{{ $id }}" data-record_id="{{ $id }}">
        @if ($is_show_checkbox)
        <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span>
        @endif
        <span class="list_column col_staff_code">{{ $staff_code }}</span>
        <span class="list_column col_staff_name">{{ $name }}</span>
        <span class="list_column col_staff_group">{{ $staff_group_name }}</span>
        <span class="list_column col_shop_code">{{ $shop_code }}</span>
        <span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View Staff Details"></span></span>
        @if ($is_allow_update)
        <span class="list_column col_action_buttons" title="Edit Staff"><span class="list_edit_button noProp glyphicon glyphicon-pencil"></span></span>
        @endif
        @if ($is_allow_reset_password)
        <span class="list_column col_action_buttons" title="Reset Password"><span class="list_reset_password_button noProp glyphicon glyphicon-edit"></span></span>
        @endif
        @if ($is_allow_delete)
        <span class="list_column col_action_buttons" title="Remove Staff"><span class="list_delete_single_item_button noProp glyphicon glyphicon-trash"></span></span>
        @endif
    </div>
    @endforeach
@endif
<input type="hidden" id="is_show_checkbox" value="{{ ($is_show_checkbox) ? 1 : 0 }}" />
<input type="hidden" id="staff_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="staff_list_total_pages" value="{{ $total_pages }}" />