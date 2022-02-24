"use strict";
var orderObject = new Object();

$(function() {

	var BTN_ORDER_ADD = $("#btn-order-add");
	var BRAND_TABLE = $("#brand-table tbody");
	var brandCode_TEXT = $(".brand_code_text");
	var brandName_TEXT = $(".brand_desc_text");
	var brandCode = $("#brand_code");
	var brandName = $("#brand_name");
	
    var CONFIRM_TABLE = $("#confirm-table tbody");
	var BTN_CONFIRM_NOW = $("#btn-confirm-now");
	

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
            confirmToAdd();
        });

        shortcut.add("F3", function() {
            addOne();
        });

        shortcut.add("F4", function() {
            minusOne();
        });
		
        shortcut.add("F9", function() {
			BRAND_TABLE.find("tr.new_brand").each(function() {
				if(this != ''){
					confirmToAdd();
				}
			});
        });

        shortcut.add("F2", function() {
            $("#searchPage").modal();
        });

        shortcut.add("Ctrl+Enter", function() {
			setBrandAdd();
			if(orderObject.brandCode != '' && orderObject.brandName !='' ){
				addToBrandList(orderObject);
			}
        });
	}
    
	function removefnKeyBinding() {
        shortcut.remove("F3");
        shortcut.remove("F4");
        shortcut.remove("F9");
        shortcut.remove("F2");
        shortcut.remove("Ctrl+Enter");
    }

    function brandTableBinding() {
        var BRAND_TABLE_ROW = $("#brand-table tbody tr");
        BRAND_TABLE_ROW.click(function(event) {
            event.preventDefault();
            event.stopPropagation();
            BRAND_TABLE_ROW.removeClass("info");
            selectedItems = $(this);
            selectedItems.addClass("info");
            calCartItemTotal();
        });
    }
	
    function addToBrandList(items) {
        var item_row = '<tr class="new_brand"><td bgcolor="#EBFFD6" width="30" class="brand_key_text">' + '---' + '</td>';
        item_row += '<td bgcolor="#EBFFD6" width="60" class="brand_code_text">' + orderObject.brandCode + '</td>';
        item_row += '<td bgcolor="#EBFFD6" width="100" class="brand_desc_text">' + orderObject.brandName + '</td>';
        item_row += '<td bgcolor="#EBFFD6" width="80" class="brand_update_text">' + 'xxx' + '</td>';
        item_row += '<td bgcolor="#EBFFD6" width="60" class="brand_by_text">' + 'xxx' + '</td>';
        item_row += '<input type="hidden" class="brand_code_value" name="brand_code_value" value="' + orderObject.brandCode + '">';		
        item_row += '<input type="hidden" class="brand_desc_value" name="brand_desc_value" value="' + orderObject.brandName + '">';
        item_row += '<input type="hidden" class="brand_update_value" name="brand_update_value" value="' + 'xxx' + '">';
        item_row += '<input type="hidden" class="brand_by_value" name="brand_by_value" value="' + 'xxx' + '"></tr>';				
        BRAND_TABLE.prepend(item_row);
        brandTableBinding();
    }
	
    function confirmToAdd() {
        CONFIRM_TABLE.html("");
        BRAND_TABLE.find("tr.new_brand").each(function() {
            var item_code = $(".brand_code_text", this).text();
            var item_name = $(".brand_desc_value", this).val();
			
            var item_row = '<tr><td class="brand_code_text">' + item_code + '</td>';
            item_row += '<td class="brand_desc_text">' + item_name + '</td></tr>';
            CONFIRM_TABLE.append(item_row);
        });
		
        $("#confirmPage").modal();
    }	
	
	function setBrandAdd(){
		orderObject.brandCode = brandCode.val();
		orderObject.brandName = brandName.val();		
	}
	
    function AddBrandNow() {
        var formObject = new Object();
        var brandObject = new Object();
        var i = 0;

        BRAND_TABLE.find("tr.new_brand").each(function() {
            var item_code = $(".brand_code_text", this).text();
            var item_name = $(".brand_desc_value", this).val();
            brandObject[i] = new Object();
            brandObject[i].item_code = item_code;
            brandObject[i].item_name = item_name;
            i++;
        });

        formObject.brand = brandObject;
        formObject.cashier_id = $("#cashier_id").val();
        formObject.cashier_password = $("#cashier_password").val();
        formObject.sales_id = $("#sales_id").val();

        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "create_brand",
            async: false,
            data: formObject
        });

        jqxhr.done(function(result) {
            result = JSON.parse(result);
            if (result.status == "success") {
                $("#resultPage .modal-body").html('<div class="alert alert-success" role="alert">新增成功</div>');
                $("#confirmPage").modal("hide");
                $("#resultPage").modal();
				
				$(".btn_brand_close").on("click", function(event) {
					location.reload();
				});
            }
        });
    }	
	
    function resetField() {
        brandCode.val('');
        brandName.val('');
        $("#brand-table tbody tr.new_brand").html('');
        orderObject = new Object();
    }	
	
    BTN_ORDER_ADD.off("click").on("click", function(event) {
        event.preventDefault();
        event.stopPropagation();
		
		setBrandAdd();
		
		if(orderObject.brandCode != '' && orderObject.brandName !='' ){
			addToBrandList(orderObject);
		}
    });		
	
    BTN_CONFIRM_NOW.off("click").on("click", function(event) {
        event.preventDefault();
        event.stopPropagation();
        AddBrandNow();
    });	
	
	fnKeyBinding();
	brandTableBinding();
});