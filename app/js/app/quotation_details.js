function updateStatus( type, status ) {
	$(".update_result").hide();

	var ans = window.confirm("Confirm to " + type + " this Quotation?");
	if ( !ans ) {
		return false;
	}

	var status_text = '';
	switch(status) {
	case 0:
		status_text = "Normal";
		break;
	case 1:
		status_text = "Voided";
		break;
	}

	var data = new Object();
	data.record_id = getValue("record_id");
	data.status = status;

	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "quotation/update_status",
		async: false,
		data: data
	});

	jqxhr.done(function(result) {
		if ( parseInt(result) === 1 ) {
			$("#status_text").text(status_text);
			$("#void_button, #unvoid_button").toggle();
			$("#update_success_result").show();
		} else {
			$("#update_failure_result").show();
		}
	});

	return false;
}

function deleteRecord() {
	var ans = window.confirm("Confirm to delete this Quotation record?");
	if ( !ans ) {
		return false;
	}

	var record_id = getValue("record_id");
    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "quotation/delete",
        async: false,
        data: { record_id: record_id }
    });

    jqxhr.done(function( result ) {
    	if ( parseInt(result) === 1 ) {
    		window.location = ROOT + "quotation/list";
	    } else {
	    	$("#delete_failure_result").show();
	    }
    });
}

function printRecord() {
	var win = window.open(ROOT + "quotation/print/" + getValue("record_id"), '_new');
	win.focus();
}

$(function() {
	$(".update_result").hide();

	$("#print_button").on("click", printRecord);
	$("#remove_button").on("click", deleteRecord);

	$("#void_button").on("click", function() {
		updateStatus('void', 1);
	});

	$("#unvoid_button").on("click", function() {
		updateStatus('unvoid', 0);
	})
});