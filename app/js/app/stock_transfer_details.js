function resetPage() {
	$("#form_finish_stock_transfer")[0].reset();
}

function showConfirmDeliverModal() {
	$("#confirmDeliverStockTransfer").modal();
}

function selectAvailableToShopOption() {
	$("#new_to_shop option").each(function() {
		if ( !($(this).is(":disabled")) ) {
			$(this).prop("selected", true);
			return false;
		}
	});
}

function confirmDelivery() {
	var is_valid = true;
	var staff_code = "";
	var is_mark_finished = $("#mark_finished").is(":checked") ? true : false

	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "check_staff_account",
		async: false,
		data: { 
			staff_code: getValue("receiver_staff_code"),
			staff_password: getValue("staff_password")
		}
	});

	jqxhr.done(function(return_staff_code) {
		if (return_staff_code == "") {
			alert("Your staff code or password is invalid. Please try again!");
			is_valid = false;
		} else {
			staff_code = return_staff_code;
		}
	});

	if (is_valid !== true) {
		return false;
	}

	var ans = window.confirm("Confirm to receive this Stock Transfer?");
	if ( !ans ) {
		return false;
	}

	var data = {
		deliver_by : getValue("deliver_by"),
		receive_by : staff_code,
		record_id : getValue("record_id"),
		is_mark_finished : (is_mark_finished) ? 1 : 0
	};

	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "stock_transfer/confirm_deliver",
		async: false,
		data: data
	});

	jqxhr.done(function(result) {
		$("#confirmDeliverStockTransfer").modal('hide');
		if ( parseInt(result) === 1 ) {
			var date_in = $("#today").attr("data-value");
			var deliver_by = getValue("deliver_by");
			var receive_by = staff_code;

			$("#date_in_text").text(date_in);
			$("#deliver_by_text").text(deliver_by);
			$("#receive_by_text").text(receive_by);
			$("#delivery_info_block").removeClass("hide");
			$("#reset_all_button").remove();
			
			if (is_mark_finished) {
				$("#confirm_deliver_button, #mark_finish_button, #cancel_button").remove();
			} else {
				$("#confirm_deliver_button, #cancel_button").addClass("hide");
				$("#mark_finish_button, #retransfer_stock_items_block").removeClass("hide");
			}
			
			$("#update_success_result").show();
		} else {
			$("#update_failure_result").show();
		}
	});
}

function retransferItems() {
	var ans = window.confirm("Confirm to re-transfer this Stock Transfer order?");
	if ( !ans ) {
		return false;
	}
	
	var to_shop_id = getValue("new_to_shop");

	var data = {
		record_id : getValue("record_id"),
		to_shop_id : to_shop_id
	};
	
	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "stock_transfer/retransfer",
		async: false,
		data: data
	});

	jqxhr.done(function( result ) {
		if ( parseInt(result) === 1 ) {
			var to_shop_code = $("#to_shop_code").text();
			var new_to_shop_code = $("#new_to_shop option:selected").text();
			selectAvailableToShopOption();
			$("#from_shop_code").text(to_shop_code);
			$("#to_shop_code").text(new_to_shop_code);
			$("#new_to_shop option:disabled").prop("disabled", false);
			$("#new_to_shop option:selected").prop("disabled", true);
			$("#mark_finish_button, #retransfer_stock_items_block, #delivery_info_block").addClass("hide");
			$("#confirm_deliver_button, #cancel_button").removeClass("hide");
			$("#stock_transfer_status").text("Processing");
			$("#retransfer_success_result").show();
		} else {
			$("#retransfer_failure_result").show();
		}
	});
}

function cancelTransfer() {
	var ans = window.confirm("Confirm to cancel this Stock Transfer?");
	if (!ans) {
		return false;
	}
	
	var data = {
		record_id : getValue("record_id")
	};
	
	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "stock_transfer/cancel",
		async: false,
		data: data
	});
	
	jqxhr.done(function( result ) {
		if ( parseInt(result) === 1 ) {
			$("#stock_transfer_status").text("Cancelled");
			$("#confirm_deliver_button, #finish_button, #cancel_button, #print_button").remove();
			$("#cancel_success_result").show();
		} else {
			$("#cancel_failure_result").show();
		}
	});
}

function markFinished() {
	var ans = window.confirm("Confirm to mark this Stock Transfer order as finished?");
	if ( !ans ) {
		return false;
	}
	
	var data = {
		record_id : getValue("record_id")
	};
	
	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "stock_transfer/finish",
		async: false,
		data: data
	});

	jqxhr.done(function( result ) {
		if ( parseInt(result) === 1 ) {
			$("#mark_finished_button, #retransfer_stock_items_block").remove();
			$("#stock_transfer_status").text("Finished");
			$("#finish_success_result").show();
		} else {
			$("#finish_failure_result").show();
		}
	});
}

function printRecord() {
	var record_id = getValue("record_id");
	var url = ROOT + "stock_transfer/print/" + record_id;
	var win = window.open(url, '_new');
	win.focus();
}

function viewLog() {
	var record_id = $("#view_log_button").data("record_id");
	window.open(ROOT + "stock_transfer/log/" + record_id);
}

$(function() {
	$('#date_in').datepicker({
	    dateFormat: 'dd-mm-yy',
	    changeMonth: true,
	    changeYear: true
	}).attr('readonly','readonly');

	$("#print_button").on("click", printRecord);
	$("html #confirm_deliver_button").on("click", showConfirmDeliverModal);
	$("#cancel_button").on("click", cancelTransfer);
	$("#mark_finish_button").on("click", markFinished);
	$("#btn-confirm_delivery").on("click", confirmDelivery);
	$("#confirm_retransfer_button").on("click", retransferItems);
	$("#view_log_button").on("click", viewLog);
});