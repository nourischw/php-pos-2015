var addedListItems = [];

function resetList() {
	total_list_items = 0;
	$("#list_checkbox_all").removeAttr("checked");
	addedListItems = [];
	disable("#list_checkbox_all");
	disable("#list_delete_multi_item_button");
	$("#list_no_items_row").show();
	$("#list_items").empty();
	$("#total_list_items").text(0);
	$("#item_already_exist_message").hide();
	$("#no_list_item_error_message").hide();
	$("#item_qty_changed_message").addClass("hide");
	$("#total_amount_value").text("0.00");
}

function resetPage() {
	resetList();
	$("#select_supplier_block").show();
	disable("#search_stock_withdraw_popup_list_button");
	$("#supplier_name_text").text("---");
	$("#search_supplier_id").val("");
	$("#supplier_info").hide();
	$(".form-error_message").empty();
}

function calculateItemTotalAmount() {
	total_amount = 0.00;
	var total_qty = 0;
	$(".list_item_row").each(function() {
		total_qty += parseInt($(this).find(".list_withdraw_qty").val());
		total_amount += parseFloat($(this).find(".list_item_total").text());
	});
	total_amount = setFloat(total_amount, 2);
	$("#total_qty").text(total_qty);
	$("#total_amount_value").text(total_amount);
	calculateNetTotalAmount();
}

function calculateNetTotalAmount() {
	net_amount = setFloat(total_amount, 2);
	$("#net_total_amount_value").text(net_amount);
}

function removeSingleItem( obj ) {
	total_list_items--;
	
	var stock_id = parseInt(obj.closest(".list_item_row").data("stock_id"));
	addedListItems.splice(addedListItems.indexOf(stock_id), 1);
	obj.closest(".list_item_row").remove();

	if ( total_list_items < 1 ) {
		resetList();
		return false;
	}
	
	checkHaveInvalidItem();
	calculateItemTotalAmount();
	$("#total_list_items").text(total_list_items);
}

function removeMultiItems() {
	$(".list_selected_row").each(function() {
		var stock_id = $(this).data("stock_id");
		addedListItems.splice(addedListItems.indexOf(stock_id), 1);
	});

	checkHaveInvalidItem();

	$(".list_selected_row").remove();
	total_list_items = $(".list_item_row").length;

	if (total_list_items < 1) {
		resetList();
	}
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
    } else if ( $("#list_items .invalid_item").length > 0 ) {
		alert("The list still contain invalid items\nPlease remove them first in order to continue");
		return false;
	}
	
	return true;
}

function confirmRecord() {
    var ok = true;

    if ( total_list_items < 1 ) {
    	$("#no_list_item_error_message").show();
    	return false;
    }

    var ans = window.confirm("Confirm to create the stock withdraw record?");   
    if ( !ans ) {
    	return false;
    }

    var formValue = new Object();
    var items = new Object();
    var i = 0;

	$(".list_item_row").each(function() {
		items[i] = new Object();
		items[i].gi_id = $(this).data("gi_id");
		items[i].stock_id = $(this).data("stock_id");
		items[i].qty = $(this).find(".list_withdraw_qty").val();
		items[i].unit_price = $(this).find(".list_item_price").text();
		i++;
	});
	formValue.items = items;
	
	formValue.withdraw_date = getValue("withdraw_date");
	formValue.supplier_id = getValue("supplier_id");
	formValue.remarks = getValue("remarks");

    var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + "/stock_withdraw/create",
        async: false,
        data: formValue
    });

    var total_new_items = i;

    jqxhr.done(function( result ) {
    	result = JSON.parse(result);
    	if ( result.status === "success" ) {
	        window.location = ROOT + "stock_withdraw/details/" + result.stock_withdraw_id;
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
		} else if (result.status === "failure") {
			$("#edit_failure_result").show();
		}
    });
}

$(function() {
	resetPage();

	$("#select_supplier_button").on("click", function() {
		var supplier_id = getValue("supplier");
		var supplier_name = $("#supplier option:selected").text();
		$("#supplier_id, #search_supplier_id").val(supplier_id);
		$("#supplier_name_text, #popup_supplier_name_text").text(supplier_name);
		$("#select_supplier_block").hide();
		$("#supplier_info").show();
		$("#item_already_exist_message").hide();
		enable("#search_stock_withdraw_popup_list_button");
		updateList("stock_withdraw_popup_list");
	});
	
	$("#change_supplier_button").on("click", function() {
		$("#item_already_exist_message").hide();
		var message = "Warning!\nChange supplier will clear all stock items in the list\nAre you sure to change the supplier?"
		var ans = window.confirm(message);
		
		if ( ans ) {
			disable("#search_stock_withdraw_popup_list_button");
			$("#supplier_id").val("");
			$("#supplier_name_text").text("---");
			$("#select_supplier_block").show();
			$("#supplier_info").hide();
			resetList();
		}
	});
	
	$("#stock_withdraw_popup_list").on("dblclick", ".popup_list_item_row",  function() {
		var stock_id = parseInt($(this).data("stock_id"));
		
		if ( $.inArray(stock_id, addedListItems) !== -1 ) {
			$("#stock_withdraw_popup_list").modal('hide');
			$("#item_already_exist_message").show();
			return false;
		}
		
		addedListItems.push(stock_id);
		
		var gi_id = $(this).data("gi_id");
		var gi_code = $(this).find(".col_gi_code").text();
		var product_upc = $(this).find(".col_product_upc").text();
		var product_name = $(this).find(".col_product_name").text();
		var serial_number = $(this).find(".col_serial_number").text();
		var serial_number_text = (serial_number !== "---") ? "S/N: " + serial_number : "";
		var stock_qty = parseInt($(this).find(".col_qty").text());
		var each_price = $(this).find(".col_unit_price").text();
		$("#list_no_items_row").hide();
		$("#no_list_item_error_message").hide();
		
        var item_row = ' \
		<div class="list_item_row new_item_row" id="stock_id_' + stock_id + '" data-gi_id="' + gi_id + '" data-stock_id="' + stock_id + '"> \
            <span class="list_checkbox_column"><input type="checkbox" class="list_item_checkbox" /></span> \
            <span class="list_column col_gi_code">' + gi_code +'</span> \
			<span class="list_column col_product_upc">' + product_upc + '</span> \
            <span class="list_column col_product_name">' + product_name + ' \
				<br /><span class="product_sn">' + serial_number_text + '</span> \
			</span> \
            <span class="list_column col_qty"><input type="text" class="form-control list_withdraw_qty num_item" style="height: 20px; text-align: center;" data-num_flags="+i" value="1" maxlength="6" /></span> \
            <span class="list_column col_remain">' + stock_qty + '</span> \
            <span class="list_column col_price list_item_price">' + each_price + '</span> \
            <span class="list_column col_price list_item_total">' + each_price + '</span> \
            <span class="list_column col_remove_button"><span class="list_delete_single_item_button glyphicon glyphicon-remove"></span></span> \
        </div>';
		
		$("#list_items").append(item_row);
        enable("#list_checkbox_all");
        enable("#list_delete_multi_item_button");
        $("#item_already_exist_message").hide();
        total_list_items++;
        calculateItemTotalAmount();
		$("#total_list_items").text(total_list_items);
		$("#stock_withdraw_popup_list").modal('hide');
	});
	
	$("#list_items").on("click", ".list_withdraw_qty", function( event ) {
		event.stopPropagation();
	});
	
	$("#list_items").on("blur", ".list_withdraw_qty", function() {
		var qty = $(this).val();
		var max_qty = parseInt($(this).closest(".list_item_row").find(".col_remain").text());

		qty = (qty == "" || isNaN(qty) || parseInt(qty) < 1) ? 1 : parseInt(qty);
		if ( qty > max_qty ) {
			qty = max_qty;
		}

		$(this).val(qty);
		var index = $(this).closest(".list_item_row").index(".list_item_row");
		var unit_price = setFloat($(".list_item_price").eq(index).text(), 2);
		var item_total = parseInt(qty) * unit_price;
		item_total = item_total.toFixed(2);
		$(".list_item_total").eq(index).text(item_total);
		calculateItemTotalAmount();
	});
});