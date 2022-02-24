"use strict";
var selectedItems;
var total_list_items = 0;
var total_amount = 0.00;
var add_item_id = 0;
var keyEnter = 13;
sessionStorage.setItem("add_item_id", "-1");

$(function() {
	$.setRules({
			"product_upc": {
				"required": false
			},
			"product_unit_price": {
				"required": false,
				"format": "number",
			},
			"order_date": {
				"required": true
			},
			"payment_type": {
				"required": true
			},
			"supplier_code": {
				"required": true
			},
			"goods_in_to": {
				"required": true
			},
			"invoice_date": {
				"required": false
			},
			"invoice_no": {
				"required": false
			},
			"po_code": {
				"required": false
			},
			"consignment": {
				"required": false
			}
		});		
		
	$("#invoice_date").datepicker({
		changeMonth	: true,
		changeYear	: true,
		dateFormat	: 'yy-mm-dd',
		onSelect	: function() {
			$(this).blur();
			$(this).change();
		}
	})

	$("#order_date").datepicker({
		changeMonth	: true,
		changeYear	: true,
		dateFormat	: 'yy-mm-dd',
		onSelect	: function() {
			$(this).blur();
			$(this).change();
		}
	}).attr('readonly','readonly');

	/** Start product list Search */
	$("#product_upc").on("keyup", function( event ) {
		var keycode = ( event.keyCode ? event.keyCode : event.which );
		if ( keycode == 13 ) {
			showProductPopupList();
		}
	});	
	
	$("#show_product_popup_list").on("click", showProductPopupList);		

	$("#product_popup_list").on("dblclick", ".popup_list_item_row", function() {
		var product_id = $(this).find(".product_id_popup").val();
		$("#product_upc").val($(this).find(".col_upc").text());
		$("#product_name_text").text($(this).find(".col_name").text());
		$("#error_product_upc").empty();
		$("#product_popup_list").modal('hide');
		$("#product_qty").val(1);
		$(".get_product_id").val(product_id);
		$("#product_require_imei").text($(this).find(".col_required_imei").text());
	});	
	/** End product list Search */
	
	/** Start purchase order list Search */
	$("#po_code").on("keyup", function( event ) {
		var keycode = ( event.keyCode ? event.keyCode : event.which );
		if ( keycode == 13 ) {
			showPurchaseOrderPopupList();
		}
	});	
	
	$("#show_purchase_order_popup_list").on("click", showPurchaseOrderPopupList);	
	
	$("#purchase_order_popup_list").on("dblclick", ".popup_list_item_row", function() {
		var po_id = $(this).data("po_id");
		var po_supplier_id = $("#purchase_order_popup_list_supplier_id_"+po_id).val();
		var po_supplier_name = $("#purchase_order_popup_list_supplier_name_"+po_id).val();
		var po_supplier_mobile = $("#purchase_order_popup_list_supplier_mobile_"+po_id).val();
		var po_supplier_fax = $("#purchase_order_popup_list_supplier_fax_"+po_id).val();
		var po_supplier_email = $("#purchase_order_popup_list_supplier_email_"+po_id).val();	
		var po_code = $(this).find(".col_po_no").text();
		var po_supplier_code = $(this).find(".col_supplier").text();
		
		$("#po_code").val(po_code);
		$("#get_supplier_id").val(po_supplier_id);
		$("#supplier_code").val(po_supplier_code);
		$("#supplier_name").text(po_supplier_name);
		$("#supplier_mobile").text(po_supplier_mobile);
		$("#supplier_fax").text(po_supplier_fax);
		$("#supplier_email").text(po_supplier_email);
		
		if(po_id !== ''){
			 getPurchaseOrderItem(po_id);
		}
		$("#purchase_order_popup_list").modal('hide');
		resetCreateItem();
		
	});
	/** End purchase order list Search */
	
	/** Start supplier list Search */
	$("#supplier_code").on("keyup", function( event ) {
		var keycode = ( event.keyCode ? event.keyCode : event.which );
		if ( keycode == 13 ) {
			showSupplierPopupList();
		}
	});
	
	$("#show_supplier_popup_list").on("click", showSupplierPopupList);	
	
	$("#supplier_popup_list").on("dblclick", ".popup_list_item_row", function() {
		var mobile = $(this).find(".col_mobile").text() || "---";
		var fax = $(this).find(".col_fax").text() || "---";
		var email = $(this).find(".col_email").text() || "---";

		$("#supplier_code").val($(this).find(".col_code").text());
		$("#supplier_shop_name").text($(this).find(".col_name").text());
		$("#supplier_mobile").text(mobile);
		$("#supplier_fax").text(fax);
		$("#supplier_email").text(email);
		$("#get_supplier_id").val($(this).data("supplier_id"));
		$("#error_supplier").empty();
		$("#supplier_popup_list").modal('hide');
	});	
	/** End supplier list Search */
	
	$("#add_item_button").on("click", function( event ) {
		event.preventDefault();
		var product_found = true;
		var product_upc = getValue("product_upc");
		var product_qty = float2int(getValue("product_qty"));
		var product_unit_price = setFloat(getValue("product_unit_price"), 2);
        var product_total_amount = setFloat((product_qty * product_unit_price), 2);
        var product_id = $(".get_product_id").val();
        var require_imei = $("#product_require_imei").text();
		var product_name = "";
		var add_item_id = parseFloat(sessionStorage.add_item_id) + 1;
		var check_required = (require_imei == 'YES') ? 'data-toggle="collapse"' : '';
		
		var ok = $.checkFields("form_add_product_item");
		
		if ( product_upc === "" ) {	
			$("#error_product_upc").text("Required");
			ok = false;
		}
		
		if(product_unit_price === "NaN"){
			$("#error_product_unit_price").text("Required");
			ok = false;
		}		
		if ( !ok ) {
			return false;
		}
		
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "goods_in/getinfo",
            async: false,
            data: { product_upc:  product_upc }
        });


        jqxhr.done(function( product_info ) {
            if ( product_info === "" ) {
				$("#product_name_text").text("---");
                $("#error_product_upc").text("Product not found!");
                product_found = false;
            } else {
				sessionStorage.setItem("add_item_id", add_item_id);
				product_id = product_info.id;
				product_name = product_info.name;
			}
        });

	    if ( !product_found ) {
	    	return false;
	    }
	    $("#list_no_items_row").hide();
	    $("#product_name_text").text("---");
		
		var item_row = ' \
		<div class="list_item_row" data-item_id="'+ add_item_id  +'" '+ check_required +' data-target="#container'+ product_id +'" aria-expanded="false" aria-controls="container'+ product_id +'"> \
			<span class="list_checkbox_column col_check_row">---</span> \
			<span id="get_product_upc'+ product_id +'" class="list_column col_product_upc">' + product_upc + '</span> \
			<span class="list_column col_product_name">' + product_name + '</span> \
			<span class="list_column col_qty"><input type="number" data-idx="'+ product_id +'" data-item_row="'+ add_item_id +'" id="get_product_qty'+ product_id +'" class="form-control list_text_field list_product_qty num_item get_product_qty" data-num_flags="+i" value="' + product_qty + '"></span> \
			<span id="get_product_unit_price'+ product_id +'" class="list_column get_product_unit_price'+ product_id +'  col_unit_price">' + product_unit_price + '</span> \
			<span id="get_product_total'+ product_id +'" class="list_column col_item_total list_item_total">' + product_total_amount + '</span> \
			<span class="list_column col_require_imei_button col_require_imei">'+ require_imei +'</span> \
			<span class="list_column col_remove_button"><span class="list_delete_single_item_button glyphicon glyphicon-remove"></span></span> \
			<div class="clearfix"></div> \
			<div id="container' + product_id + '" class="noprop hidden_class collapse"> \
				<div class="fL gi_record_head" aria-expanded="false" aria-controls="container'+ product_id +'"> \
					<span class="list_column col_num_row">No.</span> \
					<span class="list_column col_item_row">UPC</span> \
					<span class="list_column col_serial_number_row">Serial Number</span> \
					<span class="list_column col_item_row">Qty</span> \
				</div> \
			</div> \
			<input type="hidden" id="get_type" name="get_type" value="add" /> \
			<input type="hidden" name="product_id" value="' + product_id + '" /> \
		</div>';
		
		$(".load_po_list").remove();
		$("#list_items").append(item_row);
		addSerialNumberItem(product_id, product_upc, product_qty, add_item_id);
		// $("#create_new_po").show();	
		
		resetCreateItem();
		
		$(".get_product_qty").on("keyup change blur", function(){
			var idx = $(this).data("idx");
			var add_item_row = $(this).data("item_row");
			changeQtyAddSerialNumberItemY(idx, add_item_row, idx)
		});			
		
		$(".noprop").on("click", function( event ) {
			event.stopPropagation();
		});				
		
        enable("#list_checkbox_all");
        enable("#list_delete_multi_item_button");
        enable("#discount_amount");
		$("#total_row_items").text(total_list_items);
        // resetAddItemFields();
        total_list_items++;
		calculateTotalQty();
        calculateTotalAmount();
		return false;
	});	
	
	$("#confirm_goods_in_button").on("click", function(){
		checkField();
	});

	$(".get_product_info_button").on("click", getProductSearchData);
	
	$(".get_po_info_button").on("click", getPurchaseOrderSearchData);	
	
	$(".get_supplier_info_button").on("click", getSupplierSearchData);
	
	$(".get_po_remove_button").on("click", function(){
		$(".load_po_list").remove();
		$("#po_code").val('');
		$("#get_supplier_id").val('');
		$("#supplier_code").val('');
		$("#supplier_name").text('');
		$("#supplier_mobile").text('');
		$("#supplier_fax").text('');
		$("#supplier_email").text('');
	});	
	
	$(".get_supplier_remove_button").on("click", function(){
		$("#supplier_code").val('');
		$("#get_supplier_id").val('');
		$("#supplier_code").val('');
		$("#supplier_name").text('');
		$("#supplier_mobile").text('');
		$("#supplier_fax").text('');
		$("#supplier_email").text('');
	});
	
	fnEnterSearch();
});
	
	function fnEnterSearch(){
		$(".product_upc_text_field").keypress(function(e) {
			if(e.which == keyEnter) {
				getProductSearchData();
			}
		});	

		$(".po_text_field").keypress(function(e) {
			if(e.which == keyEnter) {
				getPurchaseOrderSearchData();
			}
		});		
		
		$(".supplier_text_field").keypress(function(e) {
			if(e.which == keyEnter) {
				getSupplierSearchData();
			}
		});			
	}
	
	function getProductSearchData(){
		var keyword = $("#product_upc").val();
		var jqxhr = $.ajax({
			type: 'POST',
			url: ROOT + "get_goods_in_product",
			async: false,
			data: { keyword: keyword }
		});
		jqxhr.done(function(result) {
			var required_imei = (result.required_imei == 1) ? "YES" : "NO";
			$("#product_upc").val(result.barcode);
			$("#product_name_text").text(result.name);
			$("#error_product_upc").empty();
			$("#product_qty").val(1);
			$("#product_unit_price").val(result.unit_price);
			$("#product_require_imei").text(required_imei);
		});
	}	

	function getPurchaseOrderSearchData(){
		var keyword = $("#po_code").val();
		var jqxhr = $.ajax({
			type: 'POST',
			url: ROOT + "get_goods_in_purchase_order",
			async: false,
			data: { keyword: keyword }
		});
		jqxhr.done(function(result) {
			$("#po_code").val(result.purchase_order_number);
			$("#get_supplier_id").val(result.supplier_id);
			$("#supplier_code").val(result.supplier_code);
			$("#supplier_name").text(result.supplier_name);
			$("#supplier_mobile").text(result.supplier_mobile);
			$("#supplier_fax").text(result.supplier_fax);
			$("#supplier_email").text(result.supplier_email);
			
			var po_id = result.id;	
			if(po_id !== ''){
				 getPurchaseOrderItem(po_id);
			}
			resetCreateItem();
		});
	}
	
	function getSupplierSearchData(){
		var keyword = $("#supplier_code").val();
		var jqxhr = $.ajax({
			type: 'POST',
			url: ROOT + "get_goods_in_supplier",
			async: false,
			data: { keyword: keyword }
		});
		jqxhr.done(function(result) {
			$("#get_supplier_id").val(result.id);
			$("#supplier_code").val(result.code);
			$("#supplier_name").text(result.name);
			$("#supplier_mobile").text(result.mobile);
			$("#supplier_fax").text(result.fax);
			$("#supplier_email").text(result.email);
		});
	}

	function getPurchaseOrderItem(po_id) {
		var jqxhr = $.ajax({
			type: 'POST',
			url: ROOT + "get_po_item",
			async: false,
			data: { po_id: po_id }
		});
		jqxhr.done(function(result) {
			if ( result === "" ) {
				$("#list_no_items_row").show();
				$("#list_no_items_row").text("Purchase Order item not found!");
			} else {
				$(".list_item_row").remove();
				$("#list_no_items_row").hide();
				var po_item_list = result.po_item_list;
				$.each( po_item_list, function ( key, data ) {
					var product_id = data.product_id;
					var product_qty = data.product_qty;
					var product_upc = data.product_upc;
					var check_required = (data.required_imei == '1') ? 'data-toggle="collapse"' : '';
					var required_field = (data.required_imei == '1') ? 'YES' : 'NO';
					var item_row = ' \
					<div class="list_item_row load_po_list" data-item_id="'+ data.product_id  +'" ' + check_required + ' data-target="#container'+ data.product_id +'" aria-expanded="false" aria-controls="container'+ data.product_id +'"> \
						<span class="list_checkbox_column col_check_row">---</span> \
						<span id="get_product_upc'+ data.product_id +'" class="list_column col_product_upc">' + data.product_upc + '</span> \
						<span class="list_column col_product_name">' + data.product_name + '</span> \
						<span class="list_column col_qty"><input type="number" data-idx="'+ data.product_id +'" id="get_product_qty'+ data.product_id +'" class="form-control list_text_field list_product_qty num_item get_product_qty" data-num_flags="+i" value="' + data.product_qty + '"></span> \
						<span id="get_product_unit_price'+ data.product_id +'" class="list_column get_product_unit_price'+ data.product_id +' col_unit_price">' + data.product_unit_price + '</span> \
						<span id="get_product_total'+ data.product_id +'" class="list_column col_item_total list_item_total">' + data.product_total_price + '</span> \
						<span id="get_product_imei'+ data.required_imei +'" class="list_column col_require_imei list_require_imei col_require_imei_button">' + required_field + '</span> \
						<!--span class="list_column col_remove_button"><span class="list_delete_single_item_button glyphicon glyphicon-remove"></span></span--> \
						<input type="hidden" id="get_po_number" name="get_po_number" value="' + data.purchase_order_number + '" /> \
						<input type="hidden" id="get_type" name="get_type" value="load" /> \
						<div class="clearfix"></div> \
						<div id="container' + product_id + '" class="noprop hidden_class collapse"> \
							<div class="fL gi_record_head" aria-expanded="false" aria-controls="container'+ data.product_id +'"> \
								<span class="list_column col_num_row">No.</span> \
								<span class="list_column col_item_row">UPC</span> \
								<span class="list_column col_serial_number_row">Serial Number</span> \
								<span class="list_column col_item_row">Qty</span> \
							</div> \
						</div> \
					</div>';
					$("#list_items").append(item_row);
					total_list_items++;
					if(data.required_imei == '1'){
						addSerialNumberItem(product_id, product_upc, product_qty, product_id);
					}else{
						groupSerialNumberItem(product_id, product_upc, product_qty, product_id);
					}
					$("#get_product_qty"+data.product_id).on("keyup change blur", function(){
						var idx = $(this).data("idx");
						var check_imei = $(this).closest(".list_item_row").find(".list_require_imei").text();
						if(check_imei == 'Y'){
							changeQtyAddSerialNumberItemY(0, idx, data.product_id);
						}else{
							changeQtyAddSerialNumberItemN(0, idx, data.product_id);
						}
					});							
				});	
				$("#error_product_upc").text("");
				$("#error_product_unit_price").text("");
				$("#no_list_item_error_message").hide();
			}			
			
			$(".noprop").on("click", function( event ) {
				event.stopPropagation();
			});		
			
			calculateTotalAmount();
			calculateTotalQty();	
		});	
	}
	
	function addSerialNumberItem(product_id, product_upc, product_qty, add_item_id){
		var item_row = "";
		for(var i = 1; i <= product_qty; i++){
		// var count_item_id = parseFloat(sessionStorage.count_item_id)+ i;
		item_row += ' \
				<div class="fL w100 gi_record_list get_record_list' + product_id +'" data-item_id="' + add_item_id + '" data-product_id="' + product_id +'" > \
					<span class="list_column col_num_row">' + i + '</span> \
					<span class="list_column col_item_row">'+ product_upc +'</span> \
					<span class="list_column col_serial_number_row"><input class="form-control list_text_field num_item gi-item_sn" name="item_'+ product_id +'_sn['+ i +']" placeholder="Serial No."></span> \
					<span class="list_column col_item_row gi-item_qty">1</span> \
				</div>';
		}
		// sessionStorage.setItem("count_item_id", count_item_id);
		$("#container" + product_id).append(item_row);
	}	
	
	function groupSerialNumberItem(product_id, product_upc, product_qty, add_item_id){
		var item_row = "";
		var i = 1;
		item_row += ' \
				<div class="fL w100 gi_record_list get_record_list' + product_id +'" data-item_id="' + add_item_id + '" data-product_id="' + product_id +'"> \
					<span class="list_column col_num_row">' + i + '</span> \
					<span class="list_column col_item_row">'+ product_upc +'</span> \
					<span class="list_column col_serial_number_row"><input class="form-control list_text_field num_item gi-item_sn" name="item_'+ product_id +'_sn['+ i +']" placeholder="Serial No."></span> \
					<span class="list_column col_item_row gi-item_qty">'+ product_qty +'</span> \
				</div>';		
		$("#container" + product_id).append(item_row);
	}
	
	function changeQtyAddSerialNumberItemY(idx, add_item_id, product_id) {
		var get_type = $("#get_type").val();
		if(get_type==="load"){
			var idx = add_item_id;
		}
		if(get_type==="add"){
			var idx = idx;
		}
		var qty = float2int($("#get_product_qty"+idx).val());
		qty = Math.max(1, qty);		
		$("#get_product_qty"+idx).val(qty);
		$(".get_record_list"+idx).remove();
		
		var product_total = 0.00;
		var product_upc = $("#get_product_upc"+ idx).text();
		var product_qty = $("#get_product_qty"+ idx).val();
		var product_unit_price = $("#get_product_unit_price"+ idx).text();
		
		product_total +=  parseFloat(product_qty * product_unit_price);
		product_total = setFloat(product_total, 2);
		$("#get_product_total"+ idx).text(product_total);
		
		var item_row = "";
		for(var i = 1; i <= product_qty; i++){
		item_row += ' \
				<div class="fL w100 gi_record_list get_record_list' + idx +'" data-item_id="'+ add_item_id + '" data-product_id="' + product_id +'"> \
					<span class="list_column col_num_row">' + i + '</span> \
					<span class="list_column col_item_row">'+ product_upc +'</span> \
					<span class="list_column col_serial_number_row"><input class="form-control list_text_field num_item gi-item_sn" name="item_'+ idx +'_sn['+ i +']" placeholder="Serial No."></span> \
					<span class="list_column col_item_row gi-item_qty">1</span> \
				</div>';
		}
		$("#container" + idx).append(item_row);
		calculateTotalAmount();
		calculateTotalQty();			
	}
	
	function changeQtyAddSerialNumberItemN(idx, add_item_id, product_id) {
		var get_type = $("#get_type").val();
		if(get_type==="load"){
			var idx = add_item_id;
		}
		if(get_type==="add"){
			var idx = idx;
		}
		var qty = float2int($("#get_product_qty"+idx).val());
		qty = Math.max(1, qty);		
		$("#get_product_qty"+idx).val(qty);
		$(".get_record_list"+idx).remove();
		
		var product_total = 0.00;
		var product_upc = $("#get_product_upc"+ idx).text();
		var product_qty = $("#get_product_qty"+ idx).val();
		var product_unit_price = $("#get_product_unit_price"+ idx).text();
		
		product_total +=  parseFloat(product_qty * product_unit_price);
		product_total = setFloat(product_total, 2);
		$("#get_product_total"+ idx).text(product_total);
		
		var item_row = "";
		item_row += ' \
				<div class="fL w100 gi_record_list get_record_list' + idx +'" data-item_id="'+ add_item_id + '" data-product_id="' + product_id +'"> \
					<span class="list_column col_num_row">1</span> \
					<span class="list_column col_item_row">'+ product_upc +'</span> \
					<span class="list_column col_serial_number_row"><input class="form-control list_text_field num_item gi-item_sn" name="item_'+ idx +'_sn[1]" placeholder="Serial No."></span> \
					<span class="list_column col_item_row gi-item_qty">' + product_qty + '</span> \
				</div>';
		$("#container" + idx).append(item_row);
		calculateTotalAmount();
		calculateTotalQty();			
	}	
	
	function calculateTotalAmount() {
		var total_amount = 0.00;
		$(".list_item_total").each(function() {
			total_amount += parseFloat($(this).text());
		});
		total_amount = setFloat(total_amount, 2);
		$("#total_amount_value").text(total_amount);
	}	
	
	function calculateTotalQty() {
		var total_qty = 0;
		$(".get_product_qty").each(function() {
			total_qty += parseInt($(this).val());
		});
		$("#total_qty_value").text(total_qty);
	}		
	
	function createPurchaseOrderItem() {
	
        var supplier_id = getValue("get_supplier_id");
        var ok = true;
		var i = 0;
        $("#error_supplier").text("");
        $("#form_add_product_item .form-error_message").hide();

        var ans = window.confirm("Confirm to submit the purchase order?");
        if ( !ans ) {
        	return false;
        }

        var POItems = new Object();
        var poItemsInfo = new Object();
		
        $(".add_po_list").each(function() {
            POItems[i] = new Object();
            POItems[i].product_id = $(this).find("input[name='product_id']").val();
            POItems[i].qty = $(this).find(".list_product_qty").val();
            POItems[i].unit_price = $(this).find(".col_unit_price").text();
			POItems[i].row_index = i;
            i++;
        });
        poItemsInfo.POItems = POItems;
        poItemsInfo.discount_amount = '0';
        poItemsInfo.order_date = $("#order_date").val();
        poItemsInfo.staff_code = $("#staff_code").val();
        poItemsInfo.deposit_no = $("#deposit_no").val();
        poItemsInfo.ship_to = $("#ship_to").val();
        poItemsInfo.request_by = $("#request_by").val();
        poItemsInfo.payment_type = $("#payment_type").val();
        poItemsInfo.supplier_id = supplier_id;
        poItemsInfo.remarks = $("#remarks").val();
        poItemsInfo.status = '1';
		
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "purchase_order/create",
            async: false,
            data: poItemsInfo
        });

        jqxhr.done(function(result) {
            result = JSON.parse(result);
            if ( result.status == "success" ) {
				var new_po_id = result.purchase_order_id;
				var new_po_number = result.purchase_order_number;
				var new_po_item_id = result.purchase_order_item_id;
				createPOGoodsInItemNew(new_po_id, new_po_number,new_po_item_id, supplier_id);
            }
            $("#create_" + result.status + "_result").show();
        });
	}
	
	function createPOGoodsInItemNew(new_po_id, new_po_number,new_po_item_id, new_supplier_id){
		var get_po_id = new_po_id;
		var get_po_number = new_po_number;
		var get_po_item_id = new_po_item_id;
		var get_supplier = new_supplier_id;
		var get_type = $("#get_type").val();
		var formObject = new Object();
		var itemObject = new Object();
		var c = 0;
		
		$(".gi_record_list").each(function() {
			var po_item_id = $(this).data("item_id");
			var product_id = $(this).data("product_id");
			var serial_number = $(this).find(".gi-item_sn").val();
			var qty = '1';
			itemObject[c] = new Object();
			itemObject[c].po_id = get_po_id;
			itemObject[c].product_id = product_id;
			itemObject[c].po_item_id = po_item_id;
			itemObject[c].serial_number = serial_number;
			itemObject[c].qty = qty;
			itemObject[c].get_type = get_type;
            c++;
		});
		formObject.gi_items = itemObject;
		formObject.po_number = get_po_number;
		formObject.supplier = get_supplier;
        formObject.goods_in_to = getValue("goods_in_to");
        formObject.consignment = getValue("consignment");
        formObject.invoice_no = getValue("invoice_no");
        formObject.invoice_date = getValue("invoice_date");
		formObject.remarks = getValue("remarks");
		
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "goods_in/create",
            async: false,
            data: formObject
        });

        jqxhr.done(function(result) {
            result = JSON.parse(result);
            if (result.status == "success") {
                window.open(ROOT + "goods_in/print/" + result.goods_in_id, 'Download');
				resetPage();		
                $("#resultPage .modal-body").html('<div class="alert alert-success" role="alert">成功輸出報告</div>');
                $("#resultPage").modal();
				$("#btn-confirm-now").on("click", function(event) {
					window.location = ROOT + 'goods_in/list';
				});
				resetCreateItem();
            }
        });
	}	
	
	function createGoodsInItem(create_type){
		var ans = window.confirm("確認要提交表單嗎?");
		if ( !ans ) {
			return false;
		}
		
		var get_po_number = $("#get_po_number").val();
		var get_supplier = $("#get_supplier_id").val();
		var get_type = $("#get_type").val();
		
		var formObject = new Object();
		var itemObject = new Object();
		var c = 0;
		get_po_number = (typeof(get_po_number) != "undefined" ? get_po_number : "");
		
		$(".gi_record_list").each(function() {
			var po_item_id = $(this).data("item_id");
			var product_id = $(this).data("product_id");
			var serial_number = $(this).find(".gi-item_sn").val();
			var qty = $(this).find(".gi-item_qty").text();
			var unit_price = $(".get_product_unit_price" + product_id).text();
			itemObject[c] = new Object();
			itemObject[c].po_item_id = po_item_id;
			itemObject[c].create_product_id = product_id;
			itemObject[c].serial_number = serial_number;
			itemObject[c].create_unit_price = unit_price;
			itemObject[c].qty = qty;
			itemObject[c].get_type = get_type;
            c++;
		});
		formObject.gi_items = itemObject;
		formObject.po_number = get_po_number;
		formObject.supplier = get_supplier;
        formObject.goods_in_to = getValue("goods_in_to");
        formObject.consignment = getValue("consignment");
        formObject.invoice_no = getValue("invoice_no");
        formObject.invoice_date = getValue("invoice_date");
		formObject.remarks = getValue("remarks");
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "goods_in/create",
            async: false,
            data: formObject
        });

        jqxhr.done(function(result) {
            result = JSON.parse(result);
            if (result.status == "success") {
                window.open(ROOT + "goods_in/print/" + result.goods_in_id, 'Download');
                $("#resultPage .modal-body").html('<div class="alert alert-success" role="alert">成功輸出報告</div>');
                $("#resultPage").modal();
				$("#btn-confirm-now").on("click", function(event) {
					window.location = ROOT + 'goods_in/list';
				});
				location.reload();
            }
        });
	}
	
	function checkField() {
		var ok = true;
		var require_imei = true;
		var check_goods_in_to = getValue("goods_in_to");
		var check_invoice_date = getValue("invoice_date");
		var check_invoice_no = getValue("invoice_no");
		var check_supplier_code = getValue("supplier_code");
		var check_order_date = getValue("order_date");
		total_list_items = $(".list_item_row").length;
		var get_type = $("#get_type").val();			
		
		if ( total_list_items < 1 ) {
			ok = false;
			$("#no_list_item_error_message").show();
		}
			
		if(check_goods_in_to === ""){
			ok = false;
			$("#error_goods_in_to").text('Required');
		}		
		if(check_supplier_code === ""){
			ok = false;
			$("#error_supplier_code").text('Required');
		}
		
		$(".list_item_row").each(function() {
			var require_imei_text = $(this).find(".col_require_imei_button").text();
			if(require_imei_text == 'YES'){
				$(this).find('.gi_record_list').each(function() {
					var serial_number_text = $(this).find('.col_serial_number_row > .gi-item_sn').val();
					console.log(serial_number_text);
					if(serial_number_text === ""){
						console.log(serial_number_text);
						require_imei = false;
					}
				});
			}
		});
		
		if ( !ok ) {
			alert("The form contains error\nPlease correct the errors before submit the form");
			return false;
		}else if(!require_imei){
			alert("Please enter Serial Number for require imei is YES");
			return false;
		}else{	
			if(get_type=="load"){
				createGoodsInItem(get_type);
			}
			if(get_type=="add"){
				// createPurchaseOrderItem();
				createGoodsInItem(get_type);
			}	
		}
		return true;		
	}
	
	function resetCreateItem() {
		$(".product_upc").val('');
		$(".product_upc_text_field").val('');
		$("#product_qty").val('');
		$("#product_unit_price").val('');
		$("#product_name_text").text('');
		$("#product_require_imei").text('');
	}

	function resetList() {
		sessionStorage.setItem("add_item_id", "-1");
		total_list_items = 0;
		total_amount = 0.00;
		$("#no_list_item_error_message").hide();
		$("#net_total_error_message").hide();
		$("#list_no_items_row").show();
		
		//left coloum
		$("#total_amount_value").text("0.00");
		$("#net_total_amount_value").text("0.00");
		$("#list_items").empty();
		$("#total_row_items").text(0);
		$("#total_qty").text(0);
		
		//right coloum po
		$("#create_new_po").hide();	
	}	
	
	function resetPage() {
		resetList();
		resetCreateItem();
		$("#form_goods_in")[0].reset();
		$("#form_supplier")[0].reset();
		$(".list_item_row").remove();
		$("#list_no_items_row").show();
		$("#po_code").val('');
		$("#remarks").val('');
		$("#supplier_name").text("---");
		$("#supplier_mobile").text("---");
		$("#supplier_fax").text("---");
		$("#supplier_email").text("---");
	}
	
	function removeSingleItem( obj ) {
		obj.closest(".list_item_row").remove();
		calculateTotalAmount();
		total_list_items--;
		add_item_id = parseFloat(sessionStorage.add_item_id)-1
		if(add_item_id < "0"){
			add_item_id = 0;
		}
		sessionStorage.setItem("add_item_id", add_item_id);
		changeItemID();
		if ( total_list_items < 1 ) {
			resetList();
			resetCreateItem();
		}
		$("#total_row_items").text(total_list_items);
	}
	
	function changeItemID(){
		var changeRowItemData = 0;
		var changeRecordItemData = 0;
		$(".list_item_row").each(function() {	
			changeRowItemData = $(this).data("item_id") -1;
			if(changeRowItemData < "0"){
				changeRowItemData = "0";
			}
			$(this).attr("data-item_id", changeRowItemData);
		});
		$(".gi_record_list").each(function() {	
			changeRecordItemData = $(this).data("item_id") -1;
			if(changeRecordItemData < "0"){
				changeRecordItemData = "0";
			}			
			$(this).attr("data-item_id", changeRecordItemData);
		});					
	}
	
	function showProductPopupList() {
		var search_product = getValue("product_upc");
		$("#search_product_upc").val(search_product);
		updateList("product_popup_list");
		$("#product_popup_list").modal();
	}
	
	function showPurchaseOrderPopupList() {
		var search_po = getValue("po_code");
		$("#search_purchase_order_code").val(search_po);
		updateList("purchase_order_popup_list");
		$("#purchase_order_popup_list").modal();
	}
	
	function showSupplierPopupList() {
		var search_supplier = getValue("supplier_code");
		$("#search_supplier_code").val(search_supplier);
		updateList("supplier_popup_list");
		$("#supplier_popup_list").modal();
	}