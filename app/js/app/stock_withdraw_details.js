function showFinishModal() {
	$("#finishStockWithdraw").modal();
}

function confirmWithdraw() {
	var is_valid = true;
	var staff_code = "";
	var is_mark_finished = $("#mark_finished").is(":checked") ? true : false

	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "check_staff_account",
		async: false,
		data: { 
			staff_code: getValue("staff_code"),
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

	var ans = window.confirm("Confirm to finish this Stock Withdraw order?");
	if ( !ans ) {
		return false;
	}

	var data = {
		finished_by : staff_code,
		record_id : getValue("record_id")
	};

	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "stock_withdraw/finish",
		async: false,
		data: data
	});

	jqxhr.done(function(result) {
		$("#finishStockWithdraw").modal('hide');
		var result = $.parseJSON(result);
		if ( result.status == "success" ) {
			var finished_by = staff_code;
			$("#finished_by_text").text(staff_code);
			$("#finished_date_text").text(result.finished_date);
			$("#confirm_button, #delete_button").remove();
			$(".finish_row").removeClass("hide");
			$("#update_success_result").show();
		} else {
			$("#update_failure_result").show();
		}
	});
}

function deleteRecord() {
	var ans = window.confirm("Confirm to delete this Stock Withdraw record?");
	if (!ans) {
		return false;
	}
	
	var data = {
		record_id : getValue("record_id")
	};
	
	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "stock_withdraw/delete",
		async: false,
		data: data
	});
	
	jqxhr.done(function( result ) {
		if ( parseInt(result) === 1 ) {
			window.location = ROOT + "stock_withdraw/edit";
		} else {
			$("#cancel_failure_result").show();
		}
	});
}

$(function() {
	$("#confirm_button").on("click", showFinishModal);
	$("#delete_button").on("click", deleteRecord);
	$("#btn-confirm_withdraw").on("click", confirmWithdraw);
});