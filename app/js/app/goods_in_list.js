var main_list_id = "goods_in_list";

$(function() {
	total_records = parseInt($("#total_records").text());

	if ( total_records < 1 ) {
		disable("#list_checkbox_all");
		disable(".list_page_buttons");
	}

	$(".list_page_buttons").show();
	$("#list_void_multi_gi_button").show();
	$("input[name='search_status']").prop("checked", true);

	$(".clear_icon").on("click", function() {
		$(this).siblings(".date_field").val("");
	});

	$(".list_reset_button").on("click", function() {
		$(".gi_status_button")
			.removeClass("active")
			.eq(0)
			.addClass("active");
	});

	$("#list_items").on("click", ".list_details_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.location = ROOT + "goods_in/details/" + record_id;
	});

	$("#list_items").on("click", ".list_download_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.open(ROOT + "goods_in/print/" + record_id, 'Download');
	});
});
