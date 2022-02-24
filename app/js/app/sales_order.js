var selectedItems;
var formItems;
var orderObject = new Object();

$(function() {
    function fnKeyBinding() {
        $("#btn-fn-addone").off("click").on("click", function() {
            addOne();
        });

        $("#btn-fn-minusone").off("click").on("click", function() {
            minusOne();
        });

        $("#btn-fn-delete").off("click").on("click", function() {
            removeCartItem();
        });

        $("#btn-fn-scan").off("click").on("click", function() {
            $('#order-product-code').focus();
        });

        $("#btn-fn-search").off("click").on("click", function() {
            $("#searchPage").modal();
        });

        $("#btn-fn-confirm").off("click").on("click", function() {
            confirmToPay();
        });

        shortcut.add("F3", function() {
            addOne();
        });

        shortcut.add("F4", function() {
            minusOne();
        });

        shortcut.add("F5", function() {
            console.log("focus on barcode 123456");
        });

        shortcut.add("F9", function() {
            confirmToPay();
        });

        shortcut.add("F2", function() {
            $("#searchPage").modal();
        });

        shortcut.add("Ctrl+Enter", function() {
            addToCartList(orderObject);
        });
        
        shortcut.add("Esc", function(){
           window.location = "/pos"; 
        });
	}
    
	function removefnKeyBinding() {
        shortcut.remove("F3");
        shortcut.remove("F4");
        shortcut.remove("F9");
        shortcut.remove("F2");
        shortcut.remove("Ctrl+Enter");
    }

    function cartTableBinding() {
        var CART_TABLE_ROW = $("#cart-table tbody tr");
        CART_TABLE_ROW.click(function(event) {
            event.preventDefault();
            event.stopPropagation();
            CART_TABLE_ROW.removeClass("info");
            selectedItems = $(this);
            selectedItems.addClass("info");
            calCartItemTotal();
        });
    }

    function searchTableBinding() {
        var SEARCH_TABLE_ROW = $("#search-table tbody tr");
        SEARCH_TABLE_ROW.click(function(event) {
            event.preventDefault();
            event.stopPropagation();
            SEARCH_TABLE_ROW.removeClass("info");
            $(this).addClass("info");
        });

        BTN_SEARCH_SELECT.off("click").on("click", function() {
            addSearchItemToOrder();
        });
    }

    function addSearchItemToOrder() {
        SEARCH_TABLE.find(".info").each(function() {
            orderObject.productCode = $(".search-item-product-code", this).val();
            orderObject.serialNumber = $(".search-item-serial-number", this).val();
            orderObject.unitPrice = parseFloat($(".search-item-unit-price", this).val());
            orderObject.stockQty = parseInt($(".search-item-qty", this).val());
            orderObject.productName = $(".search-item-product-name", this).val();
            orderObject.discount = 0;
            orderObject.qty = 1;
            orderObject.total = orderObject.unitPrice;
            ORDER_PRODUCT_CODE.val(orderObject.productCode);
            ORDER_KEY_NUMBER.val(orderObject.serialNumber);
            ORDER_QTY.val('1');
            ORDER_DISCOUNT.val(0);
            ORDER_TOTAL.val(orderObject.total);
        });
        $("#searchPage").modal('hide');
    }

    function getProductRecord() {
		
        var ORDER_KEY_NUMBER = $("#order-key-number");
        console.log("call getProduct");
        
        var formObject = new Object();

        formObject.search_keyword = ORDER_KEY_NUMBER.val();
        formObject.search_type = ORDER_RADIO_SEARCH_TYPE.val();

        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "get_product",
            async: false,
            data: formObject
        });

        jqxhr.done(function(result) {
            console.log(result);
            presetAddField(result);
        });
        /*
        orderObject.productCode = code;
        orderObject.unitPrice = 100;
        orderObject.stockQty = 20;
        orderObject.productName = "Iphone Case";*/
        //orderObject.total = orderObject.unitPrice;
    }

    function presetAddField(result) {
		var qty = '';
        var result = $.parseJSON(result);
        var discount = orderObject.discount = ORDER_DISCOUNT.val();
        
        for (var i = 0; i < result.length; i++) {
			var sno = result[i].SNO;
			var product_code = result[i].PRODUCT_CODE;
			var product_name = result[i].PRODUCT_NAME;
			var retail = result[i].RETAIL;
        }
        if (qty == undefined || qty == "" || qty == null) {
            ORDER_QTY.val('1');
            qty = orderObject.qty = 1;
        }

        if (discount == undefined || discount == "" || discount == null) {
            ORDER_DISCOUNT.val('0');
            discount = orderObject.discount = 0;
        }
        
		orderObject.serialNumber = sno;
		orderObject.product_code = product_code;
		orderObject.productName = product_name;
		orderObject.qty = qty;
		orderObject.unitPrice = retail;
		orderObject.discount = discount;
		orderObject.total = parseInt(qty) * retail * ((100 - parseInt(discount)) / 100);
				
        ORDER_QTY.val(qty);
        ORDER_TOTAL.val(orderObject.total);
    }    

    function clearField() {
        ORDER_PRODUCT_CODE.val('');
        ORDER_KEY_NUMBER.val('');
        ORDER_QTY.val('');
        ORDER_DISCOUNT.val('');
        ORDER_TOTAL.val('');
    }

    function resetField() {
        ORDER_PRODUCT_CODE.val('');
        ORDER_KEY_NUMBER.val('');
        ORDER_QTY.val('');
        ORDER_DISCOUNT.val('');
        ORDER_TOTAL.val('');
        $("#cart-discount-form").val('');
        $("#cart-table tbody").html('');
        orderObject = new Object();
        $("#cart-total-price-text").html('');
        $("#cart-total-price").val('');
        $("#cart-discount-text").html('');
        $("#cart-discount").val('');
        $("#cart-discount-type").val('');
        $("#cart-final-price-text").html('');
        $("#cart-final-price").val('');
    }

    function addToCartList(items) {
        var item_row = '<tr><td class="item-serial-number-text">' + orderObject.serialNumber + '</td>';
        item_row += '<td class="item-name">' + orderObject.productName + '</td>';
        item_row += '<td class="item-qty-text">' + orderObject.qty + '</td>';
        item_row += '<td class="item-unit-price-text">$' + orderObject.unitPrice + '</td>';
        item_row += '<td class="item-discount-text">' + orderObject.discount + '%</td>';
        item_row += '<td class="item-total-price-text text-right">$' + orderObject.total + '</td>';
        item_row += '<input type="hidden" class="item-product-code" name="item-product-code" value="' + orderObject.product_code + '">';
        item_row += '<input type="hidden" class="item-serial-number" name="item-serial-number" value="' + orderObject.serialNumber + '">';
        item_row += '<input type="hidden" class="item-qty" name="item-qty" value="' + orderObject.qty + '">';
        item_row += '<input type="hidden" class="item-unit-price" name="item-unit-price" value="' + orderObject.unitPrice + '">';
        item_row += '<input type="hidden" class="item-discount" name="item-discount" value="' + orderObject.discount + '">';
        item_row += '<input type="hidden" class="item-total-price" name="item-total-price" value="' + orderObject.total + '"></tr>';
        CART_TABLE.prepend(item_row);
        clearField();
        calCartTotal();
        /* Cart Table event */
        cartTableBinding();
    }

    function removeCartItem() {
        if (selectedItems != undefined) {
            selectedItems.remove();
        }
    }

    function calOrderTotal() {
        var qty = orderObject.qty = parseInt(ORDER_QTY.val());
        var discount = orderObject.discount = parseInt(ORDER_DISCOUNT.val());
        var total = 0;

        if (qty == undefined || qty == "" || qty == null) {
            ORDER_QTY.val('1');
            qty = orderObject.qty = 1;
        }

        if (discount == undefined || discount == "" || discount == null) {
            ORDER_DISCOUNT.val('0');
            discount = orderObject.discount = 0;
        }
        orderObject.total = parseInt(qty) * orderObject.unitPrice * ((100 - parseInt(discount)) / 100);
        ORDER_TOTAL.val(Math.round(orderObject.total * 100) / 100);
    }

    function calCartItemTotal() {
        var unitPrice = parseFloat(selectedItems.find('.item-unit-price').val(), 2);
        var discount = selectedItems.find('.item-discount').val();
        var qty = selectedItems.find('.item-qty').val();
        var cart_item_total = unitPrice * ((100 - discount) / 100) * qty;
        selectedItems.find('.item-total-price').val(Math.round(cart_item_total * 100) / 100);
        selectedItems.find('.item-total-price-text').text('$' + cart_item_total.toFixed(2));
        calCartTotal();
    }

    function calCartTotal() {
        var CART_TABLE_TOTAL_PRICE = $('#cart-table .item-total-price');
        var total_price = 0;
        var final_price = 0;
        var discount_val = parseFloat(CART_DISCOUNT.val());
        var discount_val_type = parseFloat(CART_DISCOUNT_TYPE.val());

        CART_TABLE_TOTAL_PRICE.each(function() {
            total_price += Number($(this).val());
        });

        CART_TOTAL_PRICE_TEXT.text("$" + total_price.toFixed(2));
        CART_TOTAL_PRICE.val(total_price);

        if (discount_val_type == 0) {
            CART_FINAL_PRICE.val(total_price);
            CART_FINAL_PRICE_TEXT.text("$" + total_price.toFixed(2));
        } else if (discount_val_type == 1) {
            CART_DISCOUNT_TEXT.text("$" + discount_val.toFixed(2));
            final_price = total_price - discount_val;
            CART_FINAL_PRICE.val(final_price);
            CART_FINAL_PRICE_TEXT.text("$" + final_price.toFixed(2));
        } else if (discount_val_type == 2) {
            CART_DISCOUNT_TEXT.text("-" + discount_val + "%");
            final_price = total_price * ((100 - discount_val) / 100);
            CART_FINAL_PRICE.val(final_price);
            CART_FINAL_PRICE_TEXT.text("$" + final_price.toFixed(2));
        }
    }

    function addOne() {
        if (selectedItems != null && selectedItems != undefined) {
            var qty = parseInt(selectedItems.find('.item-qty').val());
            qty += 1;
            selectedItems.find('.item-qty-text').text(qty);
            selectedItems.find('.item-qty').val(qty);
            calCartItemTotal();
        } else if (orderObject != null && orderObject != undefined) {
            if ($("#order-product-code").is(":focus")) {
                var qty = parseInt(ORDER_QTY.val());
                qty += 1;
                ORDER_QTY.val(qty);
                orderObject.qty = qty;
                calOrderTotal();
            }
        }
    }

    function minusOne() {
        if (selectedItems != null && selectedItems != undefined) {
            var qty = parseInt(selectedItems.find('.item-qty').val());
            qty -= 1;
            selectedItems.find('.item-qty-text').text(qty);
            selectedItems.find('.item-qty').val(qty);
            calCartItemTotal();
        } else if (orderObject != null && orderObject != undefined) {
            if ($(".sales_order_add_bar input").is(":focus")) {
                var qty = parseInt(ORDER_QTY.val());
                qty -= 1;
                ORDER_QTY.val(qty);
                orderObject.qty = qty;
                calOrderTotal();
            }
        }
    }

    function confirmToPay() {
        CONFIRM_TABLE.html("");
        CART_TABLE.find("tr").each(function() {
            var item_name = $(".item-name", this).text();
            var qty = $(".item-qty", this).val();
            var discount = $(".item-discount", this).val();
            var price = $(".item-total-price", this).val();
            var item_row = '<tr><td class="item-name">' + item_name + '</td>';
            item_row += '<td class="item-qty-text">' + qty + '</td>';
            item_row += '<td class="item-discount-text">' + discount + '%</td>';
            item_row += '<td class="item-total-price-text text-right">$' + price + '</td></tr>';
            CONFIRM_TABLE.append(item_row);
        });

        var cart_discount_val = CART_DISCOUNT.val();
        var cart_discount_type = CART_DISCOUNT_TYPE.val();
        var cart_discount_text = CART_DISCOUNT_TEXT.text();
        var cart_total_price = CART_TOTAL_PRICE_TEXT.text();
        var cart_final_price = CART_FINAL_PRICE_TEXT.text();
        CONFIRM_CART_DISCOUNT_TEXT.text(cart_discount_text);
        CONFIRM_CART_TOTAL_PRICE_TEXT.text(cart_total_price);
        CONFIRM_CART_FINAL_PRICE_TEXT.text(cart_final_price);
        $("#confirmPage").modal();
    }

    function payNow() {
        var formObject = new Object();
        var cartObject = new Object();
        var i = 0;

        CART_TABLE.find("tr").each(function() {
            var product_code = $(".item-product-code", this).val();
            var serial_number = $(".item-serial-number", this).val();
            var qty = parseInt($(".item-qty", this).val());
            var discount = parseInt($(".item-discount", this).val());
            cartObject[i] = new Object();
            cartObject[i].product_code = product_code;
            cartObject[i].serial_number = serial_number;
            cartObject[i].qty = qty;
            cartObject[i].discount = discount;
            i++;
        });

        formObject.cart = cartObject;
        formObject.cashier_id = $("#cashier_id").val();
        formObject.cashier_password = $("#cashier_password").val();
        formObject.sales_id = $("#sales_id").val();
        formObject.total_amt = CART_FINAL_PRICE.val();
        formObject.cart_discount = parseInt(CART_DISCOUNT.val());
        formObject.cart_discount_type = CART_DISCOUNT_TYPE.val();

        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "create_order",
            async: false,
            data: formObject
        });

        jqxhr.done(function(result) {
            result = JSON.parse(result);
            if (result.status == "success") {
                window.open(ROOT + "/print_sales_order?so_id=" + result.sales_order_id, 'Download');
                $("#resultPage .modal-body").html('<div class="alert alert-success" role="alert">交易完成</div>');
                $("#confirmPage").modal("hide");
                $("#resultPage").modal();
                resetField();
            }
        });
    }

    function searchProduct() {
        var jqxhr = $.ajax({
            url: ROOT + "search_product",
            async: false,
            data: $("form[name=search_product]").serialize()
        });

        jqxhr.done(function(result) {
            displaySearchResult(result);
        });
    }

    function displaySearchResult(result) {
        result = JSON.parse(result);
        SEARCH_TABLE.html("");

        if (result != null) {
            for (var i = 0; i < result.length; i++) {
                var item_row = '<tr><td class="">' + result[i].SHOPCODE + '</td>';
                item_row += '<td class="">' + result[i].PRODUCT_NAME + '</td>';
                item_row += '<td class="">' + result[i].SNO + '</td>';
                item_row += '<td class="">' + result[i].QTY + '</td>';
                item_row += '<td class="">$' + result[i].COST + '</td>';
                item_row += '<td class="text-right">$' + result[i].RETAIL + '</td>';
                item_row += '<input type="hidden" class="search-item-product-code" name="search-item-product-code" value="' + result[i].PRODUCT_CODE + '">';
                item_row += '<input type="hidden" class="search-item-serial-number" name="search-item-serial-number" value="' + result[i].SNO + '">';
                item_row += '<input type="hidden" class="search-item-product-name" name="search-item-product-name" value="' + result[i].PRODUCT_NAME + '">';
                item_row += '<input type="hidden" class="search-item-qty" name="search-item-qty" value="' + result[i].QTY + '">';
                item_row += '<input type="hidden" class="search-item-unit-price" name="search-item-unit-price" value="' + result[i].RETAIL + '"></tr>';
                SEARCH_TABLE.append(item_row);
            }
        } else {
            var item_row = '<tr><td colspan="6">No Record Found</td></tr>';
            SEARCH_TABLE.append(item_row);
        }
        searchTableBinding();
    }
    var ORDER_SEARCH_TYPE = $('input[type=radio][name=order_search_type]');
    var ORDER_RADIO_SEARCH_TYPE = $("#search_type");
    var ORDER_PRODUCT_CODE = $("#order-product-code");
    var ORDER_KEY_NUMBER = $("#order-key-number");
    var ORDER_QTY = $("#order-qty");
    var ORDER_DISCOUNT = $("#order-discount");
    var BTN_ORDER_ADD = $("#btn-order-add");
    var BTN_ORDER_CLEAR = $("#btn-order-clear");
    var ORDER_TOTAL = $("#order-total");
    var CART_TABLE = $("#cart-table tbody");
    var BTN_CAL_FINAL_PRICE = $('#btn-cal-final-price');
    var CART_TOTAL_PRICE_TEXT = $("#cart-total-price-text");
    var CART_TOTAL_PRICE = $("#cart-total-price");
    var CART_DISCOUNT_TEXT = $("#cart-discount-text");
    var CART_DISCOUNT = $("#cart-discount");
    var CART_DISCOUNT_TYPE = $("#cart-discount-type");
    var CART_FINAL_PRICE = $("#cart-final-price");
    var CART_FINAL_PRICE_TEXT = $("#cart-final-price-text");
    var CONFIRM_TABLE = $("#confirm-table tbody");
    var CONFIRM_CART_DISCOUNT_TEXT = $("#confirm-cart-discount-text");
    var CONFIRM_CART_TOTAL_PRICE_TEXT = $("#confirm-cart-total-price-text");
    var CONFIRM_CART_FINAL_PRICE_TEXT = $("#confirm-cart-final-price-text");
    var BTN_PAY_NOW = $("#btn-pay-now");
    var SEARCH_TABLE = $("#search-table tbody");
    var SEARCH_TABLE_ROW = $("#search-table tbody tr");
    var BTN_SEARCH_NOW = $("#search-now");
    var BTN_SEARCH_SELECT = $("#btn-search-select");

    $("input").on("focus", function() {
        var CART_TABLE_ROW = $("#cart-table tbody tr");
        CART_TABLE_ROW.removeClass("info");
    });

    ORDER_KEY_NUMBER.donetyping(getProductRecord);

    /*
    ORDER_KEY_NUMBER.on("paste", function(event) {
       getProductRecord();
    });
    */

	ORDER_SEARCH_TYPE.on("keyup", function() {
		var search_type = $(this).data("search_type");
		$("#search_type").val(search_type);
	});
	
   ORDER_SEARCH_TYPE.change(function() {
        var label = $(this).data("label");
        ORDER_KEY_NUMBER.prop("placeholder", label);   
		var search_type = $(this).data("search_type");
		$("#search_type").val(search_type);
    });	

    ORDER_PRODUCT_CODE.on('focus', function(event) {
        var CART_TABLE_ROW = $("#cart-table tbody tr");
        selectedItems = null;
        CART_TABLE_ROW.removeClass("info");
    });

    ORDER_QTY.on("change", function() {
        calOrderTotal();
    });

    ORDER_DISCOUNT.on("change", function() {
        calOrderTotal();
    });

    BTN_ORDER_ADD.off("click").on("click", function(event) {
        event.preventDefault();
        event.stopPropagation();
        addToCartList(orderObject);
    });

    BTN_ORDER_CLEAR.off("click").on("click", function(event) {
        event.preventDefault();
        event.stopPropagation();
        clearField();
    });

    BTN_CAL_FINAL_PRICE.off("click").on("click", function(event) {
        event.preventDefault();
        event.stopPropagation();
        var discount_form = $("input[name=cart-discount-form]");
        var discount_type = $("input[name=cart-discount-type-form]:checked");
        var total_price = parseInt(CART_TOTAL_PRICE.val());
        var final_price = 0;

        CART_DISCOUNT_TYPE.val(discount_type.val());
        discount_val = parseInt(discount_form.val());
        CART_DISCOUNT.val(discount_val);

        if (discount_type == 0) {
            CART_FINAL_PRICE.val(total_price);
            CART_FINAL_PRICE_TEXT.text("$" + total_price.toFixed(2));
        } else if (discount_type.val() == 1) {
            CART_DISCOUNT_TEXT.text("$" + discount_val.toFixed(2));
            final_price = total_price - discount_val;
            CART_FINAL_PRICE.val(final_price);
            CART_FINAL_PRICE_TEXT.text("$" + final_price.toFixed(2));
        } else {
            CART_DISCOUNT_TEXT.text(discount_val + "%");
            final_price = total_price * ((100 - discount_val) / 100);
            CART_FINAL_PRICE.val(final_price);
            CART_FINAL_PRICE_TEXT.text("$" + final_price.toFixed(2));
        }
    });

    BTN_SEARCH_NOW.off("click").on("click", function(event) {
        event.preventDefault();
        event.stopPropagation();
        searchProduct();
    });

    BTN_PAY_NOW.off("click").on("click", function(event) {
        event.preventDefault();
        event.stopPropagation();
        payNow();
    });

    /* function key event */
    fnKeyBinding();
    
    /* Cart Table event */
    cartTableBinding();
});