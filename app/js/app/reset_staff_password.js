$(function() {
	$(".alert").hide();
	
	$.setRules({
		"password": {
			"lengths": 8,
			"format": "password"
		}
	});

	$("#submit_button").on("click", function() {
		$(".alert").hide();
		var ok = $.checkFields("form_reset_staff_password");
		if (!ok) {
			return false;
		}
	
		var jqxhr = $.ajax({
			type: 'POST',
			url: ROOT + "staff/reset_password_process",
			async: false,
			data: { 
				staff_id: getValue("record_id"),
				password: getValue("password")
			}
		});

		jqxhr.done(function(result) {
			console.log(result);
			if ( parseInt(result) > 0 ) {
				$(".alert-success").show();
				$("#form_reset_staff_password")[0].reset();
			} else {
				$(".alert-danger").show();
			}
		});

		return false;
	/*
		encryptPassword("old_password", true);
		encryptPassword("new_password", false);
	*/
	});

	$("#go_back_button").on("click", function() {
		var id = getValue("record_id");
		window.location = ROOT + "staff/edit/" + id;
	});
});