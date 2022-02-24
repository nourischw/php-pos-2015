function updateStatus( type, status ) {
	$(".update_result").hide();
	var record_id = getValue("record_id");
	var ans = window.confirm("Confirm to " + type + " this Purchase Order?");
	var deliver_by = '';

	if ( !ans ) {
		return false;
	}

	var status_text = '';
	switch(status) {
	case 1:
		status_text = "Confirmed";
		break;
	case 3:
		status_text = "Voided";
		break;
	}

	var data = new Object();
	data.record_id = record_id;
	data.status = status;

	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "purchase_order/update_status",
		async: false,
		data: data
	});

	jqxhr.done(function(result) {
		if ( parseInt(result) === 1 ) {
			$("#status_text").text(status_text);
			$("#void_button, #unvoid_button").toggle();
			$("#update_success_result").show();

			if ( status === 1 ) {
				$("#deliver_by_block").show();
			}

			if ( status === 3 ) {
				$("#deliver_by_block").hide();
			}

			if ( status === 4 ) {
				$("#void_button").remove();
				$("#unvoid_button").remove();
				$("#deliver_by_block").hide();
				$("#deliver_by_row").show();
				$("#deliver_by_text").text(deliver_by);
			}
			
			$("#update_success_result").show();
		} else {
			$("#update_failure_result").show();
		}
	});

	return false;
}

function printRecord() {
	var win = window.open(ROOT + "purchase_order/print/" + getValue("record_id"), '_new');
	win.focus();
}

$(function() {
	$(".update_result").hide();
	$("#print_button").on("click", printRecord);

	$("#void_button").on("click", function() {
		updateStatus('void', 3);
	});

	$("#unvoid_button").on("click", function() {
		updateStatus('unvoid', 1);
	})
});