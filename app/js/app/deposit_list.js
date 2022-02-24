var list_id = "deposit_list";

function updateSingleStatus( record_id, type, status ) {
	var ans = window.confirm("Are you sure to " + type + " this Deposit record?");
	if ( !ans ) {
		return false;
	}

	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "deposit/update_status",
		async: false,
		data: {
			record_id: record_id,
			status: status
		}
	});

	jqxhr.done(function(result) {
		if ( parseInt(result) === 1 ) {
			$("#item_" + record_id).remove();
		}
	});

	countTotalListRows(list_id);
	return false;
}

function removeSingleItem( obj ) {
	var ans = window.confirm("Confirm to delete this Deposit record?");
	if ( !ans ) {
		return false;
	}

	var record_id = obj.closest(".list_item_row").data("record_id");
    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "deposit/delete",
        async: false,
        data: { record_id: record_id }
    });

	jqxhr.done(function(result) {
		if ( parseInt(result) === 1 ) {
			$("#item_" + record_id).remove();
		}
	});

	countTotalListRows(list_id);
	return false;
}

function removeMultiItems() {
	var ans = window.confirm("Confirm to delete selected Deposit records?");
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
		url: ROOT + "deposit/delete",
		async: false,
		data: { record_id: record_ids }
	});

	jqxhr.done(function(result) {
		if ( parseInt(result) === 1 ) {
			$(".list_selected_row").remove();
		}
	});

	countTotalListRows(list_id);
	return false;
}

$(function() {
	total_list_items = $("#list_items .list_item_row").length;
	total_records = parseInt($("#total_records").text());

	if ( total_records < 1 ) {
		disable("#list_checkbox_all");
		disable(".list_page_buttons");
	}

	$(".list_page_buttons").hide();
	$("input[name='search_status']").eq(0).prop("checked", true);
	$("#list_void_multi_button").show();

	$(".de_status_button").on("click", function() {
		$(".list_container").hide();
		$("#loading_data_message").show();

		$("#list_checkbox_all").removeAttr("checked");
		$("#list_checkbox_all").removeAttr("disabled");
		$("#list_no_items_row").hide();
		$(this).find("input[name='search_status']").prop("checked", true);
		$(".list_page_buttons").hide();
		$(".list_checkbox_column").show();

		var status = $(this).data("status");

		switch ( status ) {
		case 0:
			$("#list_void_multi_button").show();
			break;
		case 1:
			$("#list_unvoid_multi_button").show();
			break;
		default:
			break;
		}

		updateList(list_id);
		total_list_items = $("#item_list .list_item_row").length;
		if ( total_list_items < 1 ) {
			disable("#list_checkbox_all");
			$("#list_no_items_row").show();
		}
		$(".list_container").show();
		$("#loading_data_message").hide();
	});

	if ( total_list_items === 0 ) {
		disable(".list_page_buttons");
	}

	$(".clear_icon").on("click", function() {
		$(this).siblings(".date_field").val("");
	});

	$(".list_reset_button").on("click", function() {
		$(".de_status_button")
			.removeClass("active")
			.eq(0)
			.addClass("active");
	});
	
	$("#list_items").on("click", ".list_details_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "deposit/details/" + record_id;
	});

	$("#list_items").on("click", ".list_edit_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "deposit/edit/" + record_id;
	});

	$("#list_items").on("click", ".list_download_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.open(ROOT + "deposit/print/" + record_id, 'Download');
	});

	$("#list_items").on("click", ".list_void_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		updateSingleStatus(record_id, 'void', 1);
	});

	$("#list_items").on("click", ".list_unvoid_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		updateSingleStatus(record_id, 'unvoid', 0);
	});

	$(".list_page_buttons").on("click", function( event ) {
		event.stopPropagation();
		var type = $(this).data("type");
		if ( $("#list_items .list_selected_row").length < 1 ) {
			alert("Please select at least one item to " + type);
			return false;
		}

		var ans = window.confirm("Are you sure to " + type + " the selected Deposit record?");
		if ( !ans ) {
			return false;
		}

		var status = ( type === "void" ) ? 1 : 0;
		var selected_records = [];
		$("#list_items .list_selected_row").each(function() {
			var record_id = $(this).data("record_id");
			selected_records.push(record_id);
		});

		all_ids = selected_records.join();

		var jqxhr = $.ajax({
			type: 'POST',
			url: ROOT + "deposit/update_status",
			async: false,
			data: {
				record_id: all_ids,
				status: status
			}
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
