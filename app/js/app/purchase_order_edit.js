var total_amount = 0.00;
var discount_amount = 0.00;

var SUBMIT_TYPE_CONFIRM = 1;
var SUBMIT_TYPE_TEMP_SAVE = 2;

var removedPOItems = [];
var edit_type = 0;

function resetAddItemFields() {
	$("#form_add_product_item")[0].reset();
	$("#product_upc").val("");
}

function resetList() {
	total_list_items = 0;
	total_amount = 0.00;
	discount_amount = 0.00;
	$("#list_checkbox_all").prop("checked", false);
	$("#no_list_item_error_message").hide();
	$("#net_total_error_message").hide();
	$("#list_no_items_row").show();

	disable("#list_checkbox_all");
	disable("#list_delete_multi_item_button");
	disable("#discount_amount");		
	$("#total_amount_value").text("0.00");
	$("#discount_amount").val("0.00");
	$("#net_total_amount_value").text("0.00");
	$("#list_items").empty();
	$("#total_row_items").text(0);
	$("#total_qty").text(0);
}

function resetPage() {
	if ( edit_type === EDIT_TYPE_UPDATE ) {
		var record_id = getValue("record_id");
		window.location = ROOT + "purchase_order/edit/" + record_id;
		return false;
	}

	removedPOItems = [];
	$(".form-error_message").empty();
	$("#form_purchase_order_info")[0].reset();
	$("#form_purchase_order_bottom")[0].reset();
	$("#form_product_popup_list")[0].reset();
	$("#form_supplier_popup_list")[0].reset();

	$("#no_valid_supplier_error_message").hide();
	resetAddItemFields();
	resetList();
	resetSupplierInfo();

	$('#ship_to option[value="11"]').prop("selected", true);
	$('#payment_type option[value="6"]').prop("selected", true);
}

function resetSupplierInfo() {
	$("#supplier_shop_name").text("---");
	$("#supplier_mobile").text("---");
	$("#supplier_fax").text("---");
	$("#supplier_email").text("---");
	$("#supplier_id").val("");
}

function calculateTotalAmount() {
	total_amount = 0.00;
	var total_qty = 0;
	$(".list_item_row").each(function() {
		total_qty += parseInt($(this).find(".list_product_qty").val());
		total_amount += parseFloat($(this).find(".list_item_total").text());
	});
	total_amount = setFloat(total_amount, 2);
	$("#total_qty").text(total_qty);
	$("#total_amount_value").text(total_amount);
	calculateNetTotalAmount();
}

function calculateNetTotalAmount() {
	net_amount = setFloat((total_amount - discount_amount), 2);
	if ( net_amount < 0.00 ) {
		$("#net_total_error_message").show();
	} else {
		$("#net_total_error_message").hide();
	}
	$("#net_total_amount_value").text(net_amount);
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

function getSupplierInfo() {
	$("#error_supplier_code").text("");

	var code = getValue("supplier_code");
	if ( code === "" ) {
		$("#error_supplier_code").text("Required");
		resetSupplierInfo();
		return false;
	}

	var jqxhr = $.ajax({
		type: 'POST',
		url: ROOT + "get_supplier_info",
		async: false,
		data: { code: code }
	});

	jqxhr.done(function(result) {
		if ( result == "" ) {
			$("#error_supplier_code").text("Supplier not found!");
			resetSupplierInfo();
		} else {
			var mobile = ( result.mobile != null && result.mobile != '' ) ? result.mobile : '---';
			var fax = ( result.fax != null && result.fax != '' ) ? result.fax : '---';
			var email = ( result.email != null && result.email != '' ) ? result.email : '---';
			$("#supplier_id").val(result.supplier_id);
			$("#supplier_shop_name").text(result.supplier_name);
			$("#supplier_mobile").text(mobile);
			$("#supplier_fax").text(fax);
			$("#supplier_email").text(email);
		}
	});
}

function removeSingleItem( obj ) {
	if ( edit_type === EDIT_TYPE_UPDATE ) {
		var record_id = obj.closest(".list_item_row").data("record_id");
		removedPOItems.push(record_id);
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
			removedPOItems.push(record_id);
			$(this).remove();
		});
	} else {
		$(".list_selected_row").each(function() {
			$(this).remove();
		});
	}
	calculateTotalAmount();
	total_list_items = $(".list_item_row").length;
	if ( total_list_items < 1 ) {
		resetList();
	}
	$("#total_row_items").text(total_list_items);
}

function validateForm() {
    var supplier_id = getValue("supplier_id");
    var ok = true;
    $("#error_supplier").text("");
    $("#form_add_product_item .form-error_message").hide();

    if ( total_list_items < 1 ) {
    	ok = false;
    	$("#no_list_item_error_message").show();
    }

	if ( supplier_id === "" ) {
		ok = false;
		$("#no_valid_supplier_error_message").show();
	}

	if ( net_amount < 0.00 ) {
		ok = false;
		$("#net_total_error_message").show();
	}

	ok &= $.checkFields("form_purchaser_order");
	if ( !ok ) {
		alert("The form contains error\nPlease correct the errors before submit the form");
		return false;
	}

	return true;
}

function processRecord( submit_type, form_data ) {
	var message = ( submit_type === SUBMIT_TYPE_TEMP_SAVE ) ? "temp save" : "submit";
    var ans = window.confirm("Confirm to " + message + " the purchase order?");
    var type = "create";
    if ( !ans ) {
    	return false;
    }

    var formValue = new Object();
    var POItems = new Object();
    var i = 0;

    if ( edit_type === EDIT_TYPE_NEW ) {
	    $(".list_item_row").each(function() {
	        POItems[i] = new Object();
	        POItems[i].row_index = $(this).data("new_row_index");
	        POItems[i].product_id = $(this).data("product_id");
	        POItems[i].qty = $(this).find(".list_product_qty").val();
	        POItems[i].unit_price = $(this).find(".list_product_unit_price").val();
	        i++;
	    });
	    formValue.POItems = POItems;
	}

	else if ( edit_type === EDIT_TYPE_UPDATE ) {
		formValue.removedPOItems = removedPOItems;
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

	    formValue.oldPOItems = oldItems;
	    formValue.newPOItems = newItems;
	}

    formValue.discount_amount = getValue("discount_amount");
    formValue.order_date = getValue("order_date");
    formValue.staff_code = getValue("staff_code");
    formValue.deposit_no = getValue("deposit_no");
    formValue.ship_to = getValue("ship_to");
    formValue.request_by = getValue("request_by");
    formValue.payment_type = getValue("payment_type");
    formValue.supplier_id = getValue("supplier_id");
    formValue.remarks = $.trim(getValue("remarks"));
    formValue.status = submit_type;
	
    if ( edit_type === EDIT_TYPE_UPDATE ) {
    	formValue.record_id = getValue("record_id");
    	type = "update";
    }

    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "purchase_order/" + type,
        async: false,
        data: formValue
    });

    jqxhr.done(function( result ) {
    	result = JSON.parse(result);
    	if ( result.status === "success" ) {
    		var record_id = ( $("#record_id").length ) ? getValue("record_id") : result.purchase_order_id;
	   		if ( submit_type === SUBMIT_TYPE_CONFIRM ) {
	            window.open(ROOT + "purchase_order/print/" + record_id, 'Download');
	            window.location = ROOT + "purchase_order/details/" + record_id;
	        } else {
	        	var purchase_order_number = result.purchase_order_number;
	        	var new_item_ids = ( edit_type === EDIT_TYPE_NEW ) 
	        		? result.purchase_order_item_id : formValue.newPOItems;
	        	var total_new_items = new_item_ids.length;
	        	if ( total_new_items > 0 ) {
		        	for ( var i = 0; i < total_new_items; i++ ) {
		        		$(".new_item_row")
		        			.eq(i)
		        			.removeAttr("data-new_row_index")
		        			.removeAttr("data-product_id")
		        			.attr("data-record_id", new_item_ids[i])	
		        			.attr("data-type", "old")
		        	}
		        	$(".new_item_row").removeClass("new_item_row");
		        }

	        	edit_type = EDIT_TYPE_UPDATE;
	        	$("#delete_button, #create_new_record_button").show();
	        	$("#id_text").text(purchase_order_number);
	        	$("body").append('<input type="hidden" id="record_id" value="' + record_id + '" />');
	        	$("#edit_" + result.status + "_result").show();
	        }
	    }
    });
}

function tempSaveRecord() {
	if ( !validateForm() ) {
		return false;
	}
    processRecord(SUBMIT_TYPE_TEMP_SAVE);
}

function confirmRecord() {
	if ( !validateForm() ) {
		return false;
	}
    processRecord(SUBMIT_TYPE_CONFIRM);
}

function deleteRecord() {
	var ans = window.confirm("Confirm to delete this Purchase Order record?");
	if ( !ans ) {
		return false;
	}

	var record_id = getValue("record_id");
    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "purchase_order/delete",
        async: false,
        data: { record_id: record_id }
    });

    jqxhr.done(function( result ) {
    	if ( parseInt(result) === 1 ) {
    		window.location = ROOT + "purchase_order/list";
	    } else {
	    	$("#delete_failure_result").show();
	    }
    });
}

function showProductPopupList() {
	var search_product_name = getValue("product_name");
	$("#popup_search_product_name").val(search_product_name);
	updateList("product_popup_list");
	$("#product_popup_list").modal();
}

$(function() {
	edit_type = EDIT_TYPE_NEW;
	total_list_items = parseInt($("#total_row_items").text());

	$("#no_valid_supplier_error_message").hide();
	
	if ( $("#record_id").length > 0 ) {
		edit_type = EDIT_TYPE_UPDATE;
		total_amount = parseFloat($("#total_amount").text());
		discount_amount = parseFloat(getValue("discount_amount"));
		net_amount = total_amount - discount_amount;
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
		"supplier_code": {
			"required": true
		}
	});

	updateList("product_popup_list");
	updateList("supplier_popup_list");
	$(".no_record_found_message").hide();

	$("#discount_amount").on("blur", function() {
		discount_amount = $(this).val();
		calculateTotalAmount();
	});

	$("#product_qty").on("blur", function() {
		if ( getValue("product_upc") !== '') {
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
			showProductPopupList();
		}
	});

	$("#show_product_popup_list_button").on("click", function() { showProductPopupList(); });

	$("#get_product_info_button").on("click", function( event ) {
		event.preventDefault();
		getProductInfo();
	});

	$("#product_popup_list").on("dblclick", ".popup_list_item_row", function() {
		$("#product_upc").val($(this).find(".col_upc").text());
		$("#product_name_text").text($(this).find(".col_name").text());
		$("#error_product_upc").empty();
		$("#product_popup_list").modal('hide');
		$("#product_qty").val(1);
	});

	$("#add_item_button").on("click", function( event ) {
		event.preventDefault();
		var product_found = true;
		var product_upc = getValue("product_upc");
		var product_qty = float2int(getValue("product_qty"));
		var product_unit_price = getValue("product_unit_price");

		if ( product_unit_price != "" && !isNaN(product_unit_price) ) {
			product_unit_price = setFloat(product_unit_price, 2);
		} else {
			product_unit_price = 0.00;
		}
		
        var product_total_amount = setFloat((product_qty * product_unit_price), 2);
        var product_name = "";
        var product_id = 0;
		var ok = $.checkFields("form_add_product_item");

		if ( !ok ) {
			return false;
		}
		
        var jqxhr = $.ajax({
            type: 'POST',
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
            <span class="list_column col_qty"><input type"text" class="noProp form-control list_text_field list_product_qty num_item" value="' + product_qty + '"></span> \
            <span class="list_column col_unit_price"><input type"text" class="noProp form-control list_text_field list_product_unit_price num_item" value="' + product_unit_price + '"></span> \
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
        $("#no_list_item_error_message").hide();
		return false;
	});

	if ( edit_type === EDIT_TYPE_UPDATE ) {
		$("#list_items").on("click", ".list_delete_single_item_button", function() {
			var record_id = $(this).closest(".list_item_row").data("record_id");
			removedPOItems.push(record_id);
		});

		$("#list_delete_multi_item_button").on("click", function() {
			$(".list_selected_row").each(function() {
				var record_id = $(this).closest(".list_item_row").data("record_id");
				removedPOItems.push(record_id);
			});
		});
	}

	$("#supplier_popup_list").on("dblclick", ".popup_list_item_row", function() {
		var mobile = $(this).find(".col_mobile").text() || "---";
		var fax = $(this).find(".col_fax").text() || "---";
		var email = $(this).find(".col_email").text() || "---";

		$("#supplier_code").val($(this).find(".col_code").text());
		$("#supplier_shop_name").text($(this).find(".col_name").text());
		$("#supplier_mobile").text(mobile);
		$("#supplier_fax").text(fax);
		$("#supplier_email").text(email);
		$("#supplier_id").val($(this).data("supplier_id"));
		$("#error_supplier").empty();
		$("#supplier_popup_list").modal('hide');
		$("#error_supplier_code").empty();
		$("#no_valid_supplier_error_message").hide();
	});

	$("#get_supplier_info_button").on("click", function( event ) {
		event.preventDefault();
		getSupplierInfo();
	});
	
	$("#supplier_code").on("blur", function() {
		getSupplierInfo();
	});
});