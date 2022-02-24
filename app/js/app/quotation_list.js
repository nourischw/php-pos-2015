var list_id = "quotation_list";

function updateSingleQuotationStatus( record_id, type, status ) {
	var ans = window.confirm("Are you sure to " + type + " this Quotation?");
	if ( !ans ) {
		return false;
	}

	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "quotation/update_status",
		async: false,
		data: {
			record_id: record_id,
			status: status
		}
	});

	jqxhr.done(function(result) {
		if ( parseInt(result) == 1 ) {
			$("#item_" + record_id).remove();
		}
	});

	countTotalListRows(list_id);
	return false;
}

function removeSingleItem( obj ) {
	var ans = window.confirm("Confirm to delete this Quotation record?");
	if ( !ans ) {
		return false;
	}

	var record_id = obj.closest(".list_item_row").data("record_id");

    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "quotation/delete",
        async: false,
        data: { record_id: record_id }
    });

	jqxhr.done(function(result) {
		if ( parseInt(result) === 1 ) {
			$("#item_" + record_id).remove();
			total_records--;
		}
	});

	countTotalListRows(list_id);
	return false;
}

function removeMultiItems() {
	var ans = window.confirm("Confirm to delete selected Quotation records?");
	if ( !ans ) {
		return false;
	}

	var record_ids = [];
	var total_removed_rows = 0;
	$(".list_selected_row").each(function() {
		var record_id = $(this).data("record_id");
		record_ids.push(record_id);
		total_removed_rows++;
	});
	record_ids = record_ids.join(",");

	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "quotation/delete",
		async: false,
		data: { record_id: record_ids }
	});

	jqxhr.done(function(result) {
		if ( parseInt(result) === 1 ) {
			$(".list_selected_row").remove();
			total_records -= total_removed_rows;
		}
	});

	countTotalListRows(list_id);
	return false;
}

$(function() {
	total_list_items = $("#list_items .list_item_row").length;
	total_records = parseInt($("#total_records").text());
	$("#list_void_multi_quotation_button").show();
	$("input[name='search_status']").eq(0).prop("checked", true);
	$(".list_delete_button").show();

	if ( total_records < 1 ) {
		disable("#list_checkbox_all");
		disable(".list_page_buttons");
		disable("#list_void_multi_quotation_button");
	}

	$(".quotation_status_button").on("click", function() {
		$(".list_container").hide();
		$("#loading_data_message").show();

		$("#list_checkbox_all").removeAttr("checked");
		$("#list_checkbox_all").removeAttr("disabled");
		$("#list_no_items_row").hide();
		$(this).find("input[name='search_status']").prop("checked", true);
		$(".list_page_buttons").hide();
		$(".list_checkbox_column").show();
		$(".list_delete_button").show();

		var status = $(this).data("status");
		switch ( status ) {
		case 0:
			$("#list_void_multi_quotation_button").show();
			break;
		case 1:
			$("#list_unvoid_multi_quotation_button").show();
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


	$(".list_reset_button").on("click", function() {
		$(".quotation_status_button")
			.removeClass("active")
			.eq(0)
			.addClass("active");
	});

	$(".clear_icon").on("click", function() {
		$(this).siblings(".date_field").val("");
	});

	$("#list_items").on("click", ".list_details_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "quotation/details/" + record_id;
	});

	$("#list_items").on("click", ".list_edit_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "quotation/edit/" + record_id;
	});

	$("#list_items").on("click", ".list_void_quotation_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		updateSingleQuotationStatus(record_id, 'void', 1);
	});

	$("#list_items").on("click", ".list_unvoid_quotation_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		updateSingleQuotationStatus(record_id, 'unvoid', 0);
	});

	$("#list_items").on("click", ".list_download_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.open(ROOT + "quotation/print/" + record_id, 'Download');
	});
});