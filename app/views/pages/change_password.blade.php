@section('content')
<div id="change_password" class="page_content">
	<?php
		if (Session::has("change_password_result")) {
			$result = Session::pull("change_password_result");
			if ($result) {
	?>
	<div class="alert alert-success">
		<strong>Your password is changed successfully!</strong>
	</div>
	<?php } else { ?>
	<div class="alert alert-danger">
		<strong>Failed to change your password, Please try again later!</strong>
	</div>
	<?php } } ?>

	<form id="form_change_password" action="change_password/process" method="post">
		<div class="form_row">
			<label for="type" class="form_label"><sup class="required">*</sup>Old Password:</label>
			<input type="password" class="text_field form-control validateItem" id="old_password" name="old_password" />
			<span class="form-error_message" id="error_old_password"></span>
		</div>
		<div class="form_row">
			<label for="new_password" class="form_label"><sup class="required">*</sup>New Password:</label>
			<input type="password" class="text_field form-control validateItem" id="new_password" name="new_password" />
			<span class="form-error_message" id="error_new_password"></span>
		</div>
		<div class="form_row">
			<label for="confirm_new_password" class="form_label"><sup class="required">*</sup>Confirm New Password:</label>
			<input type="password" class="text_field form-control" id="confirm_new_password" />
			<span class="form-error_message" id="error_confirm_new_password"></span>
		</div>
		<div class="page_button_row">
			<input type="submit" id="submit_button" class="btn btn-default btn-sm page_buttons" value="Confirm" />
		</div>
	</form>
</div>
@stop
