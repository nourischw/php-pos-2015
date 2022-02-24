var total_amount = 0.00;
var payment_amount = 0.00;
var sub_total_amount = 0.00;

var removedItems = [];
var edit_type = 0;

function resetPage() {
	if ( edit_type === EDIT_TYPE_UPDATE ) {
		var record_id = getValue("record_id");
		window.location = ROOT + "deposit/edit/" + record_id;
		return false;
	}

	total_amount = 0.00;
	removedItems = [];
	$(".form-error_message").empty();
	$("#form_deposit_edit")[0].reset();
	$("#form_product_popup_list")[0].reset();
	$("#form_quotation_popup_list")[0].reset();
	$("#comment").val("");
	resetAddItemFields();
	resetList();
}

function calculateTotalAmount() {
	total_amount = 0.00;
	var total_qty = 0;

	$("#list_items .list_item_row").each(function() {
		total_qty += parseInt($(this).find(".list_product_qty").val());
		total_amount += parseFloat($(this).find(".col_item_total").text());
	});

	total_amount = setFloat(total_amount, 2);
	$("#total_qty").text(total_qty);
	$("#total_amount_value").text(total_amount);
	calculateSubTotalAmount();
}

function calculateSubTotalAmount() {
	sub_total_amount = setFloat((total_amount - payment_amount), 2);
	if ( sub_total_amount < 0.00 ) {
		$("#sub_total_error_message").show();
	} else {
		$("#sub_total_error_message").hide();
	}
	$("#sub_total_amount_value").text(sub_total_amount);
}

function getProductInfo() {
	$("#error_product_upc").text("");

	var product_upc = getValue("product_upc");
	if ( product_upc === '' ) {
		$("#error_product_upc").text("Required");
		return false;
	}

	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "get_product_info",
		async: false,
		data: { product_upc: product_upc }
	});

	jqxhr.done(function(result) {
		if ( result === "" ) {
			$("#product_name_text").text("---");
			$("#error_product_upc").text("Product not found!");
		} else {
			$("#product_name_text").text(result.name);
		}
	});
}

function resetList() {
	total_list_items = 0;
	total_amount = 0.00;
	payment_amount = 0.00;
	sub_total_amount = 0.00;
	$("#list_checkbox_all").prop("checked", false);
	$("#no_list_item_error_message").hide();
	$("#list_no_items_row").show();

	disable("#list_checkbox_all");
	disable("#list_delete_multi_item_button");
	disable("#discount_amount");
	$("#total_amount_value").text("0.00");
	$("#payment_amount").val("0.00");
	$("#sub_total_amount_value").text("0.00");
	$("#list_items").empty();
	$("#total_row_items").text(0);
	$("#total_qty").text(0);
	$("#sub_total_error_message").hide();
	$("#quotation_number_text").text("---");
	$("#quotation_number").val("");
}

function resetAddItemFields() {
	$("#form_add_product_item")[0].reset();
}

function removeSingleItem( obj ) {
	if ( edit_type === EDIT_TYPE_UPDATE ) {
		var record_id = obj.closest(".list_item_row").data("record_id");
		removedItems.push(record_id);
	}

	obj.closest(".list_item_row").remove();
	calculateTotalAmount();
	total_list_items--;
	if ( total_list_items < 1 ) {
		resetList();
	}
	$("#total_row_items").text(total_list_items);
}

function removeMultiItems() {
	if( edit_type === EDIT_TYPE_UPDATE ) {
		$(".list_selected_row").each(function() {
			var record_id = $(this).closest(".list_item_row").data("record_id");
			removedItems.push(record_id);
		});
	}

	$(".list_selected_row").remove();
	calculateTotalAmount();
	total_list_items = $(".list_item_row").length;
	if ( total_list_items < 1 ) {
		resetList();
	}
	$("#total_row_items").text(total_list_items);
}

function printRecord() {
	var win = window.open(ROOT + "deposit/print/" + getValue("record_id"), '_new');
	win.focus();
}

function deleteRecord() {
	var ans = window.confirm("Confirm to delete this Deposit record?");
	if ( !ans ) {
		return false;
	}

	var record_id = getValue("record_id");
    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "deposit/delete",
        async: false,
        data: { record_id: record_id }
    });

    jqxhr.done(function( result ) {
    	if ( parseInt(result) === 1 ) {
    		window.location = ROOT + "deposit/list";
	    } else {
	    	$("#delete_failure_result").show();
	    }
    });
}

function showQuotationPopupList() {
	$("#quotation_popup_list").modal();
}

$(function() {
	edit_type = EDIT_TYPE_NEW;
	total_list_items = parseInt($("#total_row_items").text());

	if ( $("#record_id").length > 0 ) {
		edit_type = EDIT_TYPE_UPDATE;
		total_amount = parseFloat($("#total_amount_value").text());
		payment_amount = setFloat(getValue("payment_amount"), 2);
		if ( total_list_items > 0 ) {
			enable("#list_checkbox_all");
			enable("#list_delete_multi_item_button");
		}
	}

	if ( edit_type === EDIT_TYPE_NEW ) {
		resetPage();
	}

	updateList("product_popup_list");
	updateList("quotation_popup_list");
	$(".no_record_found_message").hide();
	$("#show_product_popup_list_button").on("click", function() { console.log(1); showProductPopupList(); });

	$("#product_qty").on("blur", function() {
		if ( getValue("product_upc") !== '' ) {
			var product_qty = $(this).val();
			if ( product_qty === '' || parseInt(product_qty) <= 0 ) {
				$(this).val(1);
			}
		}
	});

	$("#list_items").on("click", ".list_product_qty, .list_product_unit_price", function( event ) {
		event.stopPropagation();
	});

	$("#list_items").on("change", ".list_product_qty, .list_product_unit_price", function() {
		var index = $(this).closest(".list_item_row").index(".list_item_row");
		var qty = float2int($(".list_product_qty").eq(index).val());
		qty = Math.max(1, qty);
		var unit_price = setFloat($(".list_product_unit_price").eq(index).val(), 2);
		var item_total = qty * unit_price;
		item_total = item_total.toFixed(2);
		$(".list_product_qty").eq(index).val(qty);
		$(".list_product_unit_price").eq(index).val(unit_price);
		$(".list_item_total").eq(index).text(item_total);
		calculateTotalAmount();
	});

	$("#product_name").on("keyup", function( event ) {
		var keycode = ( event.keyCode ? event.keyCode : event.which );
		if ( keycode == 13 ) {
			var search_product_name = getValue("product_name");
			$("#popup_search_product_name").val(search_product_name);
			updateList("product_popup_list");
			$("#product_popup_list").modal();
		}
	});

	$("#get_product_info_button").on("click", function( event ) {
		event.preventDefault();
		getProductInfo();
	});

	$("#product_popup_list").on("dblclick", ".popup_list_item_row", function() {
		$("#product_upc").val($(this).find(".col_upc").text());
		$("#product_name_text").text($(this).find(".col_name").text());
		$("#product_unit_price").val($(this).find(".col_unit_price").text());
		$("#error_product_upc").empty();
		$("#product_popup_list").modal("hide");
		$("#product_qty").val(1);
	});

	$("#quotation_popup_list").on("dblclick", ".popup_list_item_row", function() {
		var quotation_id = $(this).data("quotation_id");
		var quotation_number = $(this).find(".col_quotation_number").text();
		var shop_code = $(this).find(".col_shop_code").text();

		var jqxhr = $.ajax({
			type: "POST",
			url: ROOT + "quotation/get_quotation_deposit_items",
			async: false,
			data: {
				shop_code: shop_code,
				quotation_id: quotation_id
			}
		});

		jqxhr.done(function( quotation_items ) {
			var len = quotation_items.length;
			var item_row = "";
			if (len > 0) {
				for (var i = 0; i < len; i++) {
					var item = quotation_items[i];
					var index = i + 1;
					item_row += ' \
		<div class="list_item_row quotation_item new_item_row" data-type="new" data-new_row_index="' + index + '" data-product_id="' + item.product_id + '"> \
			<span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span> \
            <span class="list_column col_product_upc">' + item.barcode + '</span> \
            <span class="list_column col_product_name">' + item.product_name + '</span> \
            <span class="list_column col_qty"><input type="text" class="form-control list_text_field list_product_qty num_item" value="' + item.qty + '"></span> \
            <span class="list_column col_unit_price"><input type="text" class="noProp form-control list_text_field list_product_unit_price num_item" value="' + item.unit_price + '"></span> \
            <span class="list_column col_item_total list_item_total">' + item.total_price + '</span> \
            <span class="list_column col_remove_button"><span class="list_delete_single_item_button glyphicon glyphicon-remove"></span></span> \
        </div>';
				}

				$("#quotation_number_text").text(quotation_number);
				$("#quotation_number").val(quotation_number);
				$("#list_no_items_row").hide();
				$(".quotation_item").remove();
				$("#list_items").prepend(item_row);
				$("#quotation_popup_list").modal('hide');
				index = 1;
				$("#list_items .new_item_row").each(function() {
					$(this).attr("data-new_row_index", index);
					index++;
				});	
				calculateTotalAmount();
			}
		});
	});

	$("#clear_quotation_number_button").on("click", function(event) {
		event.preventDefault();
		$("#list_items .quotation_item").remove();
		$("#quotation_number_text").text("---");
		$("#quotation_number").val("");
		return false;
	});

	$("#add_item_button").on("click", function( event ) {
		event.preventDefault();
		var product_found = true;
		var product_upc = getValue("product_upc");
		var product_qty = float2int(getValue("product_qty"));
		var product_unit_price = setFloat(getValue("product_unit_price"), 2);
		var product_total_amount = setFloat((product_qty * product_unit_price), 2);
		var payment_amount = setFloat(getValue("payment_amount"), 2);
		var product_name = "";
		var product_id = 0;
		var ok = $.checkFields("form_add_product_item");

		if (!ok) {
			 return false;
		}

		var jqxhr = $.ajax({
			type: "POST",
			url: ROOT + "get_product_info",
			async: false,
			data: { product_upc: product_upc }
		});

		jqxhr.done(function( product_info ) {
			if ( product_info === "" ) {
				$("#product_name_text").text("---");
				$("#error_product_upc").text("Product not found!");
				product_found = false;
			} else {
				product_id = product_info.id;
				product_name = product_info.name;
			}
		});

		if ( !product_found ) {
			return false;
		}

		$("#list_no_items_row").hide();
		$("#product_name_text").text("---");

		var new_row_index = $("#list_items .new_item_row").length;

		var item_row = ' \
		<div class="list_item_row deposit_item new_item_row" data-type="new" data-new_row_index="' + new_row_index + '" data-product_id="' + product_id + '"> \
            <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span> \
            <span class="list_column col_product_upc">' + product_upc + '</span> \
            <span class="list_column col_product_name">' + product_name + '</span> \
            <span class="list_column col_qty"><input type="text" class="form-control list_text_field list_product_qty num_item" value="' + product_qty + '"></span> \
            <span class="list_column col_unit_price"><input type="text" class="noProp form-control list_text_field list_product_unit_price num_item" value="' + product_unit_price + '"></span> \
            <span class="list_column col_item_total list_item_total">' + product_total_amount + '</span> \
            <span class="list_column col_remove_button"><span class="list_delete_single_item_button glyphicon glyphicon-remove"></span></span> \
        </div>';

		$("#list_items").append(item_row);
		enable("#list_checkbox_all");
		enable("#list_delete_multi_item_button");
		total_list_items++;
		$("#total_row_items").text(total_list_items);
		resetAddItemFields();
		calculateTotalAmount();
	});

	$("#payment_amount").on("blur", function() {
		payment_amount = setFloat($(this).val(), 2);
		calculateSubTotalAmount();
	});

	$("#form_confirm_button").on("click", function() {
		var ok = true;
		$("#no_list_item_error_message").hide();
		$(".form-error_message").empty();

		if ( total_list_items < 1 ) {
			ok = false;
			$("#no_list_item_error_message").show();
		}

		ok &= $.checkFields("form_deposit_edit");
		if ( !ok ) {
			alert("The form contains error\nPlease correct the errors before submit the form");
			return false;
		}

		var message = ( edit_type === EDIT_TYPE_NEW ) ? "create" : "update";
		var ans = window.confirm("Confirm to " + message + " the deposit?");
		if ( !ans ) {
			return false;
		}

		var formValue = new Object();
		var items = new Object();
		var i = 0;

		if ( edit_type === EDIT_TYPE_NEW ) {
			$(".list_item_row").each(function() {
				items[i] = new Object();
				items[i].row_index = $(this).data("new_row_index");
				items[i].product_id = $(this).data("product_id");
				items[i].qty = $(this).find(".list_product_qty").val();
				items[i].unit_price = $(this).find(".list_product_unit_price").val();
				items[i].is_quotation_item = ($(this).hasClass("quotation_item")) ? 1 : 0;
				i++;
			});
			formValue.items = items;
		}

		else if ( edit_type === EDIT_TYPE_UPDATE ) {
			formValue.removedItems = removedItems;
			var oldItems = [];
			var newItems = [];

			$(".list_item_row").each(function() {
				var type = $(this).data("type");
				var item = new Object();
				item.qty = $(this).find(".list_product_qty").val();
				item.unit_price = $(this).find(".list_product_unit_price").val();

				if ( type === "old" ) {
					item.record_id = $(this).data("record_id");
					oldItems.push(item);
				}

				else if ( type === "new" ) {
					item.row_index = $(this).data("new_row_index");
					item.product_id = $(this).data("product_id");
					newItems.push(item);
				}
			});

			formValue.oldItems = oldItems;
			formValue.newItems = newItems;
		}

		formValue.deposit_date = getValue("deposit_date");
		formValue.shop_code = getValue("shop_code");
		formValue.staff_id = getValue("staff_id");
		formValue.quotation_number = getValue("quotation_number");
		formValue.payment_type = getValue("payment_type");
		formValue.deposit_terms = getValue("deposit_terms");
		formValue.cheque_number = getValue("cheque_number");
		formValue.cheque_date = getValue("cheque_date");
		formValue.deposit_status = $("#status").is(":checked") ? 1 : 0;
		formValue.remarks = getValue("remarks");
		formValue.payment_amount = payment_amount;

		var type = "create";
		if ( edit_type === EDIT_TYPE_UPDATE ) {
			formValue.record_id = getValue("record_id");
			type = "update";
		}

	    var jqxhr = $.ajax({
	        type: 'POST',
	        url: ROOT + "deposit/" + type,
	        async: false,
	        data: formValue
	    });

	    jqxhr.done(function( result ) {
	    	result = JSON.parse(result);
	    	if ( result.status === "success" ) {
	    		if ( edit_type === EDIT_TYPE_NEW ) {
	    			var record_id = ( $("#record_id").length ) ? getValue("record_id") : result.deposit_id;
	    			window.open(ROOT + "deposit/print/" + record_id, 'Download');
		        	window.location = ROOT + "deposit/edit/" + record_id;
		        } else {
		        	$("#update_success_result").show();
		        }
		    } else {
		    	$("#edit_failure_result").show();
		    }
	    });
	});

	$("#print_button").on("click", printRecord);
});
