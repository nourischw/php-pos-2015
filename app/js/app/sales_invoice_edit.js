$(function(){
	var p = 0;
	var c = 0;
	var formObject = new Object();
	var cartObject = new Object();
	var paymentObject = new Object();

	var cartObject_new = new Object();
	var paymentObject_new = new Object();
	var remove_payment_item_id = [];
	
	var keyEnter = 13;

	$(".search_status_button").on("click", function(){
		var status = $(this).data("status");
		$("#quick_search_type").val(status);
	});

	$("#get_product_info_button").on("click", getQuickSearchItem);

	$("#select_from_shop_button").on("click", function() {
		var from_shop_id = getValue("from_shop");
		var from_shop_name = $("#from_shop option:selected").text();
		$("#item_already_exist_message").hide();
		$("#from_shop_id, #search_from_shop").val(from_shop_id);
		$("#from_shop_name_text, #from_shop_name").text(from_shop_name);
		$("#select_from_shop_block").hide();
		$("#from_shop_info").show();
		enable("#show_stock_popup_list_button");

		enable("#to_shop option");
		disable("#to_shop option[value='" + from_shop_id + "']");
		enable("#to_shop");
		updateList("stock_popup_list");
		check_shop_code();
	});

	$("#change_from_shop_button").on("click", function() {
		var message = "Warning!\nChange From shop will clear all stock items in the list\nAre you sure to change the from shop?"
		var ans = window.confirm(message);

		if (ans) {
			disable("#to_shop");
			disable("#search_stock_popup_list_button");
			$("#from_shop_id, #search_from_shop").val("");
			$("#from_shop_name_text, #from_shop_name").text("---");
			$("#select_from_shop_block").show();
			$("#from_shop_info").hide();
		}
	});

	$("#select_from_shop_button").click();
	
	/** Start Stock produc Search */
	$("#quick_search").on("keyup", function( event ) {
		var keycode = ( event.keyCode ? event.keyCode : event.which );
		if ( keycode == 13 ) {
			showStockPopupList();
		}
	});

	$("#show_stock_popup_list_button").on("click", showStockPopupList);			

	$("#stock_popup_list").on("dblclick", ".popup_list_item_row",  function() {
		resetProductInformtaion();
		var stock_id = parseInt($(this).data("stock_id"));
		var product_id = parseInt($(this).data("product_id"));

		var product_upc = $(this).find(".col_product_upc").text();
		var product_name = $(this).find(".col_product_name").text();
		var serial_number = $(this).find(".col_serial_number").text();
		var serial_number_text = (serial_number !== "---") ? "S/N: " + serial_number : "";
		var stock_qty = parseInt($(this).find(".col_qty").text());
		var product_unit_price = $(this).find(".col_unit_price").text();

		getProductTotalPrice(product_unit_price);
		$("#stock_id").val(stock_id);
		$("#product_id").val(product_id);
		$("#product_code").text(product_upc);
		$("#serial_number").text(serial_number);
		$("#product_name").val(product_name);
		$("#product_unit_price").text("$" + product_unit_price);
		$("#have_qty").text("Qty (" + stock_qty + "):")
		$("#check_total_qty").val(stock_qty);
		$("#product_discount").val('0');
		$("#error_product_upc").empty();
		$("#product_qty").val(1);

		$("#stock_popup_list").modal('hide');
	});
	/** End Stock produc Search **/
	
	/** Start Quotation produc Search */
	$("#quotation_code").on("keyup", function( event ) {
		var keycode = ( event.keyCode ? event.keyCode : event.which );
		if ( keycode == 13 ) {
			showQuotationPopupList();
		}
	});	
	
	$("#show_quotation_popup_list_button").on("click", showQuotationPopupList);	
	
	$("#quotation_popup_list").on("dblclick", ".popup_list_item_row", function(){
		var current_shop_code = $("#from_shop_name_text").text();
		var quotation_id = $(this).data("quotation_id");
		var quotation_number = $(this).find(".col_quotation_number").text();
		var quotation_payment_amount = $(this).find(".col_quotation_sub_total_amount").text();
		var select_shop_code = $(this).find(".col_shop_code").text();
		
		if(current_shop_code != select_shop_code){
			alert('This quotation item is not your current selected shop');
			return;
		}		
		
		var jqxhr = $.ajax({
			type: "POST",
			url: ROOT + "quotation/get_quotation_items",
			async: false,
			data: { 
				quotation_id: quotation_id,
				shop_code: current_shop_code 
			}
		});

		jqxhr.done(function( quotation_items ) {
			if(quotation_items != 0){
				var len = quotation_items.length;
				var item_row = "";
				if (len > 0) {
					for (var i = 0; i < len; i++) {
						var item = quotation_items[i];
						var index = i + 1;
						var stock_qty = item.stock_qty;
						var item_color = (stock_qty != 0 ) ? "quotation_item" : "quotation_item out_of_stock_item";
						var item_type = (stock_qty != 0 ) ? "Q" : "O";								
						 item_row = ' \
						<div class="list_item_row ' + item_color + '" data-quotation-id="'+ quotation_id +'" data-item_id="' + c + '" data-product-id="' + item.product_id + '"> \
							<input type="hidden" id="check_shop_item" class="check_shop_item" value="'+item.code+'"> \
							<span class="list_column col_no_row">' + item_type + '</span> \
							<span class="list_column col_product_upc get_product_upc">' + item.barcode + '</span> \
							<span class="list_column col_product_name get_product_name">' + item.product_name + '</span> \
							<span class="list_column col_serial_number get_serial_number">---</span> \
							<span class="list_column col_product_unit_price get_product_unit_price">$' + item.unit_price + '</span> \
							<input type="number" class="list_column col_product_qty get_product_qty" value="' + item.qty + '" disabled> \
							<input type="number" class="list_column col_product_discount get_product_discount" value="0" disabled> \
							<span class="list_column col_product_total count_product_total">$' + item.total_price + '</span> \
							<input type="hidden" class="quotation_item_price" value="' +item.total_price+ '"> \
						</div>';
						$("#list_items").append(item_row);
					}
					
					$(".quotation_pop_btn").hide();
					$("#cal_quotation_id").val(quotation_id);
					$("#quotation_code").val(quotation_number);
					$("#cal_quotation_payment_amount").val(quotation_payment_amount);				
					
					removeItems();
					calculateTotalAmount();
					calculateNetTotalAmount();
					$("#no_list_item_error_message").hide();
					
					var payment_total_price = parseInt($("#payment_list_total").val());
					var net_total_price = parseInt($("#net_total_amount_value").text());
					if(payment_total_price > net_total_price){
						$("#no_payment_list_over_error_message").show();
					}else{
						$("#no_payment_list_over_error_message").hide();
					}
					$("#quotation_popup_list").modal('hide');
				}
			}else{
				alert('Stock no this item record');
			}
		});
	});	
	/** End Quotation produc Search **/	
	
	/** Start Deposit produc Search */
	$("#deposit_code").on("keyup", function( event ) {
		var keycode = ( event.keyCode ? event.keyCode : event.which );
		if ( keycode == 13 ) {
			showDepositPopupList();
		}
	});		
	
	$("#show_deposit_popup_list_button").on("click", showDepositPopupList);
	
	$("#deposit_popup_list").on("dblclick", ".popup_list_item_row", function(){
		var current_shop_code = $("#from_shop_name_text").text();
		var deposit_id = $(this).data("deposit_id");
		var deposit_number = $(this).find(".col_deposit_number").text();
		var deposit_payment_amount = $(this).find(".col_deposit_payment_amount").text();
		var select_shop_code = $(this).find(".col_shop_code").text();
		
		if(current_shop_code != select_shop_code){
			alert('This despoit item is not your current selected shop');
			return;
		}
		
		var jqxhr = $.ajax({
			type: "POST",
			url: ROOT + "deposit/get_deposit_items",
			async: false,
			data: { 
				deposit_id: deposit_id, 
				shop_code: current_shop_code 
			}
		});

		jqxhr.done(function( deposit_items ) {
			if(deposit_items != 0){
				var len = deposit_items.length;
				var item_row = "";
				if (len > 0) {
					for (var i = 0; i < len; i++) {
						var item = deposit_items[i];
						var index = i + 1;
						var stock_qty = item.stock_qty;
						var item_color = (stock_qty != 0 ) ? "deposit_item" : "deposit_item out_of_stock_item";
						var item_type = (stock_qty != 0 ) ? "D" : "O";					
						 item_row = ' \
						<div class="list_item_row '+item_color+'" data-deposit-id="'+ deposit_id +'" data-item_id="' + c + '" data-product-id="' + item.product_id + '"> \
							<span class="list_column col_no_row">'+ item_type +'</span> \
							<span class="list_column col_product_upc get_product_upc">' + item.barcode + '</span> \
							<span class="list_column col_product_name get_product_name">' + item.product_name + '</span> \
							<span class="list_column col_serial_number get_serial_number">---</span> \
							<span class="list_column col_product_unit_price get_product_unit_price">$' + item.unit_price + '</span> \
							<input type="number" class="list_column col_product_qty get_product_qty" value="' + item.qty + '" disabled> \
							<input type="number" class="list_column col_product_discount get_product_discount" value="0" disabled> \
							<span class="list_column col_product_total count_product_total">$' + item.total_price + '</span> \
							<input type="hidden" class="deposit_item_price" value="' +item.total_price+ '"> \
						</div>';
						$("#list_items").append(item_row);
					}
					$(".total_deposit_row").show();
					$(".deposit_pop_btn").hide();
					$("#cal_deposit_id").val(deposit_id);
					$("#deposit_code").val(deposit_number);
					$("#cal_deposit_payment_amount").val(deposit_payment_amount);

					removeItems();
					calculateTotalAmount();
					calculateNetTotalAmount();
					$("#no_list_item_error_message").hide();
					
					var payment_total_price = parseInt($("#payment_list_total").val());
					var net_total_price = parseInt($("#net_total_amount_value").text());
					if(payment_total_price > net_total_price){
						$("#no_payment_list_over_error_message").show();
					}else{
						$("#no_payment_list_over_error_message").hide();
					}
					$("#deposit_popup_list").modal('hide');
				}
			}else{
				alert('Stock no this item record');
			}
		});
	});
	/** End Despoit produc Search **/	
	
	$("#product_qty").on("change blur", function(){
		var product_qty = float2int($("#product_qty").val());
		var product_discount = $("#product_discount").val();
		var total_qty = $("#check_total_qty").val();

		product_qty = Math.max(1, product_qty);
		$("#product_qty").val(product_qty);
		product_qty = $("#product_qty").val();

		if(parseInt(product_qty) > parseInt(total_qty)){
			$("#product_qty").val(total_qty);
			product_qty = $("#product_qty").val();
		}
		calculationTotalPrice(product_qty, product_discount);
	});

	$("#product_discount").on("change keyup blur", function(){
		var product_qty = $("#product_qty").val();
		var product_discount = $("#product_discount").val();

		product_discount = Math.max(0, product_discount);
		$("#product_discount").val(product_discount);
		product_discount = $("#product_discount").val();

		calculationTotalPrice(product_qty, product_discount);
	});

	$("#product_btn_add").on("click", function(){
		var check_field = true;
		var error_message = "";
		var list_item_row = $(".list_item_row").length;
		var stock_id = $("#stock_id").val();
		var product_id = $("#product_id").val();
		var product_code = $("#product_code").text();
		var product_serial_number = $("#serial_number").text();
		var product_name = $("#product_name").val();
		var product_unit_price = $("#product_unit_price").text();
		var product_total_price = $("#product_total_price").text();
		var product_qty = $("#product_qty").val();
		var product_discount = $("#product_discount").val();
		var check_total_qty = $("#check_total_qty").val();

		if(product_code == "---"){
			error_message += ("Warning! Product UPC is empty \n");
			check_field = false;
		}

		if(product_qty == ""){
			error_message +=("Warning! Product QTY is empty \n");
			check_field = false;
		}

		if(product_unit_price == "---"){
			error_message += ("Warning! Product Unit Price is empty \n");
			check_field = false;
		}

		if(product_total_price == "---"){
			error_message += ("Warning! Product Total Price is empty \n");
			check_field = false;
		}

    if ( !check_field ) {
        alert(error_message);
        return false;
    }

		var item_row = ' \
			<div class="list_item_row" data-item_id="" data-stock-id="'+ stock_id + '" data-product-id="' + product_id + '"> \
				<span class="list_column col_no_row">N</span> \
				<span class="list_column col_product_upc get_product_upc">' + product_code + '</span> \
				<span class="list_column col_product_name get_product_name">' + product_name + '</span> \
				<span class="list_column col_serial_number get_serial_number">' + product_serial_number + '</span> \
				<span class="list_column col_product_unit_price get_product_unit_price">' + product_unit_price + '</span> \
				<input type="number" class="list_column col_product_qty get_product_qty" value=' + product_qty + '> \
				<input type="number" class="list_column col_product_discount get_product_discount" value='+ product_discount +'> \
				<span class="list_column col_product_total count_product_total">' + product_total_price + '</span> \
				<span class="list_column col_remove_button"><span class="list_delete_single_item_button glyphicon glyphicon-remove"></span></span> \
				<input type="hidden" class="product_total_qty" value='+ check_total_qty +'> \
			</div>';
		$("#list_items").append(item_row);
		editProductItem();
		calculateTotalAmount();
		calculateNetTotalAmount();
		resetProductInformtaion();
		removeItems();
		$("#no_list_item_error_message").hide();
	});

	$("#add_payment_type").on("click", function(){
		var payment_type_id = $("#payment_type :selected").val();
		var payment_type_text = $("#payment_type :selected").text();
		var count_payment_option = $("#payment_type option").length;
		var payment_option_id = parseInt(payment_type_id) + 1;
		var changeSelectID = ( payment_option_id <= count_payment_option ) ? payment_option_id : 1;
		var item_row = ' \
			<div id="payment_list_row" class="fL payment_list_row" data-payment-id="" data-payment-type-id="'+ payment_type_id +'"> \
				<label class="fL payment_type_type_field">' + payment_type_text + '</label> \
					<label class="fL payment_type_amt_field"> \
						<input type="text" class="product_text_field_spec form-control payment_type_amount payment_type_amount_text_field" data-payment-num="' + p + '"> \
						<span class="payment_type_amount payment_type_amount_text" data-payment-num="' + p + '" style="display: none;"></span> \
					</label> \
					<label class="fL payment_type_no_field"> \
						<span class="payment_list_action_button payment_list_ok_single_item_button glyphicon glyphicon-ok"></span> \
						<span class="payment_list_action_button payment_list_edit_single_item_button glyphicon glyphicon-pencil" style="display: none;"></span> \
					</label> \
					<label class="fL payment_type_no_field"><span class="payment_list_action_button payment_list_delete_single_item_button glyphicon glyphicon-remove"></span></label> \
					<input type="hidden" id="payment_type_type_id" class="payment_type_type_id" value="'+ payment_type_id +'"> \
			</div> \
		';
		if($("#payment_type option:selected").is(':enabled')){
			$(".payment_list").append(item_row);
			$("#payment_type option[value="+ changeSelectID + "]").attr('selected','selected');
		}
		$("#payment_type option[value="+ payment_type_id + "]").attr('disabled','disabled');
		p++;
		payment_list_action_button();
	});

	$("#total_discount_button").on("click", function(){
		var get_discount_type = $("input[name=discount_type]:checked").val();
		var discount_amount = $("#total_discount_amount").val();
		var total_deposit_value = $("#total_deposit_value").text();
		var total_amount_value = $("#total_amount_value").text();
		var total_price = total_amount_value - total_deposit_value;

		if(discount_amount > total_price){
			alert("Not enought price to use discount!");
			resetDiscount();
		}else{
			if(get_discount_type == "1"){
				$("#total_discount_value").text("-$" + discount_amount);
				$("#cal_discount_type").val(get_discount_type);
				resetDiscount();
			}
			if(get_discount_type == "2"){
				$("#total_discount_value").text("-" + discount_amount + "%");
				$("#cal_discount_type").val(get_discount_type);
				resetDiscount();
			}
		}
		calculateNetTotalAmount();
	});

	$("#reset_discount_button").on("click", function(){
		$("#total_discount_status").val("");
		$("#total_discount_amount").val("");
		$("#total_discount_value").text("---");
		$("#cal_discount_type").val("");
		calculateNetTotalAmount();
	});
	
	$("#reset_deposit_button").on("click", function(){
		$(".total_deposit_row").hide();
		$(".deposit_pop_btn").show();
		$(".deposit_item").remove();
		$("#total_deposit_value").text("0.00");
		$("#deposit_code").val("");
		$("#cal_deposit_id").val("");
		$("#cal_deposit_payment_amount").val("");
		calculateTotalAmount();
		calculateNetTotalAmount();
	});
	
	$("#reset_quotation_button").on("click", function(){
		$(".quotation_item").remove();
		$(".quotation_pop_btn").show();
		$("#total_quotation_value").text("0.00");
		$("#quotation_code").val("");
		$("#cal_quotation_id").val("");
		$("#cal_quotation_payment_amount").val("");
		calculateTotalAmount();
		calculateNetTotalAmount();
	});

	$("#confirm_btn").on("click", function(){
		setStatusBtn('1');
		checkConfirmField();
	});

	$("#temp_save_btn").on("click", function(){
		setStatusBtn('2');
		checkConfirmField();
	});

	$("#void_btn").on("click", function(){
		var ans = window.confirm("Are you sure void this order?");
		if ( !ans ) {
			return false;
		}	
		var sales_invoice_edit_id = $("#sales_invoice_edit_id").val();
		var void_status = '3';
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "sales_invoice/update_si_status",
            async: false,
            data: { 
				record_id : sales_invoice_edit_id,
				status: void_status
			}
        });

        jqxhr.done(function( result ) {
			if(result == '1'){
				location.href= ROOT + "sales_invoice/list";
			}else{
				var error = window.confirm("Void This Invocie Fail");
			}
		});
	});
	
	$("#btn-pay-now").on("click", checkPaymentConfirmField);

	function getQuickSearchItem(){
		var i = 0;
		var shop_code = $("#from_shop_name_text").text();
		var status = $("#quick_search_type").val();
		var quick_search = $("#quick_search").val();
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "sales_invoice/get_quick_item",
            async: false,
            data: {
				quick_shop_code	:	shop_code,
				quick_search	:	quick_search,
				quick_status	:	status
			}
        });

        jqxhr.done(function( result ) {
			data = $.parseJSON(result);
			$.each(data, function(){

				if(data[i].serial_number == null){
					data[i].serial_number = "---";
				}

				getProductTotalPrice(data[i].unit_price);
				$("#product_id").val(data[i].product_id);
				$("#product_code").text(data[i].product_upc);
				$("#serial_number").text(data[i].serial_number);
				$("#product_name").val(data[i].product_name);
				$("#product_unit_price").text("$" + data[i].unit_price);
				$("#have_qty").text("Qty (" + data[i].remain_qty + "):")
				$("#check_total_qty").val(data[i].remain_qty);
				$("#error_product_upc").empty();
				$("#product_qty").val(1);

				i++;
			});
        });
	};

	function resetProductInformtaion(){
		$("#product_code").text("---");
		$("#serial_number").text("---");
		$("#product_name").val("");
		$("#product_unit_price").text("---");
		$("#have_qty").text("Qty (?)");
		$("#check_total_qty").val("");
		$("#product_qty").val("");
		$("#product_discount").val("");
		$("#product_discount_price").val("");
		$("#product_total_price").text("---");
		$("#quick_search").val("");
	}

	function removeItems(){
		var remove_item_id = [];
		$(".list_delete_single_item_button").on("click", function(event){
			event.preventDefault();
			var obj = $(this).closest(".list_item_row");
			var item_id = obj.data("item_id");
			if(item_id != ""){
				remove_item_id.push(item_id);
				$(".remove_item_ids").val(remove_item_id);
			}
			obj.remove();
			calculateTotalAmount();
			calculateNetTotalAmount();
		});
	}

	function getProductTotalPrice(product_unit_price){
		var product_discount = $("#product_discount").val();
		var product_qty = $("#product_qty").val();

		if(product_qty == ""){
			product_qty = 1;
		}

		if(product_discount!=""){
			var product_total_price = (product_unit_price * product_qty) - product_discount;
			product_total_price = product_total_price.toFixed(2);
		}else{
			var product_total_price = (product_unit_price * product_qty);
			product_total_price = product_total_price.toFixed(2);
		}
		$("#product_total_price").text("$" + product_total_price);

	}

	function editProductItem(){
		$(".get_product_qty").on("change blur", function(event){
			event.preventDefault();
			var obj = $(this);
			getEditProductItem(obj);
		});

		$(".get_product_discount").on("change blur", function(event){
			event.preventDefault();
			var obj = $(this);
			getEditProductItem(obj);
		});
	}

	function getEditProductItem(obj){
			var product_item = obj.prevAll();
			var product_unit_price = product_item.closest(".list_item_row").find(".get_product_unit_price").text();
			var product_qty = product_item.closest(".list_item_row").find(".get_product_qty").val();
			var product_discount = product_item.closest(".list_item_row").find(".get_product_discount").val();
			var total_qty = product_item.closest(".list_item_row").find(".product_total_qty").val();
			var product_total = product_item.closest(".list_item_row").find(".count_product_total");

			product_qty = Math.max(1, product_qty);
			product_item.closest(".list_item_row").find(".get_product_qty").val(product_qty);
			product_discount = Math.max(0, product_discount);
			product_item.closest(".list_item_row").find(".get_product_discount").val(product_discount);

			if(parseInt(product_qty) > parseInt(total_qty)){
				product_item.closest(".list_item_row").find(".get_product_qty").val(total_qty);

							product_qty = product_item.closest(".list_item_row").find(".get_product_qty").val();
			}

			product_qty = product_item.closest(".list_item_row").find(".get_product_qty").val();
			product_discount = product_item.closest(".list_item_row").find(".get_product_discount").val();

			calculationEditTotalPrice(product_qty, product_discount, product_unit_price, product_total);
	}
	
	function checkPreTotalPrice(){
		var product_qty = $("#product_qty").val();
		var product_discount = $("#product_discount").val();
			product_discount = parseFloat(product_discount).toFixed(2);
		var product_unit_price = $("#product_unit_price").text();
			product_unit_price = product_unit_price.split("$");
			product_unit_price = product_unit_price[1];
		var product_total_price = product_unit_price * product_qty;

		var new_total_price = 0;

		if(product_discount > product_total_price){
			product_discount = product_total_price;
			 $("#product_discount").val(product_discount);

			 if(product_total_price < 0){
				$("#product_total_price").text("$0.00");
			}
		}
	}

	function calculationTotalPrice(qty, discount){
		var product_unit_price = $("#product_unit_price").text();

		if(product_unit_price !== "---"){
			product_unit_price = product_unit_price.split('$');
			product_unit_price = product_unit_price[1];
			var product_total_price = (product_unit_price * parseInt(qty)) - discount;
			product_total_price = product_total_price.toFixed(2);
			$("#product_total_price").text("$" + product_total_price);
			$("#product_discount_price").val("$" + discount);
		}
		checkPreTotalPrice();
	}

	function calculationEditTotalPrice(qty, discount, unit_price, total_field){
		if(unit_price !== ""){
			product_unit_price = unit_price.split('$');
			product_unit_price = product_unit_price[1];
			var product_total_price = (product_unit_price * parseInt(qty));
			var count_total_price = 0.00;
			if(product_total_price >= discount){
				count_total_price = (product_total_price) - discount;
				count_total_price = count_total_price.toFixed(2);
			}
			total_field.text("$" + count_total_price);
		}
		calculateTotalAmount();
		calculateNetTotalAmount();
	}

	function calculateTotalAmount() {
		var total_amount = 0.00;
		$(".count_product_total").each(function() {
			get_total_amount = $(this).text();
			get_total_amount = get_total_amount.split("$");
			get_total_amount = get_total_amount[1];
			total_amount += parseFloat(get_total_amount);
		});
		total_amount = setFloat(total_amount, 2);
		$("#total_amount_value").text(total_amount);
	}
	
	function calculateNetTotalAmount(){
		var net_total_amount = 0.00;
		var total = $("#total_amount_value").text();
		var discount = $("#total_discount_value").text();
		var discount_type = $("input[name=discount_type]:checked").val();
		var deposit_value = $("#total_deposit_value").text();
		var total_deposit = $("#cal_deposit_payment_amount").val();
		$("#total_deposit_value").text(total_deposit, 2)
		var deposit_value = $("#total_deposit_value").text();
		var getTotal = setFloat((total - deposit_value), 2);
		net_total_amount = getTotal;		
		
		discount_type = (discount_type == "undefined") ? "" : discount_type;
		if(total != "0.00"){
			if(discount_type ==""){
				discount = "0";
			}	
			if(discount_type == "1"){
				discount = discount.split("-$");
				discount = discount[1];
				net_total_amount = setFloat(getTotal - discount, 2);
			}
			if(discount_type == "2"){
				discount = discount.split("-");
				discount = discount[1];
				discount = discount.split("%");
				discount = discount[0];
				discount = discount / 100;
				net_total_amount = setFloat(getTotal - (getTotal * discount), 2);
			}
			$("#net_total_amount_value").text(net_total_amount, 2);
		}else{
			$("#net_total_amount_value").text(net_total_amount, 2);		
		}
		calPaymentAmount();
	}	

	function load_payment_list_action(){
		$("#payment_list").find($(".payment_list_row")).each(function(){
			var payment_type_id = $(this).data("payment-type-id");
			$("#payment_type option[value="+ payment_type_id + "]").attr('disabled','disabled');
		});
	}

	function payment_list_action_button(){
		$(".payment_list_ok_single_item_button").on("click", function( event ){
			event.preventDefault();
			$(this).hide();
			$(this).next(".payment_list_edit_single_item_button").show();
			var obj = $(this);
			confirmSinglePeymentItem(obj);
		});

		$(".payment_type_amount_text_field").keypress(function(e) {
			if(e.which == keyEnter) {
				event.preventDefault();
				$(this).closest(".payment_list_row").find(".payment_list_ok_single_item_button").hide();
				$(this).closest(".payment_list_row").find(".payment_list_edit_single_item_button").show();
				var obj = $(this);
				confirmSinglePeymentItem(obj);
			}
		});			
		
		$(".payment_list_edit_single_item_button").on("click", function( event ){
			event.preventDefault();
			$(this).hide();
			$(this).prev(".payment_list_ok_single_item_button").show();
			var obj = $(this);
			editSinglePeymentItem(obj);
		});

		$(".payment_list_delete_single_item_button").on("click", function(){
			event.preventDefault();
			var obj = $(this);
			removeSinglePeymentItem(obj);
		});
	}
	
	function check_shop_code(){
		var current_shop_code = $("#from_shop_name_text").text();
		console.log(current_shop_code);
		$(".list_item_row.quotation_item").each(function(){
			var load_shop_code = $(this).find('.check_shop_item').val();
			if(current_shop_code != load_shop_code){
				$(this).addClass('out_of_stock_item');
			}else{
				$(this).removeClass('out_of_stock_item');
			}
		});
		$(".list_item_row.deposit_item").each(function(){
			var load_shop_code = $(this).find('.check_shop_item').val();
			if(current_shop_code != load_shop_code){
				$(this).addClass('out_of_stock_item');
			}else{
				$(this).removeClass('out_of_stock_item');
			}
		});		
	}

	function calPaymentAmount(){
		var payment_list_total = 0.00;
		var payment_last_total = 0.00;
		
		$("#payment_list").find($(".payment_list_row")).each(function(){	
			var payment_amt = parseFloat($(".payment_type_amount_text", this).text());
			payment_list_total += payment_amt;
		});	
		var payment_total = parseFloat(payment_list_total);
		var net_total_price = $("#net_total_amount_value").text();
		if(isNaN(net_total_price)== true){
			net_total_price = "0.00";
		}		
		if(isNaN(payment_total)== true){
			payment_total = "0.00";
		}
		if(payment_total > net_total_price){
			$("#payment_list_total").val(payment_total);
			$("#no_payment_list_over_error_message").show();
		}else{
			$("#payment_list_total").val(payment_total);
			$("#no_payment_list_over_error_message").hide();
		}
		payment_last_total = (net_total_price - payment_total).toFixed(2);
		if(payment_last_total == '0.00'){
			$("#no_payment_list_require_error_message").hide();
		}		
		$(".payment_cal_total").text("$" + payment_total);
		$(".payment_cal_last_total").text("$" + payment_last_total);
	}	
	
	function confirmSinglePeymentItem( obj ) {
		var payment_amount_block = obj.closest(".payment_list_row").find(".payment_type_amt_field");
		var payment_amount_input_field = payment_amount_block.find(".payment_type_amount_text_field");
		var payment_amount_text_field = payment_amount_block.find(".payment_type_amount_text");
		payment_amount_input_field_val = (payment_amount_input_field.val() == "") ? '0.00' : payment_amount_input_field.val();
		payment_amount_input_field.hide();
		payment_amount_text_field.text(payment_amount_input_field_val);
		payment_amount_text_field.show();

		if(payment_amount_text_field.text() != ""){
			$("#no_payment_list_item_error_message").hide();
		}
		calPaymentAmount();
	}

	function editSinglePeymentItem( obj ) {
		var payment_amount_block = obj.closest(".payment_list_row").find(".payment_type_amt_field");
		var payment_amount_input_field = payment_amount_block.find(".payment_type_amount_text_field");
		var payment_amount_text_field = payment_amount_block.find(".payment_type_amount_text");

		payment_amount_input_field.show();
		payment_amount_text_field.text();
		payment_amount_text_field.hide();
	}

	function removeSinglePeymentItem( obj ) {
		var payment_item_id = obj.closest(".payment_list_row").data("payment-id");
		var payment_type_id = obj.closest(".payment_list_row").data("payment-type-id");
		$("#payment_type option[value="+ payment_type_id + "]").removeAttr('disabled','disabled');
		remove_payment_item_id.push(payment_item_id);
		$(".remove_payment_ids").val(remove_payment_item_id);
		obj.closest(".payment_list_row").remove();
		calPaymentAmount();
	}

	function resetDiscount(){
		var discount_amount = $("#total_discount_amount").val();
		if(discount_amount == ""){
			$("#total_discount_value").text("---");
			$("#cal_discount_type").val("");
		}
		if(discount_amount == "0"){
			$("#total_discount_value").text("---");
			$("#cal_discount_type").val("");
		}
	}

	function resetDeposit(){
		$("#cal_deposit_id").val("");
		$("#deposit_code").val("");
		$("#total_deposit_value").text("0.00");

		$("#deposit_popup_list").modal('hide');
		calculateNetTotalAmount();
	}

	function fnKeyBinding() {
    shortcut.add("F8", function() {
			setStatusBtn('1');
      checkConfirmField();
    });
	}

	function setStatusBtn(status){
		$("#status_type").val(status);
	}

	function checkConfirmField(){
		var status = $("#status_type").val();
		var ok = true;
		var payment_total_price = parseInt($("#payment_list_total").val());
		var net_total_price = parseInt($("#net_total_amount_value").text());
		var payment_require = $(".payment_cal_last_total").text();
		
		if($(".list_item_row").length < 1){
			$("#no_list_item_error_message").show();
			ok = false;
		}
		if($(".payment_list_row").length < 1){
			$("#no_payment_list_item_error_message").show();
			ok = false;
		}
		if($(".payment_type_amount_text").text() === ""){
			$("#no_payment_list_item_error_message").show();
			ok = false;
		}

		if(payment_total_price > net_total_price){
			$("#no_payment_list_over_error_message").show();
			ok = false;
		}
		
		if(payment_require != '$0.00'){
			$("#no_payment_list_require_error_message").show();
			ok = false;
		}
		
		if($(".list_items").find(".out_of_stock_item")){
			$.each($(".out_of_stock_item "), function(){
				ok = false;
			});
		};		
		
		if ( !ok ) {
			alert("The form contains error\nPlease correct the errors before submit the form");
			return false;
		}

		confirmToPay(status);

		return true;
	}

	function confirmToPay(status){
		$("#confirm-table tbody").html("");

		if(status == "1"){
			$(".payment_status").text('Payment');
		}
		if(status == "2"){
			$(".payment_status").text('Temp Save Payment');
		}

		 $("#list_items").find($(".list_item_row")).each(function(){
			var product_name = $(".get_product_name", this).text();
			var product_qty = $(".get_product_qty", this).val();
			var product_discount = $(".get_product_discount", this).val();
			var product_total = $(".count_product_total", this).text();

			var item_row = ' \
							<tr><td class="item-name">' + product_name + '</td> \
								<td class="item-qty-text">' + product_qty + '</td> \
								<td class="item-discount-text">' + product_discount + '</td> \
								<td class="item-total-price-text text-right">' + product_total + '</td> \
							</tr>';
			$("#confirm-table tbody").append(item_row);
		});

		 $(".confirm_payment_row").remove();
		 $("#payment_list").find($(".payment_list_row")).each(function(){
			var payment_type_text = $(".payment_type_type_field", this).text();
			var payment_type_amount = $(".payment_type_amount_text", this).text();
			var payment_type_id = $(".payment_type_type_id", this).val();
				if(payment_type_amount != ""){
					var item_row = ' \
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 confirm_payment_row"> \
									<div class="input-group"> \
										<div class="input-group-addon payment_confirm_field">'+ payment_type_text +'</div> \
										<input type="text" id="payment_confirm_amount" class="form-control payment_confirm_amount" name="payment_confirm_amount" value="' + payment_type_amount +'" readonly> \
										<input type="hidden" id="payment_confirm_id" class="form-control payment_confirm_id" name="payment_confirm_amount" value="' + payment_type_id +'" readonly> \
									</div> \
								</div>';
					$(".confirm_payment_list").append(item_row);
			}
		});

		var cal_total = $("#total_amount_value").text();
		var cal_discount = $("#total_discount_value").text();
		var cal_deposit = $("#total_deposit_value").text();
		var cal_net_total = $("#net_total_amount_value").text();

		$("#confirm-cart-total-price-text").text("$" + cal_total);
		$("#confirm-cart-discount-text").text(cal_discount);
		$("#confirm-cart-deposit-text").text("-$" + cal_deposit);
		$("#confirm-cart-final-price-text").text("$" + cal_net_total);

		$("#confirmPage").modal();
	}

	function checkPaymentConfirmField(){
		var cashier_id = $("#cashier_id").val();
		var cashier_password = $("#cashier_password").val();
		var ok = true;

		if(cashier_password == "" ){
			$("#cashier_password").focus();
			ok = false;
		}

		if( cashier_id == "" ){
			$("#cashier_id").focus();
			ok = false;
		}

		if(!ok){
			alert("Please enter Cashier ID AND Password.");
			return false;
		}else{
			getOrderItem();
		}
	}

	function getProductItemObject(){
		var i = 0;
		var nc = 0;

		$("#list_items").find($(".list_item_row")).each(function(){
			var product_item_id = $(this).data('item_id');
			var product_item_num = $(".product_item", this).val();
			var stock_id = parseInt($(this).data("stock-id"));
			var deposit_id = parseInt($(this).data("deposit-id"));
			var quotation_id = parseInt($(this).data("quotation-id"));
			var product_id = parseInt($(this).data("product-id"));
			var product_code = $(".get_product_upc", this).text();
			var serial_number = $(".get_serial_number", this).text();
			var product_unit_price = $(".get_product_unit_price", this).text();
			var product_qty = $(".get_product_qty", this).val();
			var product_discount = $(".get_product_discount", this).val();
			var product_total = $(".count_product_total", this).text();

			if(product_item_id == ""){
				product_item_id = "0";
			}
			
			if(product_discount == ""){
				product_discount = "0";
			}

			if(serial_number == "---"){
				serial_number = "";
			}
			
			if(isNaN(stock_id)== true){
				stock_id = 0;
			}
			
			if(isNaN(deposit_id)== true){
				deposit_id = 0;
			}	
			
			if(isNaN(quotation_id)== true){
				quotation_id = 0;
			}			

			product_unit_price = product_unit_price.split("$");
			product_unit_price = product_unit_price[1];
			product_total = product_total.split("$");
			product_total = product_total[1];

			if(product_item_id == "0"){
				cartObject_new[nc] = new Object();
				cartObject_new[nc].stock_id = stock_id;
				cartObject_new[nc].deposit_id = deposit_id;
				cartObject_new[nc].quotation_id = quotation_id;
				cartObject_new[nc].product_id = product_id;
				cartObject_new[nc].product_code = product_code;
				cartObject_new[nc].serial_number = serial_number;
				cartObject_new[nc].product_unit_price = product_unit_price;
				cartObject_new[nc].product_qty = product_qty;
				cartObject_new[nc].product_discount = product_discount;
				cartObject_new[nc].product_total = product_total;
				nc++;
			}else{
				cartObject[i] = new Object();
				cartObject[i].product_item_id = product_item_num;
				cartObject[i].stock_id = stock_id;
				cartObject[i].deposit_id = deposit_id;
				cartObject[i].quotation_id = quotation_id;
				cartObject[i].product_id = product_id;
				cartObject[i].product_code = product_code;
				cartObject[i].serial_number = serial_number;
				cartObject[i].product_unit_price = product_unit_price;
				cartObject[i].product_qty = product_qty;
				cartObject[i].product_discount = product_discount;
				cartObject[i].product_total = product_total;
				i++;
			}
		});

		return {cartObject, cartObject_new};
	}

	function getPaymentItemObject(){
		var p = 0;
		var np = 0;
		$(".payment_list").find($(".payment_list_row")).each(function(){
			var payment_item_id = $(this).data('payment-id');
			var payment_item_num = $(".payment_type_amount_text_field").data('payment-num');
			var payment_type_id = $(".payment_type_type_id", this).val();
			var payment_type_field = $(".payment_type_type_field", this).text();
			var payment_type_amount = $(".payment_type_amount_text", this).text();

			if(payment_item_id == ""){
				paymentObject_new[np] = new Object();
				paymentObject_new[np].payment_method_id = payment_type_id;
				paymentObject_new[np].payment_method_name = payment_type_field;
				paymentObject_new[np].payment_method_amount = payment_type_amount;
				np++;
			}else{
				paymentObject[p] = new Object();
				paymentObject[p].payment_item_id = payment_item_id;
				paymentObject[p].payment_method_id = payment_type_id;
				paymentObject[p].payment_method_name = payment_type_field;
				paymentObject[p].payment_method_amount = payment_type_amount;
				p++;
			}
		});
			return { paymentObject, paymentObject_new };
	}

	function getOrderItem(){
		var status = $("#status_type").val();
		var status_text = "";
		(status == '1') ? status_text = "Confirm" : "";
		(status == '2') ? status_text = "Temp save" : "";

		var ans = window.confirm("Are you sure " + status_text + " this order?");
		if ( !ans ) {
			return false;
		}
		var sales_invoice_edit_id = $("#sales_invoice_edit_id").val();
		var cashier_id = $("#cashier_id").val();
		var cashier_password = $("#cashier_password").val();
		var sales_name = $("#sales_name").val();
		var term = $("#payment_term").val();
		var remark = $("#remarks").val();
		var deposit_id = $("#cal_deposit_id").val();
		var quotation_id = $("#cal_quotation_id").val();
		var old_deposit_id = $("#old_cal_deposit_id").val();
		var old_quotation_id = $("#old_cal_quotation_id").val();
		var deposit_payment_amount = $("#total_deposit_value").text();
		var total_amount = $("#confirm-cart-total-price-text").text();
		total_amount = total_amount.split("$");
		total_amount = total_amount[1];

		var total_discount = $("#confirm-cart-discount-text").text();
		var total_discount_type = $("input[name=discount_type]:checked").val();

		if(total_discount_type == "1"){
			total_discount = total_discount.split("-$");
			total_discount = total_discount[1];
		}
		if(total_discount_type == "2"){
			total_discount = total_discount.split("-");
			total_discount = total_discount[1];
			total_discount = total_discount.split("%");
			total_discount = total_discount[0];
		}
		if(total_discount_type == ""){
			total_discount = total_discount
			total_discount_type = "0";
		}
		if(total_discount == "---"){
			total_discount = "0";
		}
		
		var net_total_amount = $("#confirm-cart-final-price-text").text();
		net_total_amount = net_total_amount.split("$");
		net_total_amount = net_total_amount[1];

		var remove_cart_ids = $(".remove_item_ids").val();
		var remove_payment_ids = $(".remove_payment_ids").val();

		cartObject = getProductItemObject().cartObject;
		cartObject_new = getProductItemObject().cartObject_new;

		paymentObject = getPaymentItemObject().paymentObject;
		paymentObject_new = getPaymentItemObject().paymentObject_new;

		formObject.cart = cartObject;
		formObject.cart_new = cartObject_new;
		formObject.payment = paymentObject;
		formObject.payment_new = paymentObject_new;

		formObject.sales_invoice_edit_id = sales_invoice_edit_id;
		formObject.cashier_id = cashier_id;
		formObject.cashier_password = cashier_password;
		formObject.sales_name = sales_name;
		formObject.status = status;

		formObject.term = term;

		formObject.remark = remark;
		formObject.total_discount = total_discount;
		formObject.total_discount_type = total_discount_type;
		formObject.total_amount = total_amount;
		formObject.net_total_amount = net_total_amount;
		
		formObject.old_deposit_id = old_deposit_id;
		formObject.old_quotation_id = old_quotation_id;
		formObject.deposit_id = deposit_id;
		formObject.quotation_id = quotation_id;		
		formObject.deposit_payment_amount = deposit_payment_amount;		

		formObject.remove_cart_ids = remove_cart_ids;
		formObject.remove_payment_ids = remove_payment_ids;
		
		console.log(formObject);
		paymentChecking(formObject);
		
	}

	function paymentChecking(formObject){
		var status = $("#status_type").val();
		var status_text = "";
		(status == '1') ? status_text = "交易成功" : "";
		(status == '2') ? status_text = "儲存成功" : "";

		var jqxhr = $.ajax({
			type: 'POST',
			url: ROOT + "sales_invoice/update",
			async: false,
			data: formObject
		});

		jqxhr.done(function(result) {
		  result = JSON.parse(result);
			if (result.status == "success") {
					(status == '1') ?  window.open(ROOT + "/sales_invoice/print/" + result.sales_invoice_id, 'Download') : "";
			  $("#resultPage .modal-body").html('<div class="alert alert-success" role="alert">' + status_text + '</div>');
			  $("#confirmPage").modal("hide");
			  $("#resultPage").modal();
			  resetField();

					$(".btn_confirm_refresh").on("click", function(event) {
						window.location = ROOT + 'sales_invoice';
					});
		  }
			if (result.status == "fail") {
				alert("Cannot access the Cashier / Password, Please try again!")
			}
		});
	}

	/** Start popup function */
	
	function showStockPopupList() {
		var search_stock = getValue("quick_search");
		var search_type = getValue("quick_search_type");
		if(search_type == 1){
			$("#search_product_upc").val(search_stock);
			$("#search_serial_number").val('');
		}else if(search_type == 2){
			$("#search_serial_number").val(search_stock);
			$("#search_product_upc").val('');
		}
		updateList("stock_popup_list");
		$("#stock_popup_list").modal();
	}	
	
	function showQuotationPopupList() {
		var search_quotation = getValue("quotation_code");
		$("#search_quotation_number").val(search_quotation);
		updateList("quotation_popup_list");
		$("#quotation_popup_list").modal();
	}	
	
	function showDepositPopupList() {
		var search_deposit = getValue("deposit_code");
		$("#search_deposit_number").val(search_deposit);
		updateList("deposit_popup_list");
		$("#deposit_popup_list").modal();
	}			
	
	/** End popup function */	
	
	removeItems();
	editProductItem();
	load_payment_list_action();
	payment_list_action_button();
	calculateTotalAmount();
	calculateNetTotalAmount();
	fnKeyBinding();
	check_shop_code();
});
