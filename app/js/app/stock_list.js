var list_id = "stock_list";

$(function() {
	total_list_items = $("#list_items .list_item_row").length;
	total_records = parseInt($("#total_records").text());
	$(".list_page_buttons").hide();

	$("#list_items").on("click", ".list_view_log_button", function( event ) {
		event.stopPropagation();
		var record_id = $(this).closest(".list_item_row").data("record_id");
		window.open(ROOT + "stock_transport_log/" + record_id);
	});
});