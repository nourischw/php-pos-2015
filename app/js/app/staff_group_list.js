var list_id = "staff_list";

function removeSingleItem( obj ) {
	var ans = window.confirm("Confirm to delete this Staff group record?");
	if ( !ans ) {
		return false;
	}

	var record_id = obj.closest(".list_item_row").data("record_id");

    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "staff_group/delete",
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
	var ans = window.confirm("Confirm to delete selected Staff group records?");
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
		url: ROOT + "staff/delete",
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

	if ( total_records < 1 ) {
		disable("#list_checkbox_all");
		disable(".list_page_buttons");
	}

	$("#list_items").on("click", ".list_details_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "staff_group/details/" + record_id;
	});

	$("#list_items").on("click", ".list_edit_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "staff_group/edit/" + record_id;
	});

	$("#list_items").on("click", ".list_member_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "staff_group/member/" + record_id;
	});
});