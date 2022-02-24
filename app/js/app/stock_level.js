$(function() {
	$("#list_items").on("click", ".list_item_row", function() {
		$(this).removeClass("list_selected_row");
	});

	$("#list_items").on("dblclick", ".list_item_row", function() {
		var product_id = $(this).data("product_id");
		var product_image = $(this).data("product_image");
		var product_upc = $(this).find(".col_product_upc").text();
		var product_category = $(this).find(".col_category").text();
		var product_name = $(this).find(".col_product_name").text();
		var image_path = (product_image == '') ? "img/noimg.png" : "app/images/product/" + product_image;

		$("#product_image").attr("src", image_path);
		$("#product_upc_text").text(product_upc);
		$("#product_category_text").text(product_category);
		$("#product_name_text").text(product_name);
		$("#product_images_text").text('');

		$("#shop_qty_loading_message").show();
		$("#list_select_first_message").hide();
		$("#shop_qty_list_block").hide();

		var jqxhr = $.ajax({
			type: 'POST',
			url: ROOT + "stock_level/get_product_stock_level",
			async: false,
			data: { product_id: product_id }
		});

		jqxhr.done(function( record_list ) {
			$("#shop_qty_location_list").html(record_list);
			var total_shop_records = parseInt(getValue("total_shop_records"));
			$("#total_shops").text(total_shop_records);

			if ( total_shop_records < 1 ) {
				$("#no_record_message").show();
			} else {
				$("#no_record_message").hide();
			}
			$("#shop_qty_loading_message").hide();
			$("#shop_qty_list_block").show();
		});
	});

	$("#search_shop_id").on("change", function() {
		var disable_checkbox = $(this).val() == "";
		$("#have_qty_item_only").attr("disabled", disable_checkbox).removeAttr("checked");
	});

	$(".list_reset_button").on("click", function() {
		$("#have_qty_item_only").attr("disabled", true).removeAttr("checked");
	});
});
