function deleteRecord() {
	var ans = window.confirm("Confirm to delete this staff?");
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

    return false;
}

function resetStaffPassword() {
	$("#form_reset_staff_password").submit();
}

$(function() {
	edit_type = EDIT_TYPE_NEW;
	var path = "create";
	var message = "create";
	
	$("#password, #confirm_password").val('');

	if ( $("#record_id").length > 0 ) {
		edit_type = EDIT_TYPE_UPDATE;
		path = "update";
		message = "update";
	}
	
	$.setRules({
		"staff_code": {
			"required": true,
		},
		"name": {
			"required": true
		},
		"shop_id": {
			"required": true
		},
		"staff_group": {
			"required": true,
			"format": "number"
		},
		"telephone": {
			"format": "number"
		},
		"mobile": {
			"format": "number"
		},
		"email": {
			"format": "email"
		},
		"password": {
			"lengths": 8,
			"format": "password"
		}
	});

	$("#submit_button").on("click", function(event) {
		event.preventDefault();
		$("#password_hash").val("");
		var ok = $.checkFields("form_staff_edit");
		
		if ( staff_code !== '' ) {
			var record_id = ( $("#record_id").length > 0 ) ? getValue("record_id") : 0;
			var jqxhr = $.ajax({
				type: "POST",
				url: ROOT + "staff/check_staff_code",
				async: false,
				data: { 
					staff_code: getValue("staff_code"),
					staff_id: record_id
				}
			});

			jqxhr.done(function( is_used ) {
				if ( parseInt(is_used) > 0 ) {
					$("#error_staff_code").text("This staff code is used").show();
					ok = false;
				}
			});
		}

		if (!ok) {
			return false;
		}
		
		//encryptPassword("password");
	
		var ans = window.confirm("Confirm to " + message + " the staff?");
		if ( !ans ) {
			return false;
		}

		var jqxhr = $.ajax({
			type: 'POST',
			url: ROOT + "staff/" + path,
			async: false,
			data: $("#form_staff_edit").serialize()
		});

		jqxhr.done(function( result ) {
			$(".result_alert_box").hide();
			result = JSON.parse(result);
			if ( result.status === "success" ) {
				if ( edit_type === EDIT_TYPE_NEW ) {
	    			var record_id = ( $("#record_id").length ) ? getValue("record_id") : result.staff_id;
		        	window.location = ROOT + "staff/edit/" + record_id;
				} else {
					$("#update_success_result").show();
				}
			} else {
				$("#edit_failure_result").show();
			}
		});
	});

	$("#reset_staff_password_button").on("click", resetStaffPassword);

	$("#remove_button").on("click", deleteRecord);
});