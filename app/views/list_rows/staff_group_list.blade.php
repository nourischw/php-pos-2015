<?php 
    extract($staff_group_list); 
?>
@if ($have_records)
    @foreach ($list_data as $value)
        <?php extract($value); ?>
    <div class="list_item_row" id="item_{{ $id }}" data-record_id="{{ $id }}">
        <span class="list_column col_staff_group_name">
            @if ($is_primary)
            <span class="glyphicon glyphicon-star"></span>
            @endif
            {{ $name }}
        </span>
        <span class="list_column col_staff_group_description">{{ $description }}</span>
        <span class="list_column col_members">{{ $members }}</span>
        @if ($id != 1)
        <span class="list_column col_action_buttons"><span class="list_details_button noProp glyphicon glyphicon-eye-open" title="View Staff Group Details"></span></span>
            @if ($is_allow_update)
            <span class="list_column col_action_buttons" title="Edit Staff Group"><span class="list_edit_button noProp glyphicon glyphicon-pencil"></span></span>
            @endif        
        @endif
        @if ($is_allow_delete && $is_primary != 1 && $members === 0)
        <span class="list_column col_action_buttons" title="Remove Staff Group"><span class="list_delete_single_item_button noProp glyphicon glyphicon-trash"></span></span>
        @endif
    </div>
    @endforeach
@endif
<input type="hidden" id="is_show_checkbox" value="0" />
<input type="hidden" id="staff_group_list_total_records" value="{{ $total_records }}" />
<input type="hidden" id="staff_group_list_total_pages" value="{{ $total_pages }}" />