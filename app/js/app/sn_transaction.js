var resetSNForm = function() {
	$("#shop_code, #product_barcode, #product_name, #serial_number").text("---");
	$("#new_serial_number, #stock_id").val("");
	disable("#new_serial_number");
	disable("#update_sn_button");	
}

$(function() {
	resetSNForm();
	$(".result_alert_box").hide();
	total_list_items = $("#list_items .list_item_row").length;
	total_records = parseInt($("#total_records").text());

	$.setRules({
		'new_serial_number': {
			'required': true
		}
	});

	$("#page_bar").on("click", "li", function() {
		if ($(this).hasClass("disabled")) {
			return false;
		}
		resetSNForm();
	});

	$("#list_items").on("click", ".list_item_row", function( event ) {
		event.stopPropagation();
		$(this).removeClass("list_selected_row");
		var stock_id = $(this).data("record_id")
		var shop_code = $(this).find(".col_shop_code").text();
		var product_barcode = $(this).find(".col_product_barcode").text();
		var product_name = $(this).find(".col_product_name").text();
		var serial_number = $(this).find(".col_serial_number").text();

		$("#stock_id").val(stock_id);
		$("#shop_code").text(shop_code);
		$("#product_barcode").text(product_barcode);
		$("#product_name").text(product_name)
		$("#serial_number").text(serial_number);
		enable("#new_serial_number");
		enable("#update_sn_button");
	});

	$("#update_sn_button").on("click", function() {
		$(".result_alert_box").hide();
		var ok = $.checkFields("form_update_sn");
		if ( !ok ) {
			return false;
		}

		var ans = window.confirm("Confirm to update this stock's Serial Number?");
		if ( !ans ) {
			return false;
		}

		formValue = new Object();
		var stock_id = getValue("stock_id");
		var new_serial_number = getValue("new_serial_number");
		formValue.stock_id = stock_id;
		formValue.new_serial_number = new_serial_number;

		var jqxhr = $.ajax({
			type: "POST",
			url: ROOT + '/sn_transaction/update_serial_number',
			async: false,
			data: formValue
		});

		jqxhr.done(function( result ) {
			if ( parseInt(result) === 1 ) {
				$("#item_" + stock_id).find(".col_serial_number").text(new_serial_number);
				$("#update_success_result").show();
				resetSNForm();
			} else {
				$("#update_failure_result").show();
			}
		})
	});
});