@section('content')
<div id="reset_staff_password" class="page_content">
	<div class="alert alert-success"><strong>Reset staff password successfully!</strong></div>
	<div class="alert alert-danger"><strong>Failed to reset staff password, please try again!</strong></div>

	<form id="form_reset_staff_password" action="staff/reset_password_process" method="post">
		<div class="form_row">
			<label for="type" class="form_label">Staff Code:</label>
			<span>{{ $staff_code }}</span>
		</div>
		<div class="form_row">
			<label for="type" class="form_label"><sup class="required">*</sup>New Password:</label>
			<input type="password" class="text_field form-control password" id="password" name="password" data-check_field="password" />
			<span class="form-error_message" id="error_password"></span>
		</div>
		<div class="form_row">
			<label for="type" class="form_label"><sup class="required">*</sup>Confirm New Password:</label>
			<input type="password" class="text_field form-control password" id="confirm_password" data-check_field="password" />
			<span class="form-error_message" id="error_confirm_password"></span>
		</div>
		<div class="page_button_row">
			<input type="submit" id="submit_button" class="btn btn-default btn-sm page_buttons" value="Confirm" />
			@if ($from_list == false)
			<input type="button" id="go_back_button" class="btn btn-default btn-sm page_buttons" value="Go Back" />
			@endif
			<input type="button" class="btn btn-default btn-sm redirect_button page_buttons" data-redirect_page="staff/list" value="Return to list" />
		</div>
		<!--
		<input type="hidden" name="new_password" id="password_hash" value="" />
		-->
		<input type="hidden" id="record_id" name="record_id" value="{{ $record_id }}" />
	</form>
</div>
@stop
