function resetStaffPassword() {
	$("#form_reset_staff_password").submit();
}

function deleteRecord() {
	var ans = window.confirm("Confirm to delete this Staff record?");
	if ( !ans ) {
		return false;
	}

	var record_id = getValue("record_id");
    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "staff/delete",
        async: false,
        data: { record_id: record_id }
    });

    jqxhr.done(function( result ) {
    	if ( parseInt(result) === 1 ) {
    		window.location = ROOT + "staff/list";
	    } else {
	    	$("#delete_failure_result").show();
	    }
    });
}

$(function() {
	$(".update_result").hide();
	$("#remove_button").on("click", deleteRecord);
	$("#reset_staff_password_button").on("click", resetStaffPassword);
});