var main_list_id = "sales_invoice_list";

function updateSingleSIStatus( record_id, type, status ) {
	var ans = window.confirm("Are you sure to " + type + " this Sales Invoice?");
	if ( !ans ) {
		return false;
	}

	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "sales_invoice/update_si_status",
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

	countTotalListRows(main_list_id);
	return false;
}

function setStatus(status) {
	if(status!= '0'){
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "sales_invoice/set_status",
            async: false,
            data: { status: status }
        });

        jqxhr.done(function(result) {
			$("#get_status").val(result);
        });
	}
}

$(function() {
	var getStatus = ($("#get_status").val())-1;
	getStatus = (getStatus == 0) ? 3 : getStatus;
	$(".sales_status_button").eq(getStatus).click();

	total_list_items = $("#list_items .list_item_row").length;
	total_records = parseInt($("#total_records").text());

	if ( total_list_items === 0 ) {
		disable(".list_page_buttons");
	}

	$(".list_page_buttons").hide();
	$("#list_void_multi_si_button").show();
	$("input[name='search_status']").eq(0).prop("checked", true);

		$(".sales_status_button").on("click", function() {
		$(".list_container").hide();
		$("#loading_data_message").show();

		$(".list_no_items_row").hide();
		$(".list_delete_button").hide();
		$(this).find("input[name='search_status']").prop("checked", true);
		$(".list_page_buttons").hide();
		$(".list_checkbox_column").show();

		var status = $(this).data("status");
		switch ( status ) {
		case 0:
			window.location = ROOT + "sales_invoice/order";
			break;
		case 1:
			setStatus(status);
			$("#list_void_multi_si_button").show();
			break;
		case 2:
			setStatus(status);
			$(".list_delete_button").show();
			$("#list_confirm_multi_po_button").show();
			break;
		case 3:
			setStatus(status);
			$("#list_unvoid_multi_po_button").show();
			break;
		case 4:
			setStatus(status);
			$(".list_checkbox_column").hide();
			break;
		default:
			break;
		}

		updateList(main_list_id);
		total_list_items = $("#item_list .list_item_row").length;
		if ( total_list_items === 0 ) {
			$(".list_no_items_row").show();
		}
		$(".list_container").show();
		$("#loading_data_message").hide();
	});

	$(".clear_icon").on("click", function() {
		$(this).siblings(".date_field").val("");
	});

	$(".list_reset_button").on("click", function() {
		$(".sales_status_button")
			.removeClass("active")
			.eq(3)
			.addClass("active");
	});

	$(".list_clear_button").on("click", function() {
		$(".list_item_row").remove();
	});

	$("#list_items").on("click", ".list_details_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "sales_invoice/details/" + record_id;
	});

	$("#list_items").on("click", ".list_edit_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "sales_invoice/edit/" + record_id;
	});

	$("#list_items").on("click", ".list_confirm_po_button ", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		updateSingleSIStatus(record_id, 'confirm', 1);
	});

	$("#list_items").on("click", ".list_download_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.open(ROOT + "sales_invoice/print/" + record_id, 'Download');
	});

	$("#list_items").on("click", ".list_void_si_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		updateSingleSIStatus(record_id, 'void', 3);
	});
	
	$(".list_page_buttons").on("click", function( event ) {
		event.stopPropagation();

		var type = $(this).data("type");

		if ( $("#list_items .list_selected_row").length < 1 ) {
			alert("Please select at least one item to " + type);
			return false;
		}

		var ans = window.confirm("Are you sure to " + type + " the selected Sales Invoice?");
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
			url: ROOT + "sales_invoice/update_si_status",
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
				countTotalListRows(main_list_id);
			}
		});
	});
});
