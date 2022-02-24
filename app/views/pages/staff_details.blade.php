@section('content')
<?php extract($staff_data, EXTR_PREFIX_ALL, 'st'); ?>
<div id="staff_details" class="page_content">
	<div class="row">
		<form id="form_staff_details" method="post">
			<div class="form_row">
				<label class="form_label">Staff Code:</label>
				{{ $st_staff_code }}
	        </div>
			<div class="form_row">
				<label class="form_label">Staff Name:</label>
	            {{ $st_name }}
	        </div>
			<div class="form_row">
				<label class="form_label">Shop Code:</label>
				{{ $st_shop_code }}
			</div>
			<div class="form_row">
				<label class="form_label">Staff Group:</label>
				{{ $st_staff_group_name }}
			</div>
			<div class="form_row">
				<label class="form_label">Title:</label>
	           	{{ $st_title }}
	        </div>
			<div class="form_row">
				<label class="form_label">Telephone:</label>
				{{ $st_telephone }}
			</div>
			<div class="form_row">
				<label class="form_label">Mobile:</label>
				{{ $st_mobile }}
			</div>
			<div class="form_row">
				<label class="form_label">Email:</label>
				{{ $st_email }}
			</div>

			@if ($is_allow_reset_password)
			<div class="form_row">
				<input type="button" id="reset_staff_password_button" class="btn btn-default btn-sm" style="margin-left: 190px;" value="Reset Password" />
			</div>
			@endif

			<div class="page_button_row">
	            @if ($is_allow_update)
	            <button data-redirect_page="staff/edit/{{ $record_id }}" class="btn btn-default btn-sm redirect_button" style="width: 100px;">Update</button>
	            @endif
				@if ($is_allow_delete)
	       		<button id="remove_button" class="btn btn-default btn-sm page_buttons">Delete</button>
	        	@endif
	        	<button class="btn btn-default btn-sm page_buttons redirect_button" data-redirect_page="staff/list">Return to List</button>
	        	@if ($is_allow_create)
	        	<button class="btn btn-primary btn-sm redirect_button" id="create_new_record_button" data-redirect_page="staff/edit">Create New Staff</button>
				@endif
			</div>

		    @if ($record_id > 0)
		    <input type="hidden" name="record_id" id="record_id" value="{{ $record_id }}" />
		    @endif
		</form>
	</div>

	@if ($is_allow_reset_password > 0)
	<form id="form_reset_staff_password" method="post" action="{{ Config::get("path.ROOT") }}staff/reset_password">
		<input type="hidden" name="record_id" value="{{ $record_id }}" />
	</form>
	@endif

    <div class="panel panel-success result_alert_box" id="update_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Update staff successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="edit_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to edit staff. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="delete_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to delete staff. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>
</div>
@stop
