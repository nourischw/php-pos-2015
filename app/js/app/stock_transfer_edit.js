var addedListItems = [];

var SUBMIT_TYPE_CONFIRM = 1;
var SUBMIT_TYPE_TEMP_SAVE = 2;

var EDIT_TYPE_NEW = 1;
var EDIT_TYPE_UPDATE = 2;

var removedItems = [];
var edit_type = 0;

function resetList() {
	total_list_items = 0;
	$("#list_checkbox_all").prop("checked", false);
	addedListItems = [];
	disable("#list_checkbox_all");
	disable("#list_delete_multi_item_button");
	$("#list_no_items_row").show();
	$("#list_items").empty();
	$("#total_qty").text(0);
	$("#total_list_items").text(0);
	$("#item_already_exist_message").hide();
	$("#no_list_item_error_message").hide();
	$("#item_qty_changed_message").addClass("hide");
}

function resetPage() {
	if ( edit_type === EDIT_TYPE_UPDATE ) {
		var record_id = getValue("record_id");
		window.location = ROOT + "stock_transfer/edit/" + record_id;
		return false;
	}
	
	resetList();
	$("#select_from_shop_block").show();
	disable("#to_shop");
	disable("#search_stock_popup_list_button");
	$("#from_shop option[value='11']").prop("selected", true);
	$("#from_shop_name_text").text("---");
	$("#from_shop_info").hide();
	$("#form_stock_transfer_info")[0].reset();
	$("#from_shop_name_field").hide();
	$(".form-error_message").empty();
}

function calculateTotalQty() {
	if ( total_list_items < 1 ) {
		resetList();
		return false;
	}

	$("#total_list_items").text(total_list_items);
	var total_qty = 0;
	$(".list_transfer_qty").each(function() {
		total_qty += parseInt($(this).val());
	});
	$("#total_qty").text(total_qty);
}

function selectAvailableToShopOption() {
	$("#to_shop option").each(function() {
		if ( !($(this).is(":disabled")) ) {
			$(this).prop("selected", true);
			return false;
		}
	});
}

function removeSingleItem( obj ) {
	if ( edit_type === EDIT_TYPE_UPDATE ) {
		var record = obj.closest(".list_item_row").data("record_id");
		removedItems.push(record_id);
	}

	total_list_items--;
	
	var stock_id = parseInt(obj.closest(".list_item_row").data("stock_id"));
	addedListItems.splice(addedListItems.indexOf(stock_id), 1);
	obj.closest(".list_item_row").remove();
	checkHaveInvalidItem();

	if ( total_list_items < 1 ) {
		resetList();
		return false;
	}
	
	$("#total_list_items").text(total_list_items);
	calculateTotalQty();
}

function removeMultiItems() {
	if( edit_type === EDIT_TYPE_UPDATE ) {
		$(".list_selected_row").each(function() {
			var stock_id = $(this).data("stock_id");
			var record_id = $(this).closest(".list_item_row").data("record_id");
			removedItems.push(record_id);
			addedListItems.splice(addedListItems.indexOf(stock_id), 1);
		});
	} else {
		$(".list_selected_row").each(function() {
			var stock_id = $(this).data("stock_id");
			addedListItems.splice(addedListItems.indexOf(stock_id), 1);
		});
	}

	checkHaveInvalidItem();

	$(".list_selected_row").each(function() {
		var stock_id = $(this).data("stock_id");
		addedListItems.splice(addedListItems.indexOf(stock_id), 1);
	});
	
	$(".list_selected_row").remove();
	total_list_items = $(".list_item_row").length;
	calculateTotalQty();
}

function checkHaveInvalidItem() {
	if ($("#list_items .invalid_item").length < 1) {
		$("#item_qty_changed_message").addClass("hide");
	} else {
		$("#item_qty_changed_message").removeClass("hide");
	}
}

function validateForm() {
    if ( total_list_items < 1 ) {
    	$("#no_list_item_error_message").show();
		alert("The form contains error\nPlease correct the errors before submit the form");    	
    	return false;
    } else if ($("#list_items .invalid_item").length > 0) {
		alert("The list still contain invalid items\nPlease remove them first in order to continue");
		return false;
	}
	
	return true;
}

function processRecord( submit_type, form_data ) {
	var message = ( submit_type === SUBMIT_TYPE_TEMP_SAVE ) ? "temp save" : "submit";
    var ans = window.confirm("Confirm to " + message + " the stock transfer order?");   
    if ( !ans ) {
    	return false;
    }

    var formValue = new Object();
    var items = new Object();
    var i = 0;

    if ( edit_type === EDIT_TYPE_NEW ) {
	    $(".list_item_row").each(function() {
	        items[i] = new Object();
	        items[i].stock_id = $(this).data("stock_id");
	        items[i].transfer_qty = $(this).find(".list_transfer_qty").val();
	        i++;
	    });
	    formValue.items = items;
	}

	else if ( edit_type === EDIT_TYPE_UPDATE ) {
		formValue.removedItems = removedItems.join(',');
		var oldItems = [];
		var newItems = [];

	    $(".list_item_row").each(function() {
	    	var type = $(this).data("type");
	    	var item = new Object();
	    	item.stock_id = $(this).data("stock_id");
	    	item.transfer_qty = $(this).find(".list_transfer_qty").val();
			
	    	if ( type === "old" ) {
	    		item.record_id = $(this).data("record_id");
	    		oldItems.push(item);
	    	}

	    	else if ( type === "new" ) {
	    		newItems.push(item);
	    	}
	    });

	    formValue.oldItems = oldItems;
	    formValue.newItems = newItems;
	}

	formValue.from_shop = getValue("from_shop_id");
    formValue.date_out = getValue("date_out");
    formValue.to_shop = getValue("to_shop");
    formValue.staff_id = getValue("staff_id");
    formValue.request_by = getValue("request_by");
    formValue.remarks = getValue("remarks");
    formValue.status = submit_type;

	var type = "create";
    if ( edit_type === EDIT_TYPE_UPDATE ) {
    	formValue.record_id = getValue("record_id");
    	type = "update";
    }

    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "/stock_transfer/" + type,
        async: false,
        data: formValue
    });

    var total_new_items = i;

    jqxhr.done(function( result ) {
    	result = JSON.parse(result);
    	if ( result.status === "success" ) {
    		var record_id = ( $("#record_id").length ) ? getValue("record_id") : result.stock_transfer_id;
	   		if ( submit_type === SUBMIT_TYPE_CONFIRM ) {
	            window.open(ROOT + "stock_transfer/print/" + record_id, 'Download');
	            window.location = ROOT + "stock_transfer/details/" + record_id;
	        } else {
	        	var stock_transfer_number = result.stock_transfer_number;
	        	var new_item_ids = ( edit_type === EDIT_TYPE_NEW ) ? formValue.items : formValue.newItems;
	        	if (edit_type === EDIT_TYPE_UPDATE) {
	        		total_new_items = new_item_ids.length;
	        	}
	        	if ( total_new_items > 0 ) {
		        	for ( var i = 0; i <= total_new_items; i++ ) {
		        		$(".new_item_row")
		        			.eq(i)
		        			.removeAttr("data-new_row_index")
		        			.attr("data-record_id", result.new_item_ids[i])			
		        			.attr("data-type", "old")
		        	}
		        	$(".new_item_row").removeClass("new_item_row");
		        }

	        	edit_type = EDIT_TYPE_UPDATE;
	        	$("#delete_button, #create_new_record_button").show();
	        	$("#id_text").text(stock_transfer_number);
	        	$("body").append('<input type="hidden" id="record_id" value="' + record_id + '" />');
	        	$("#edit_" + result.status + "_result").show();
	        }
	    } else if ( result.status === "have_invalid_items" ) {
			$("#list_items .list_item_row").removeClass("list_selected_row");
			var invalid_items = result.invalid_items;
			for ( var i = 0; i < invalid_items.length; i++ ) {
				var item_id = invalid_items[i]['id'];
				var item_remain_qty = parseInt(invalid_items[i]['remain_qty']);
				var row = $("html #stock_id_" + invalid_items[i]['id']);
				
				if (item_remain_qty <= 0) {
					row.addClass("invalid_item").find(".col_qty").html("---");
					row.find(".col_remain").text(item_remain_qty);
				} else {
					row.addClass("invalid_item").find(".col_remain").text(item_remain_qty);
				}
			}
			
			$("#have_invalid_items_block").show();
			$("#item_qty_changed_message").removeClass("hide");
			calculateTotalQty();
		} else if (result.status === "failure") {
			$("#edit_failure_result").show();
		}
    });
}

function tempSaveRecord() {
	var is_valid = validateForm();
	if ( !is_valid ) {
		return false;
	}
    processRecord(SUBMIT_TYPE_TEMP_SAVE);
}

function confirmRecord() {
	var is_valid = validateForm();
	if ( !is_valid ) {
		return false;
	}
    processRecord(SUBMIT_TYPE_CONFIRM);
}

function deleteRecord() {
	var ans = window.confirm("Confirm to delete this Stock Transfer record?");
	if ( !ans ) {
		return false;
	}

	var record_id = getValue("record_id");
    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "stock_transfer/delete",
        async: false,
        data: { record_id: record_id }
    });

    jqxhr.done(function( result ) {
    	if ( parseInt(result) === 1 ) {
    		window.location = ROOT + "stock_transfer/list";
	    } else {
	    	$("#delete_failure_result").show();
	    }
    });
}

$(function() {
	edit_type = EDIT_TYPE_NEW;
	total_list_items = parseInt($("#total_list_items").text());
	checkHaveInvalidItem();
	calculateTotalQty();

	if ( $("#record_id").length > 0 ) {
		edit_type = EDIT_TYPE_UPDATE;
		enable("#list_checkbox_all");
		enable("list_delete_multi_item_button");
		$("#list_no_items_row").hide();

		$(".list_item_row").each(function() {
			var stock_id = $(this).data("stock_id");
			addedListItems.push(stock_id);
		});
		enable("#search_stock_popup_list_button");

		var from_shop_id = getValue("from_shop_id");
		var from_shop_name = $("#from_shop_name_text").text();
		$("#from_shop_name").text(from_shop_name);
		$("#search_from_shop").val(from_shop_id);
		updateList("stock_popup_list");
	}

	if ( edit_type === EDIT_TYPE_NEW ) {
		resetPage();
	}
	
	$("#select_from_shop_button").on("click", function() {
		var from_shop_id = getValue("from_shop");
		var from_shop_name = $("#from_shop option:selected").text();
		$("#item_already_exist_message").hide();
		$("#from_shop_id, #search_from_shop").val(from_shop_id);
		$("#from_shop_name_text, #from_shop_name").text(from_shop_name);
		$("#select_from_shop_block").hide();
		$("#from_shop_info").show();
		enable("#search_stock_popup_list_button");
		
		enable("#to_shop option");
		disable("#to_shop option[value='" + from_shop_id + "']");
		enable("#to_shop");
		selectAvailableToShopOption();
		updateList("stock_popup_list");
	});
	
	$("#change_from_shop_button").on("click", function() {
		var message = "Warning!\nChange From shop will clear all stock items in the list\nAre you sure to change the from shop?"
		var ans = window.confirm(message);
		
		if ( ans ) {
			disable("#to_shop");
			disable("#search_stock_popup_list_button");
			$("#from_shop_id, #search_from_shop").val("");
			$("#from_shop_name_text, #from_shop_name").text("---");
			$("#select_from_shop_block").show();
			$("#from_shop_info").hide();
			resetList();
		}
	});
	
	$("#stock_popup_list").on("dblclick", ".popup_list_item_row",  function() {
		var stock_id = parseInt($(this).data("stock_id"));
		
		if ( $.inArray(stock_id, addedListItems) !== -1 ) {
			$("#stock_popup_list").modal('hide');
			$("#item_already_exist_message").show();
			return false;
		}
		
		addedListItems.push(stock_id);
		
		var product_upc = $(this).find(".col_product_upc").text();
		var product_name = $(this).find(".col_product_name").text();
		var serial_number = $(this).find(".col_serial_number").text();
		var serial_number_text = (serial_number !== "---") ? "S/N: " + serial_number : "";
		var stock_qty = parseInt($(this).find(".col_qty").text());
		$("#list_no_items_row").hide();
		$("#no_list_item_error_message").hide();
		
        var item_row = ' \
		<div class="list_item_row new_item_row" id="stock_id_' + stock_id + '" data-type="new" data-stock_id="' + stock_id + '"> \
            <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span> \
			<span class="list_column col_product_upc">' + product_upc + '</span> \
            <span class="list_column col_product_name">' + product_name + ' \
				<br /><span class="product_sn">' + serial_number_text + '</span> \
			</span> \
            <span class="list_column col_qty"><input type="text" class="form-control list_transfer_qty num_item" data-num_flags="+i" value="1" maxlength="6" /></span> \
            <span class="list_column col_remain">' + stock_qty + '</span> \
            <span class="list_column col_remove_button"><span class="list_delete_single_item_button glyphicon glyphicon-remove"></span></span> \
        </div>';
		
		$("#list_items").append(item_row);
        enable("#list_checkbox_all");
        enable("#list_delete_multi_item_button");
        total_list_items++;
		$("#total_list_items").text(total_list_items);
		calculateTotalQty();
		$("#stock_popup_list").modal('hide');
	});
	
	$("#list_items").on("click", ".list_transfer_qty", function( event ) {
		event.stopPropagation();
	});
	
	$("#list_items").on("blur", ".list_transfer_qty", function() {
		var qty = $(this).val();
		var max_qty = parseInt($(this).closest(".list_item_row").find(".col_remain").text());

		qty = (qty == "" || isNaN(qty) || parseInt(qty) < 1) ? 1 : parseInt(qty);
		if ( qty > max_qty ) {
			qty = max_qty;
		}
		
		$(this).val(qty);
		calculateTotalQty();
	});
});