var total_amount = 0.00;
var discount_amount = 0.00;
var sub_total_amount = 0.00;

var removedItems = [];
var edit_type = 0;

function resetPage() {
	if ( edit_type === EDIT_TYPE_UPDATE ) {
		var record_id = getValue("record_id");
		window.location = ROOT + "quotation/edit/" + record_id;
		return false;
	}

	total_amount = 0.00;
	discount_amount = 0.00;
	sub_total_amount = 0.00;
	removedItems = [];
	$(".form-error_message").empty();
	$("#form_quotation_edit")[0].reset();
	$("#form_product_popup_list")[0].reset();
	// $("#form_quotation_popup_list")[0].reset();
	$("#comment").val("");
	resetAddItemFields();
	resetList();
}

function calculateTotalAmount() {
	total_amount = 0.00;
	var total_qty = 0;
	$(".list_item_row").each(function() {
		total_qty += parseInt($(this).find(".list_product_qty").val());
		total_amount += parseFloat($(this).find(".col_item_total").text());
	});
	total_amount = setFloat(total_amount, 2);
	$("#total_qty").text(total_qty);
	$("#total_amount_value").text(total_amount);
	calculateSubTotalAmount();
}

function calculateSubTotalAmount() {
	sub_total_amount = setFloat((total_amount - discount_amount), 2);
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
	discount_amount = 0.00;
	$("#list_checkbox_all").prop("checked", false);
	$("#no_list_item_error_message").hide();
	$("#sub_total_error_message").hide();
	$("#list_no_items_row").show();

	disable("#list_checkbox_all");
	disable("#list_delete_multi_item_button");
	disable("#discount_amount");
	$("#quotation_number").text("---");
	$("#total_amount_value").text("0.00");
	$("#discount_amount").val("0.00");
	$("#sub_total_amount_value").text("0.00");
	$("#list_items").empty();
	$("#total_row_items").text(0);
	$("#total_qty").text(0);
}

function resetAddItemFields() {
	$("#form_add_product_item")[0].reset();
	$("#product_upc").val("");
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

function deleteRecord() {
	var ans = window.confirm("Confirm to delete this Quotation record?");
	if ( !ans ) {
		return false;
	}

	var record_id = getValue("record_id");
    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "quotation/delete",
        async: false,
        data: { record_id: record_id }
    });

    jqxhr.done(function( result ) {
    	if ( parseInt(result) === 1 ) {
    		window.location = ROOT + "quotation/list";
	    } else {
	    	$("#delete_failure_result").show();
	    }
    });
}

function printRecord() {
	var win = window.open(ROOT + "quotation/print/" + getValue("record_id"), '_new');
	win.focus();
}

$(function() {
	edit_type = EDIT_TYPE_NEW;
	total_list_items = parseInt($("#total_row_items").text());

	if ( $("#record_id").length > 0 ) {
		edit_type = EDIT_TYPE_UPDATE;
		total_amount= parseFloat($("#total_amount_value").text());
		discount_amount = parseFloat(getValue("discount_amount"));
		if ( total_list_items > 0 ) {
			enable("#discount_amount");
			enable("#list_checkbox_all");
			enable("#list_delete_multi_item_button");
		}
	}

	if ( edit_type === EDIT_TYPE_NEW ) {
		resetPage();
	}

	$.setRules({
		"product_unit_price": {
			"required": true,
			"format": "number"
		},
		"email": {
			"format": "email"
		},
		"phone": {
			"format": "number"
		},
		"mobile": {
			"format": "number"
		},
		"fax": {
			"format": "number"
		}
	});

	updateList("product_popup_list");
	$(".no_record_found_message").hide();

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
		$("#error_product_upc").empty();
		$("#product_popup_list").modal("hide");
		$("#product_qty").val(1);
	});

	$("#add_item_button").on("click", function( event ) {
		event.preventDefault();
		var product_found = true;
		var product_upc = getValue("product_upc");
		var product_qty = float2int(getValue("product_qty"));
		var product_unit_price = setFloat(getValue("product_unit_price"), 2);
		var product_total_amount = setFloat((product_qty * product_unit_price), 2);
		var product_name = "";
		var product_id = 0;
		$(".form-error_message").empty();
		var ok = $.checkFields("form_add_product_item");

		if ( getValue("product_upc") == '' ) {
			$("#error_product_name").text("Please select a product first!").show();
			ok = false;
		}

		if ( !ok ) {
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
		<div class="list_item_row new_item_row" data-type="new" data-new_row_index="' + new_row_index + '" data-product_id="' + product_id + '"> \
            <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span> \
            <span class="list_column col_product_upc">' + product_upc + '</span> \
            <span class="list_column col_product_name">' + product_name + '</span> \
            <span class="list_column col_qty"><input type="text" class="form-control list_text_field list_product_qty num_item" value="' + product_qty + '"></span> \
            <span class="list_column col_unit_price"><input type="text" class="form-control list_text_field list_product_unit_price num_item" value="' + product_unit_price + '"></span> \
            <span class="list_column col_item_total list_item_total">' + product_total_amount + '</span> \
            <span class="list_column col_remove_button"><span class="list_delete_single_item_button glyphicon glyphicon-remove"></span></span> \
        </div>';

		$("#list_items").append(item_row);
		enable("#list_checkbox_all");
		enable("#list_delete_multi_item_button");
		enable("#discount_amount");
		total_list_items++;
		$("#total_row_items").text(total_list_items);
		resetAddItemFields();
		calculateTotalAmount();
	});

	$("#discount_amount").on("blur", function() {
		discount_amount = parseFloat($(this).val());
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

		if ( sub_total_amount < 0.00 ) {
			ok = false;
			$("#sub_total_error_message").show();
		}

		ok &= $.checkFields("form_quotation_edit");
		if ( !ok ) {
			alert("The form contains error\nPlease correct the errors before submit the form");
			return false;
		}

		var message = ( edit_type === EDIT_TYPE_NEW ) ? "create" : "update";
		var ans = window.confirm("Confirm to " + message + " the quotation order?");
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

		formValue.quote_date = getValue("quote_date");
		formValue.quote_type = getValue("quote_type");
		formValue.quote_terms = getValue("quote_terms");		
		formValue.staff_id = getValue("staff_id");
		formValue.remarks = getValue("remarks");
		formValue.comment = getValue("comment");		
		formValue.discount_amount = getValue("discount_amount");
		formValue.status = $("input[name='status']").is(":checked") ? 1 : 0;

		var type = "create";
		if ( edit_type === EDIT_TYPE_UPDATE ) {
			formValue.record_id = getValue("record_id");
			type = "update";
		}

	    var jqxhr = $.ajax({
	        type: 'POST',
	        url: ROOT + "quotation/" + type,
	        async: false,
	        data: formValue
	    });

	    jqxhr.done(function( result ) {
	    	result = JSON.parse(result);
	    	if ( result.status === "success" ) {
	    		if ( edit_type === EDIT_TYPE_NEW ) {
	    			var record_id = ( $("#record_id").length ) ? getValue("record_id") : result.quotation_id;
		        	window.location = ROOT + "quotation/edit/" + record_id;
		        } else {
		        	$("#update_success_result").show();
		        }
		    } else {
		    	$("#edit_failure_result").show();
		    }
	    });
	});
});