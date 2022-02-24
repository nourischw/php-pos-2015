@section('content')
<div id="staff_attendance" class="page_content">
	<div class="form_block">
		<div class="left_column">
			<div id="current_date"></div>
			<div id="clock"></div>
		</div>
			<div class="right_column">
				right
			</div>
	</div>
	<div class="row">
		<form id="form_staff_details" method="post">
			<div class="form_row">
				<label class="form_label">Staff Code:</label>
	        </div>
			<div class="form_row">
				<label class="form_label">Staff Name:</label>
	        </div>
			<div class="form_row">
				<label class="form_label">Shop Code:</label>
			</div>
			<div class="form_row">
				<label class="form_label">Staff Group:</label>
			</div>
			<div class="form_row">
				<label class="form_label">Title:</label>
	        </div>
			<div class="form_row">
				<label class="form_label">Telephone:</label>
			</div>
			<div class="form_row">
				<label class="form_label">Mobile:</label>
			</div>
			<div class="form_row">
				<label class="form_label">Email:</label>
			</div>


			<div class="page_button_row">
	        	<button class="btn btn-default btn-sm page_buttons redirect_button" data-redirect_page="staff/list">Return to List</button>
	        	@if ($is_allow_create)
	        	<button class="btn btn-primary btn-sm redirect_button" id="create_new_record_button" data-redirect_page="staff/edit">Create New Staff</button>
				@endif
			</div>

		</form>
	</div>

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
