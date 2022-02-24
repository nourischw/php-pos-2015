var list_id = "stock_withdraw_list";

$(function() {
	total_list_items = $("#list_items .list_item_row").length;
	total_records = parseInt($("#total_records").text());

	$("input[name='search_status']").eq(0).prop("checked", true);

	$(".status_button").on("click", function() {
		$(".list_container").hide();
		$("#loading_data_message").show();

		$("#list_no_items_row").hide();
		$(this).find("input[name='search_status']").prop("checked", true);
		$(".list_checkbox_column").hide();

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
		$(".status_button")
			.removeClass("active")
			.eq(0)
			.addClass("active");
	});

	$("#list_items").on("click", ".list_details_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "stock_withdraw/details/" + record_id;
	});

	$("#list_items").on("click", ".list_delete_button", function( event ) {
		event.stopPropagation();
		var ans = window.confirm("Confirm to delete this Stock Withdraw record?");
		if ( !ans ) {
			return false;
		}

		var record_id = $(this).closest(".list_item_row").data("record_id");

	    var jqxhr = $.ajax({
	        type: 'POST',
	        url: ROOT + "stock_withdraw/delete",
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
	});
});