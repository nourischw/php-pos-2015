function removeRecord() {
	var ans = window.confirm("Confirm to delete this staff group?");
	if ( !ans ) {
		return false;
	}

	var record_id = getValue("record_id");
    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "staff_group/delete",
        async: false,
        data: { record_id: record_id }
    });

    jqxhr.done(function( result ) {
    	if ( parseInt(result) === 1 ) {
    		window.location = ROOT + "staff_group/list";
	    } else {
	    	$("#delete_failure_result").show();
	    }
    });

    return false;
}

$(function() {
	edit_type = EDIT_TYPE_NEW;
	var path = "create";
	var message = "create";

	if ( $("#record_id").length > 0 ) {
		edit_type = EDIT_TYPE_UPDATE;
		path = "update";
		message = "update";
	}
	
	$.setRules({
		"name": {
			"required": true
		}
	});

	$(".access_checkbox").each(function() {
		var section = $(this).data("section");
		var is_disabled = ($(this).is(":checked")) ? false : true;
		$("." + section).prop("disabled", is_disabled);
	})

	$(".access_checkbox").on("click", function() {
		var section = $(this).data("section");
		var is_disabled = ($(this).is(":checked")) ? false : true;
		$("." + section).prop("disabled", is_disabled);
	});

	$("#remove_button").on("click", removeRecord);

	$("#submit_button").on("click", function(event) {
		event.preventDefault();
		$("#password_hash").val("");
		var ok = $.checkFields("form_staff_group_edit");
		if (!ok) {
			return false;
		}
	
		var ans = window.confirm("Confirm to " + message + " the staff group?");
		if ( !ans ) {
			return false;
		}

		var jqxhr = $.ajax({
			type: 'POST',
			url: ROOT + "staff_group/" + path,
			async: false,
			data: $("#form_staff_group_edit").serialize()
		});

		jqxhr.done(function( result ) {
			$(".result_alert_box").hide();
			result = JSON.parse(result);
			if ( result.status === "success" ) {
				if ( edit_type === EDIT_TYPE_NEW ) {
	    			var record_id = ( $("#record_id").length ) ? getValue("record_id") : result.staff_id;
		        	window.location = ROOT + "staff_group/edit/" + record_id;
				} else {
					$("#update_success_result").show();
				}
			} else {
				$("#edit_failure_result").show();
			}
		});
	});
});