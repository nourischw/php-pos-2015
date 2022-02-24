var list_id = "purchase_order_list";

function updateSinglePOStatus( record_id, type, status ) {
	var ans = window.confirm("Are you sure to " + type + " this Purchase Order?");
	if ( !ans ) {
		return false;
	}

	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "purchase_order/update_status",
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
	var ans = window.confirm("Confirm to delete this Purchase Order record?");
	if ( !ans ) {
		return false;
	}

	var record_id = obj.closest(".list_item_row").data("record_id");

    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "purchase_order/delete",
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
	var ans = window.confirm("Confirm to delete selected Purchase Order records?");
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
		url: ROOT + "purchase_order/delete",
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

$(function() {
	total_list_items = $("#list_items .list_item_row").length;
	total_records = parseInt($("#total_records").text());

	if ( total_records < 1 ) {
		disable("#list_checkbox_all");
		disable(".list_page_buttons");
	}

	$(".list_page_buttons").hide();
	$("#list_void_multi_po_button").show();
	$("input[name='search_status']").eq(0).prop("checked", true);

	$(".po_status_button").on("click", function() {
		$(".list_container").hide();
		$("#loading_data_message").show();

		$("#list_checkbox_all").removeAttr("checked");
		$("#list_checkbox_all").removeAttr("disabled");
		$("#list_no_items_row").hide();
		$(".list_delete_button").hide();
		$(this).find("input[name='search_status']").prop("checked", true);
		$(".list_page_buttons").hide();
		$(".list_checkbox_column").show();

		var status = $(this).data("status");
		switch ( status ) {
		case 1:
			$("#list_void_multi_po_button").show();
			break;
		case 2:
			$(".list_delete_button").show();
			$("#list_confirm_multi_po_button").show();
			break;
		case 3:
			$("#list_unvoid_multi_po_button").show();
			break;
		case 4:
			$(".list_checkbox_column").hide();
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

	$(".clear_icon").on("click", function() {
		$(this).siblings(".date_field").val("");
	});

	$(".list_reset_button").on("click", function() {
		$(".po_status_button")
			.removeClass("active")
			.eq(0)
			.addClass("active");
	});

	$("#list_items").on("click", ".list_details_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "purchase_order/details/" + record_id;
	});

	$("#list_items").on("click", ".list_edit_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "purchase_order/edit/" + record_id;
	});

	$("#list_items").on("click", ".list_download_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.open(ROOT + "purchase_order/print/" + record_id, 'Download');
	});

	$("#list_items").on("click", ".list_void_po_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		updateSinglePOStatus(record_id, 'void', 3);
	});

	$("#list_items").on("click", ".list_unvoid_po_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		updateSinglePOStatus(record_id, 'unvoid', 1);
	});

	$("#list_items").on("click", ".list_confirm_po_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		updateSinglePOStatus(record_id, 'confirm', 1);
	});

	$(".list_page_buttons").on("click", function( event ) {
		event.stopPropagation();
		var type = $(this).data("type");
		if ( $("#list_items .list_selected_row").length < 1 ) {
			alert("Please select at least one item to " + type);
			return false;
		}

		var ans = window.confirm("Are you sure to " + type + " the selected Purhcase Order?");
		if ( !ans ) {
			return false;
		}

		var status = ( type === "void" ) ? 3 : 1;
		var selected_records = [];
		$("#list_items .list_selected_row").each(function() {
			var record_id = $(this).data("record_id");
			selected_records.push(record_id);
		});

		all_ids = selected_records.join();

		var jqxhr = $.ajax({
			type: 'POST',
			url: ROOT + "purchase_order/update_status",
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
