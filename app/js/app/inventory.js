"use strict";



$(function() {
	var product_title = $("#form_title");
	var product_action = $("#product_action");
	var product_submit = $("#product_submit");
	var product_edit = $("#product_edit");
	var getProductID = $("#product_id");
	var getProductUpc = $("#product_upc");
	var getProductBrand = $("#product_brand");
	var getProductBrandID = $("#product_brand_id");
	var getProductName = $("#product_name");
	var getProductSpec = $("#product_spec");
	var getProductRemark = $("#product_remark");
	var getProductCategory = $("#product_category");
	var product_detail = $(".product_detail");
	var keyword = getValue("product_search");
	var currentPage = getValue("currentPage");
	var total_product_page = getValue("lastPage");
	
	var $list_id = $("#product");
	var list_id = "product";	

	
	/* Start Product Script */

	function getValue(id) {
		return $.trim($("#" + id).val());
	}

	function getProductData(item_id){
		var clickResult = '1';
		var data = '';
		var jqxhr = $.ajax({
			type: "POST",
			url: ROOT + "product_search",
			data: { 
				keyword: item_id,
				clickResult: clickResult
			},
			async: false
		});

		jqxhr.done(function(record_list) {
			var product_list = record_list.product_list;
			if (product_list !== '') {
				$.each( product_list, function ( key, data ) {
					getProductID.val(data.id);
					getProductUpc.val(data.barcode);
					getProductBrandID.val(data.brand_id);
					getProductBrand.val(data.brand_name);
					getProductName.val(data.name);
					getProductSpec.val(data.product_spec);
					getProductRemark.val(data.remark);
					getProductCategory.val(data.category);
				})
			}			
		});
	}

	function SelectProduct(changeClass){
		if ( changeClass.hasClass("info") ) {
			changeClass.removeClass("info");
			CancelFormAction();
		} else {
			$(".info").removeClass("info");
			changeClass.addClass("info");
			var item_id = $(".info").data("item_id");
			product_title.text('Product Information');
			product_detail.css( "background", "#ccc" );
			product_action.show();
			product_submit.hide();
			product_edit.show();
			getProductData(item_id);
		}
	}
	
	function CancelFormAction() {
		$("#form_product_create")[0].reset();
		product_title.text('Product Create');
		product_detail.css( "background", "#e7e7e7" );
		product_action.hide();
		product_submit.show();
		product_edit.hide();	
	}
	
	function getProductSearchData(pages){	
		var data = '';
		var i = 0;
		var keyword = getValue("product_search");
		var search_record = '1';
		
		if ( keyword.length > 0 ) {
			$("#product_item_list tr").hide();
			$(".search_record").remove();
			 var jqxhr = $.ajax({
				type: "POST",
				url: ROOT + "product_search",
				data: { 
					keyword: keyword,
					page: pages
				},
				async: false
			 });

			jqxhr.done(function(record_list) {
				var search_total_pages = record_list.total_pages;
				var search_current_pages = record_list.current_pages;
				var product_list = record_list.product_list;
				if (product_list !== '') {
					$.each( product_list, function ( key, data ) {
						var item_row = ' \
							<tr data-item_id="' + data.id + '" data-current_pages="' + search_current_pages + '" class="search_record"> \
							<td width="165.188">' + data.barcode + '</td>" \
							<td width="330.391">' + data.name + '</td>" \
							<td width="165.188">' + data.brand_name + '</td>" \
							<td width="165.188">' + data.category + '</td>" \
							</tr>';	
						$("#product_item_list").prepend(item_row);
						i++;
					});			
				}			
			setChangePages(list_id, search_total_pages);				
			});
		}
		
		$(".search_record").on("click", function() {
			SelectProduct($(this));
		});		
		
		if ( keyword == '' ) {
			$(".search_record").remove();
			$("#product_item_list tr").show();
			$(".search_pages_list").remove();
			$(".list_pagination").show();
		}
	}
	
	function setChangePages(list_id, total_pages){
		$(".list_pagination").hide();
		var page_bar = ' \
			<div class="list_pagination search_pages_list"> \
			<ul class="pagination pagination-sm list_pagination_bar" id="page_bar"> \
			';
		for ( var i = 1; i <= total_pages; i++ ) {
			page_bar += '<li id="list_page_row" class="list_page_row search_pages_row" data-pages="'+ i +'"><a href="#" class="page_button" data-type="page">' + i + '</a></li>';
		}
		page_bar += ' \
			</ul> \
			</div>';
		$(".modal-footer").prepend(page_bar);
		getDisplayPages(list_id, 1, total_pages);
		
		$(".search_pages_row").on("click", function( event ) {
			var getSearchPages = $(this).data("pages");
			getProductSearchData(getSearchPages);
			var getCurrentPages = $(".search_record").data("current_pages");
			getDisplayPages(list_id, getCurrentPages, total_pages);
		});		
	}
	
	function setKeywordStorage(){
		var keyword = getValue("product_search");
		
		var jqxhr = $.ajax({
			type: "POST",
			url: ROOT + "keyword_product",
			data: { keyword: keyword },
			async: false
		});

		jqxhr.done(function(product_list) {});		
	}

	product_action.hide();
	getChangePages();
	
	if(keyword != ""){
		getProductSearchData();
		$("#product_item_list tr").on("click", function( event ) {
			SelectProduct($(this));
		});			
	}else if (keyword == '' ) {
			$(".search_record").remove();
			$("#product_item_list tr").show();
			$(".search_pages_list").remove();
			$(".list_pagination").show();	
			// $("#product_item_list tr").on("click", function( event ) {
				// SelectProduct($(this));
			// });				
		}

	$("#product_search").focus();
	
	$("#product_search").donetyping(getProductSearchData, 1500);
	
	$("#product_search").donetyping(getChangePages, 1500);
	
	$("#product_search").donetyping(setKeywordStorage, 1500);
	
	$("#product_item_list tr").on("click", function( event ) {
		SelectProduct($(this));
	});	
	
    $("#btn-order-add").on("click", function( event ) {
	
        var product_upc = getValue("product_upc");
        var product_name = getValue("product_name");
        var product_brand = getValue("product_brand_id");
        var product_brand_name = getValue("product_brand");
        var product_category = getValue("product_category");

        var valid = true;
        var error_message = "";

        if ( product_upc == "" ) {
            error_message += "請輸入貨品號碼\n";
            valid = false;
        }

        if ( product_name == "" && product_brand_name == ""  ) {
            error_message += "請搜尋貨品牌子\n";
            valid = false;
        }

        if ( product_category == "" ) {
            error_message += "請選擇貨品類別\n";
            valid = false;
        }
		
        if ( product_brand == "" ) {
            error_message += "請選擇牌子名稱\n";
            valid = false;
        }		
		
        if ( !valid ) {
            alert(error_message);
            return false;
        }
		
		var ans = window.confirm("你確定要新增此貨品嗎?");
		if ( !ans ) {
			return false;
		}		
		
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "create_product",
            async: false,
            data: $("#form_product_create").serialize()
        });

        jqxhr.done(function(result) {
            result = parseInt(result);
            if ( result != 0 ) {
                var item_row = ' \
                <tr data-item_id=' + result + ' class="new_product"> \
                    <td width="165.188">' + product_upc + '</td> \
                    <td width="330.391">' + product_name + '</td> \
                    <td width="165.188">' + product_brand_name + '</td> \
                    <td width="165.188">' + product_category + '</td> \
                </tr>';
                $("#product_item_list").prepend(item_row);
				CancelFormAction();
				alert("已成功新增貨品");
				$(".new_product").on("click", function() {
					SelectProduct($(this));
				});					
            } else {
				alert("未能新增貨品, 請稍後再試");
            }
        });
        return false;
    });
	
	$("#btn-order-edit").on("click", function( event ){
		var ans = window.confirm("你確定要更改此貨品嗎?");
		if ( !ans ) {
			return false;
		}
		var item_id = $(".info").data("item_id");
		
		var product_upc = getProductUpc.val();
		var product_brand = getProductBrandID.val();
		var product_name = getProductName.val();
		var product_spec = getProductSpec.val();
		var product_remark = getProductRemark.val();
		var product_category = getProductCategory.val();

        var jqxhr = $.ajax({
           type: "POST",
           url: ROOT + "edit_product",
           data: 
		   { 
				product_id: item_id,
				product_upc: product_upc,
				product_brand: product_brand,
				product_name: product_name,
				product_spec: product_spec,
				product_remark: product_remark,
				product_category: product_category
		   },
           async: false
        });

        jqxhr.done(function(result) {
           result = parseInt(result);
           if ( result != 1 ) {
               alert("未能更改貨品, 請稍後再試");
           } else {
               // $(".info").remove();
               alert("已成功更改貨品");
           }
        }); 
	});	
	
	$("#btn-order-delete").on("click", function( event ) {
        var ans = window.confirm("你確定要刪除此貨品嗎?");
        if ( !ans ) {
           return false;
        }

        var item_id = $(".info").data("item_id");

        var jqxhr = $.ajax({
           type: "POST",
           url: ROOT + "remove_product",
           data: { product_id: item_id },
           async: false
        });

        jqxhr.done(function(result) {
           result = parseInt(result);
           if ( result != 1 ) {
               alert("未能刪除貨品, 請稍後再試");
           } else {
               $(".info").remove();
			   $("#product_search").val('');
			   CancelFormAction();
               alert("已成功刪除貨品");
           }
        }); 
	});	
	
	$("#btn-order-reset").on("click", function( event ){
		CancelFormAction();
		SelectProduct($("#product_item_list tr"));
	});	
	
	/* End Product Script */
	
	/* Start Brand Search Pop up script */
	
	function SelectBrand(changeClass){
		if ( changeClass.hasClass("info") ) {
			changeClass.removeClass("info");
		} else {
			$(".info").removeClass("info");
			changeClass.addClass("info");
		}	
	}
	
	function SelectThisBrand(changeClass){
		var getBrandName = changeClass.find(".col_brand_name").text();
		var getBrandID = changeClass.find(".col_brand_id").text();
		getProductBrand.val(getBrandName);
		getProductBrandID.val(getBrandID);
		$("#brand_list_popup").modal('hide');	
	}
	
	function getBrandSearchData(pages){
		var data = '';
		var i = 0;
		var keyword = getValue("search_brand_name");
		if(typeof(pages) === 'undefined'){
			pages = "1";
		}
		$("#popup_list_items .popup_list_item_row").hide();
		$(".search_brand_record").remove();
		 var jqxhr = $.ajax({
			type: "POST",
			url: ROOT + "search_brand_popup_list",
			data: { 
				keyword: keyword,
				page: pages
			},
			async: false
		 });

		jqxhr.done(function(brand_list) {
			if (brand_list !== '') {
				data = $.parseJSON(brand_list);
				$.each(data, function(){
					var item_row = ' \
						<div class="popup_list_item_row search_brand_record" data-brand_id="' + data[i].id + '"> \
							<span class="list_column col_brand_id">' + data[i].id + '</span> \
							<span class="list_column col_brand_name">' + data[i].name + '</span> \
							<span class="list_column col_brand_remark">' + data[i].remark + '</span> \
							<span class="list_column col_brand_update_by">' + data[i].update_by + '</span> \
						</div>';		
					$("#popup_list_items").prepend(item_row);
					i++;
				});				
			}
		});	

		$("#popup_list_items .popup_list_item_row").on("click", function() {
			SelectBrand($(this));
		});		

		$("#popup_list_items .popup_list_item_row").on("dblclick", function() {
			SelectThisBrand($(this));
		});				
		
		if(keyword == '' && pages === '1'){
			$("#popup_list_items .popup_list_item_row").show();
			$(".search_brand_record").remove();
		}	
	}
	
	
	$("#search_brand_name").donetyping(getBrandSearchData, 1500);
	
	$("#search_brand_button").on("click", function( event ) {
		event.preventDefault();
		$("#brand_list_popup").modal();
	});	
	
	$("#close_popup_button").on("click", function( event ){
		$("#brand_list_popup").modal('hide');
	});
	
	$("#search_brand_reset").on("click", function( event ){
		getProductBrand.val('');
		getProductBrandID.val('');
	});
	
	$("#popup_list_items .popup_list_item_row").on("dblclick", function() {
		SelectThisBrand($(this));
	});		
	
	$("#popup_list_items .popup_list_item_row").on("click", function() {
		SelectBrand($(this));
	});	
	
	$(".brand_list").on("click", function( event ) {
		var getBrandPages = $(this).data("brand-page");
		getBrandSearchData(getBrandPages);
		// getDisplayPages(list_id, getCurrentPages, total_pages);
	});			
	
	/* End Brand Search Pop up script */
	
	/* Start page pagination */
	function getChangePages(){	
		currentPage = parseInt(currentPage);
		total_product_page = parseInt(total_product_page);		
		getDisplayPages( list_id, currentPage, total_product_page );
	}
	
});