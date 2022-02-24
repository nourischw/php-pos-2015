@section('content')
<div id="staff_edit" class="page_content">
	<div class="row">
		<form id="form_staff_edit" method="post">
			<div class="form_row">
				<h1>{{ ($record_id === 0) ? 'Create' : 'Update' }} Staff</h1>
	        </div>

			<div class="form_row">
				<label for="staff_code" class="form_label"><sup class="required">*</sup>Staff Code:</label>
	            <input type="text" class="text_field form-control validateItem" id="staff_code" name="staff_code" maxlength="20" value="{{ $st_staff_code }}" />
	            <span class="form-error_message" id="error_staff_code"></span>
	        </div>
			<div class="form_row">
				<label for="staff_code" class="form_label"><sup class="required">*</sup>Staff Name:</label>
	            <input type="text" class="text_field form-control validateItem" id="name" name="name" maxlength="50" value="{{ $st_name }}" />
	            <span class="form-error_message" id="error_name"></span>
	        </div>
			<div class="form_row">
				<label for="shop_id" class="form_label"><sup class="required">*</sup>Shop:</label>
				<select class="text_field form-control validateItem" id="shop_id" name="shop_id">
					@if ($record_id === 0)
					<option value="" disabled {{ ($st_shop_id === 0) ? 'selected' : null }}>Please select</option>
					@endif
					@foreach ($shop_list as $shop)
						<?php extract($shop, EXTR_PREFIX_ALL, 'shop'); ?>
					<option value="{{ $shop_id }}" {{ ($shop_id === $st_shop_id) ? 'selected' : null }}>{{ $shop_code }}</option>
					@endforeach
				</select>
				<span class="form-error_message" id="error_shop_id"></span>
			</div>
			<div class="form_row">
				<label for="type" class="form_label"><sup class="required">*</sup>Staff Group:</label>
				<select class="text_field form-control validateItem" id="staff_group" name="staff_group">
					@foreach ($staff_group_list as $value)
						<?php extract($value, EXTR_PREFIX_ALL, 'group') ?>
						<option value="{{ $group_id }}" {{ ($group_id === $st_staff_group) ? 'selected' : null }}>{{ $group_name }}</option>
					@endforeach
				</select>
				<span class="form-error_message" id="error_type"></span>
			</div>
			<div class="form_row">
				<label for="type" class="form_label">Title:</label>
	            <input type="text" class="text_field form-control" name="title" id="title" value="{{ $st_title }}" />
	        </div>
			<div class="form_row">
				<label for="type" class="form_label">Telephone:</label>
				<input type="text" class="text_field form-control num_item validateItem" name="telephone" id="telephone" value="{{ $st_telephone }}" />
				<span class="form-error_message" id="error_telephone"></span>
			</div>
			<div class="form_row">
				<label for="type" class="form_label">Mobile:</label>
				<input type="text" class="text_field form-control num_item validateItem" name="mobile" id="mobile" value="{{ $st_mobile }}" />
				<span class="form-error_message" id="error_mobile"></span>
			</div>
			<div class="form_row">
				<label for="type" class="form_label">Email:</label>
				<input type="email" class="text_field form-control validateItem" name="email" id="email" value="{{ $st_email }}" />
				<span class="form-error_message" id="error_email"></span>
			</div>

			@if ($record_id === 0)
			<div class="form_row">
				<label for="type" class="form_label"><sup class="required">*</sup>Password:</label>
				<input type="password" class="text_field form-control password" id="password" name="password" value="" data-check_field="password" />
				<span class="form-error_message" id="error_password"></span>
			</div>
			<div class="form_row">
				<label for="type" class="form_label"><sup class="required">*</sup>Confirm Password:</label>
				<input type="password" class="text_field form-control password" id="confirm_password" data-check_field="password" />
				<span class="form-error_message" id="error_confirm_password"></span>
			</div>
			<!--
			<input type="hidden" name="password" id="password_hash" value="" />
		-->
			@elseif ($is_allow_reset_password)
			<div class="form_row">
				<input type="button" id="reset_staff_password_button" class="btn btn-default btn-sm" style="margin-left: 190px;" value="Reset Password" />
			</div>
			@endif

			<div class="page_button_row">
				@if ($record_id === 0 && $is_allow_create || $record_id > 0 && $is_allow_update)
				<input type="button" id="submit_button" class="btn btn-default btn-sm page_buttons" value="Confirm" />
				<input type="reset" class="btn btn-default btn-sm page_buttons" value="Reset All" />
				@endif
				@if ($is_allow_delete)
	       		<button id="remove_button" class="btn btn-default btn-sm page_buttons" {{ ($record_id > 0) ? null : 'style="display: none"' }}>Delete</button>
	        	@endif
	        	<button class="btn btn-default btn-sm page_buttons redirect_button" data-redirect_page="staff/list">Return to List</button>
			</div>

		    @if ($record_id > 0)
		    <input type="hidden" name="record_id" id="record_id" value="{{ $record_id }}" />
		    @endif
		</form>
	</div>

	@if ($record_id > 0)
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
