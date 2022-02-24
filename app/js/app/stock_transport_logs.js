var list_id = "stock_transport_log_list";

$(function() {
	$(".list_page_buttons").hide();
	$("input[name='search_type']").eq(0).prop("checked", true);

	$(".stl_type_button").on("click", function() {
		$(".list_container").hide();
		$("#loading_data_message").show();

		$("#list_no_items_row").hide();
		$(this).find("input[name='search_type']").prop("checked", true);

		updateList(list_id);
		total_list_items = $("#item_list .list_item_row").length;
		console.log(total_list_items);
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
		$(".stl_type_button")
			.removeClass("active")
			.eq(0)
			.addClass("active");
	});
});
