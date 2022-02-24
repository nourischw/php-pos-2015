var list_id = "stock_transfer_list";

function removeSingleItem( obj ) {
	var ans = window.confirm("Confirm to delete this Stock Transfer record?");
	if ( !ans ) {
		return false;
	}

	var record_id = obj.closest(".list_item_row").data("record_id");

    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "stock_transfer/delete",
        async: false,
        data: { record_id: record_id }
    });

    jqxhr.done(function( result ) {
    	if ( parseInt(result) === 1 ) {
    		$("#item_" + record_id).remove();
	    }
    });

	countTotalListRows(list_id);
	$("#total_row_items").text(total_list_items);
}

function removeMultiItems() {
	var ans = window.confirm("Confirm to delete selected Stock Transfer records?");
	if ( !ans ) {
		return false;
	}

	var record_ids = [];
	$(".list_selected_row").each(function() {
		var record_id = $(this).data("record_id");
		record_ids.push(record_id);
	});
	record_ids = record_ids.join(",");

	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "stock_transfer/delete",
		async: false,
		data: {
			record_id: record_ids,
		}
	});

	jqxhr.done(function(result) {
		if ( parseInt(result) === 1 ) {
			total_records -= $(".list_selected_row").length;;
			$(".list_selected_row").remove();
		}
	});

	countTotalListRows(list_id);
	return false;
}

function cancelStockTransfer( obj ) {
	$(".result_alert_box").hide();
	var ans = window.confirm("Confirm to cancel this Stock Transfer?");
	if (!ans) {
		return false;
	}
	
	var record_id = obj.closest(".list_item_row").data("record_id");
	
	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "stock_transfer/cancel",
		async: false,
		data: { record_id : record_id }
	});
	
	jqxhr.done(function( result ) {
		if ( parseInt(result) === 1 ) {
			$("#item_" + record_id).remove();
			countTotalListRows(list_id);
			$("#total_row_items").text(total_list_items);
			$("#cancel_success_result").show();
		} else {
			$("#cancel_failure_result").show();
		}
	});
}

$(function() {
	total_list_items = $("#list_items .list_item_row").length;
	total_records = parseInt($("#total_records").text());
	$(".list_delete_button").hide();
	$(".list_checkbox_column").hide();
	
	if ( total_list_items < 1 ) {
		disable(".list_delete_button");
		disable("#list_checkbox_all");
	}

	$(".list_page_buttons").hide();
	$("input[name='search_status']").eq(0).prop("checked", true);

	$(".st_status_button").on("click", function() {
		$(".list_container").hide();
		$("#loading_data_message").show();
		$("#list_checkbox_all").removeAttr("checked");

		$("#list_no_items_row").hide();
		$(this).find("input[name='search_status']").prop("checked", true);
		$(".list_page_buttons").hide();
		$(".list_checkbox_column").hide();
		$(".list_delete_button").hide();

		var status = $(this).data("status");
		switch ( status ) {
		case 1:
			$(".col_date_in").hide();
			break;
			
		case 2:
			$(".list_checkbox_column").show();
			$(".list_delete_button").show();
			$(".col_date_in").hide();
			$(".list_page_buttons").show();
			break;
			
		case 3:
			$(".col_date_in").show();
			$(".list_checkbox_column").hide();
			break;
			
		default:
			break;
		}

		updateList(list_id);
		total_list_items = $("#item_list .list_item_row").length;
		if ( total_list_items === 0 ) {
			disable("#list_checkbox_all");
			$("#list_no_items_row").show();
		}
		$(".list_container").show();
		$("#loading_data_message").hide();
	});

	$(".clear_icon").on("click", function() {
		$(this).siblings(".date_field").val("");
	});

	$(".list_reset_button").on("click", function() {
		$(".st_status_button")
			.removeClass("active")
			.eq(0)
			.addClass("active");
	});

	$("#list_items").on("click", ".list_details_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "stock_transfer/details/" + record_id;
	});
	
	$("#list_items").on("click", ".list_cancel_button", function( event ) {
		event.stopPropagation();
		cancelStockTransfer($(this));
	});
	
	$("#list_items").on("click", ".list_edit_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "stock_transfer/edit/" + record_id;
	});

	$("#list_items").on("click", ".list_download_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.open(ROOT + "stock_transfer/print/" + record_id, 'Download');
	});

	$("#list_items").on("click", ".list_view_log_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.open(ROOT + "stock_transfer/log/" + record_id);
	});

	$(".list_page_buttons").on("click", function( event ) {
		event.stopPropagation();
		var type = $(this).data("type");

		if ( $("#list_items .list_selected_row").length < 1 ) {
			alert("Please select at least one item to " + type);
			return false;
		}

		var ans = window.confirm("Are you sure to " + type + " the selected Stock Transfer Order?");
		if ( !ans ) {
			return false;
		}

		var selected_records = [];
		$("#list_items .list_selected_row").each(function() {
			var record_id = $(this).data("record_id");
			selected_records.push(record_id);
		});

		all_ids = selected_records.join();

		var jqxhr = $.ajax({
			type: 'POST',
			url: ROOT + "stock_transfer/update_status",
			async: false,
			data: {	record_id: all_ids }
		});

		jqxhr.done(function(result) {
			if ( parseInt(result) === 1 ) {
				var all_ids_length = selected_records.length;
				for ( var i = 0; i < all_ids_length; i++ ) {
					$("#item_" + selected_records[i]).remove();
				}
				countTotalListRows(list_id);
			}
		});
	});
});