"use strict";

$(function() {
	var product_title = $("#form_title");
	var product_action = $("#product_action");
	var product_submit = $("#product_submit");
	var product_edit = $("#product_edit");
	var getProductID = $("#product_id");
	var getProductImages = $(".product_images");
	var getProductUpc = $("#product_upc");
	var getUnitPrice = $("#unit_price");
	var getReferenceCost = $("#reference_cost");
	var getProductBrand = $("#product_brand");
	var getProductBrandID = $("#product_brand_id");
	var getProductName = $("#product_name");
	var getProductSpec = $("#product_spec");
	var getProductRemark = $("#product_remark");
	var getProductCategory = $("#product_category");
	var getRequiredImei = $("input:checkbox[name='required_imei']");
	var product_detail = $(".product_detail");
	var keyword = getValue("product_search");
	var currentPage = getValue("currentPage");
	var total_product_page = getValue("lastPage");
	
	var keyEnter = 13;
	
	var addStatus = 1;
	var editStatus = 2;

	var $list_id = $("#product");
	var list_id = "product";

	/* Start Product Script */
	fnEnterSearch();
	product_action.hide();
	getChangePages();

	if(keyword != ""){
		getProductSearchData();
		$(".list_item_row").on("click", function( event ) {
			SelectProduct($(this));
		});
	}else if (keyword == '' ) {
		$(".search_record").remove();
		$(".list_item_row").show();
		$(".search_pages_list").remove();
		$(".list_pagination").show();
	}

	$("#product_search").focus();

	// $("#product_search").on("click change", function(){
		// shortcut.add("ENTER", function() {
			// getProductSearchData();
			// getChangePages();
			// setKeywordStorage();
		// });
	// });

	// $("#product_search").donetyping(getProductSearchData, 1500);

	$("#product_search").donetyping(getChangePages, 1500);

	$("#product_search").donetyping(setKeywordStorage, 1500);

	function fnEnterSearch(){
		$("#product_search").keypress(function(e) {
			if(e.which == keyEnter) {
				getProductSearch();
			}
		});			
	}	
	
	function getValue(id) {
		return $.trim($("#" + id).val());
	}

	function getProductSearch(){
		getProductSearchData();
		getChangePages();
		setKeywordStorage();	
	}
	
	function getProductData(item_id){
		var clickResult = '1';
		var data = '';
		var product_images;
		var jqxhr = $.ajax({
			type: "POST",
			url: ROOT + "product/search",
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
					product_images = data.photo;
					if(product_images == data.id + ".png"){
						$(".product_images_block").show();
						getProductImages.prepend('<img class="product_pic" src="'+ROOT+'app/images/product/' +product_images + '">');
					}else{
						$(".product_images_block").hide();
						$(".product_pic").remove();
					}
					getProductID.val(data.id);
					getProductUpc.val(data.barcode);
					getProductBrandID.val(data.brand_id);
					getProductBrand.val(data.brand_name);
					getProductName.val(data.name);
					getUnitPrice.val(data.unit_price);
					getReferenceCost.val(data.reference_cost);
					getProductSpec.val(data.product_spec);
					getProductRemark.val(data.remark);
					$( "#product_category option[value='" + data.category_id + "']").prop("selected", true);
					( data.required_imei == '1' ) ? getRequiredImei.prop( "checked", true ) : getRequiredImei.prop( "checked", false );
				})
			}
		});
		
		$(".zoom_images_btn").on("click", function(){
			window.open(ROOT + "app/images/product/" + item_id + ".png");
		});		
		
		$(".remove_images_btn").on("click", function(){
			$(".remove_images").val(item_id);
			$(".zoom_images_btn").hide();
			$(".product_pic").hide();
			$(this).hide();
			$(".return_images_btn").show();
		});		
		
		$(".return_images_btn").on("click", function(){
			$(".remove_images").val('');
			$(this).hide();
			$(".remove_images_btn").show();
			$(".zoom_images_btn").show();
			$(".product_pic").show();
		});
		
	}

	function SelectProduct(changeClass){
		if ( changeClass.hasClass("info") ) {
			$("#product_upc").prop('disabled', false);
			changeClass.removeClass("info");
			$(".product_images_block").hide();
			$(".product_pic").remove();
			CancelFormAction();
		} else {
			$(".info").removeClass("info");
			changeClass.addClass("info");
			$(".product_images_block").hide();
			$(".product_pic").remove();			
			$("#product_upc").prop('disabled', true);
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
		$("#product_upc").val('');
		$("#product_brand").val('');
		$("#product_brand_id").val('');
		$("#product_name").val('');
		$("#unit_price").val('');
		$("#reference_cost").val('');
		$("#product_spec").val('');
		$("#product_remark").val('');
		$("#product_category option").prop("selected", false);
		$("#required_imei").prop( "checked", false );
		$(".product_images_block").hide();
		$(".product_pic").remove();
		
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
			$(".list_item_row").hide();
			$(".search_record").remove();
			 var jqxhr = $.ajax({
				type: "POST",
				url: ROOT + "product/search",
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
							<div class="list_item_row search_record" data-item_id="' + data.id + '" data-current_pages="' + search_current_pages + '"> \
							<span class="list_checkbox_column">' + (key + 1) +'</span> \
							<span class="list_column col_product_upc">' + data.barcode + '</span> \
							<span class="list_column col_product_name">' + data.name + '</span> \
							<span class="list_column col_brand_name">' + data.brand_name + '</span> \
							<span class="list_column col_product_category">' + data.category + '</span> \
							</div>';
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
			$(".list_item_row").show();
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
			url: ROOT + "product/keyword",
			data: { keyword: keyword },
			async: false
		});

		jqxhr.done(function(product_list) {});
	}

	function uploadImages(product_id, file_data, status){
		var form_data = new FormData();
		
		form_data.append('id', product_id);
		form_data.append('file', file_data);

		$.ajax({
			url: ROOT + "product/upload", 
			dataType: 'text',
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,                         
			type: 'post',
			success: function(result){
				if (result == '1'){
					if(status == 1){
						InsertProductList(product_id);
					}
					if(status == 2){
						var product_unit_price = getUnitPrice.val();
						$(".info .col_product_unit_price").text("$" + product_unit_price);
						alert("已成功更改貨品");
						location.reload();
					}
				}
			}
		 });
	}
	
	function InsertProductList(product_id){
		var product_upc = getValue("product_upc");
		var product_name = getValue("product_name");
		var product_unit_price = getValue("unit_price");
		var product_reference_cost = getValue("reference_cost");
		var product_brand = getValue("product_brand_id");
		var product_brand_name = getValue("product_brand");
		var product_category = getValue("product_category");
		var product_category_name = $("#product_category option:selected").text();
		var get_required_imei = $("input:checkbox[name='required_imei']");
		
		var item_row = ' \
		<div class="list_item_row new_info new_product" data-item_id=' + product_id + '> \
			<span class="list_checkbox_column">N</span> \
			<span class="list_column col_product_upc">' + product_upc + '</span> \
			<span class="list_column col_product_name">' + product_name + '</span> \
			<span class="list_column col_product_unit_price">$' + product_unit_price + '</span> \
			<span class="list_column col_brand_name">' + product_brand_name + '</span> \
			<span class="list_column col_product_category">' + product_category_name + '</span> \
		</div>';
		$("#product_item_list").prepend(item_row);
		
		CancelFormAction();
		alert("已成功新增貨品");
		
		$(".new_product").on("click", function() {
			$("#btn-order-add").show();
			$('#check_product_upc').html('');
			SelectProduct($(this));
		});
	};
	
	$(".list_item_row").on("click", function( event ) {
		$("#btn-order-add").show();
		$('#check_product_upc').html('');
		SelectProduct($(this));
	});
	
	$("#product_upc").donetyping(checkProductUpc);
	
	function checkProductUpc(){
		$('#check_product_upc').html('');
		var product_upc = getValue("product_upc");
		if(product_upc != ""){
			var jqxhr = $.ajax({
				type: "POST",
				url: ROOT + "product/check",
				async: false,
				data: { product_upc: product_upc }
			});

			jqxhr.done(function(result) {
			  if(result == 0){  
					$('#check_product_upc').html('Product UPC already exists');  
				}else{  
					$('#check_product_upc').html('');
				}
			});
		}
	}
	
	$("#btn-order-add").on("click", function( event ) {
		event.preventDefault();
		var FormObject = new Object();
		var file_data = $('#upload_product_images').prop('files')[0];
		var product_upc = getValue("product_upc");
		var product_name = getValue("product_name");
		var product_unit_price = getValue("unit_price");
		var product_reference_cost = getValue("reference_cost");
		var product_brand = getValue("product_brand_id");
		var product_brand_name = getValue("product_brand");
		var product_category = getValue("product_category");
		var product_category_name = $("#product_category option:selected").text();
		var get_required_imei = $("input:checkbox[name='required_imei']");
		var required_imei = (get_required_imei.is(":checked")) ? 1 : 0;
		var check_product_upc = $("#check_product_upc").text();
		var valid = true;
		var error_message = "";

		if ( product_upc == "" ) {
			error_message += "Please input product upc\n";
			valid = false;
		}

		if ( product_name == "" && product_brand_name == ""  ) {
			error_message += "Please search product brand\n";
			valid = false;
		}

		if ( product_category == "" ) {
			error_message += "Please input product category\n";
			valid = false;
		}

		if ( product_brand == "" ) {
			error_message += "Please input product brand\n";
			valid = false;
		}

		if ( product_unit_price == "" ) {
			error_message += "Please input unit price\n";
			valid = false;
		}
		if ( product_reference_cost == "" ) {
			error_message += "Please input reference cost\n";
			valid = false;
		}
		
		if(check_product_upc != ""){
			error_message += "Product UPC already exists\n";
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
		
		FormObject.product_upc = $("#product_upc").val();
		FormObject.product_brand = $("#product_brand").val();
		FormObject.product_brand_id = $("#product_brand_id").val();
		FormObject.product_name = $("#product_name").val();
		FormObject.unit_price = $("#unit_price").val();
		FormObject.reference_cost = $("#reference_cost").val();
		FormObject.product_spec = $("#product_spec").val();
		FormObject.product_remark = $("#product_remark").val();
		FormObject.product_category = $("#product_category").val();
		FormObject.required_imei = required_imei;

		var jqxhr = $.ajax({
			type: 'POST',
			url: ROOT + "product/create",
			async: false,
			data: FormObject
		});

		jqxhr.done(function(result) {
			result = parseInt(result);
			if ( result != 0 ) {
				if(typeof file_data != 'undefined'){
					uploadImages(result, file_data, addStatus);
				}else{
					$("#btn-order-add").hide();
					InsertProductList(result);
				}
			}else{
				alert("未能新增貨品, 請稍後再試");
				return false;
			}
		});
	});

	$("#btn-order-edit").on("click", function( event ){
		var ans = window.confirm("你確定要更改此貨品嗎?");
		if ( !ans ) {
			return false;
		}
		var item_id = $(".info").data("item_id");

		var file_data = $('#upload_product_images').prop('files')[0];
		var product_upc = getProductUpc.val();
		var product_brand = getProductBrandID.val();
		var product_name = getProductName.val();
		var product_unit_price = getUnitPrice.val();
		var product_reference_cost = getReferenceCost.val();
		var product_spec = getProductSpec.val();
		var product_remark = getProductRemark.val();
		var product_category = getProductCategory.val();
		var get_required_imei = $("input:checkbox[name='required_imei']");
		var required_imei = (get_required_imei.is(":checked")) ? 1 : 0;
		var remove_images = $(".remove_images").val();
		
		var jqxhr = $.ajax({
		   type: "POST",
		   url: ROOT + "product/edit",
		   data:
	   {
			product_id: item_id,
			product_upc: product_upc,
			product_brand: product_brand,
			product_name: product_name,
			product_unit_price: product_unit_price,
			product_reference_cost: product_reference_cost,
			product_spec: product_spec,
			product_remark: product_remark,
			product_category: product_category,
			required_imei: required_imei,
			remove_images: remove_images
	   },
			async: false
		});

		jqxhr.done(function(result) {
		   result = parseInt(result);
		   if ( result != 1 ) {
			   alert("未能更改貨品, 請稍後再試");
		   } else {
				if(typeof file_data != 'undefined'){
					uploadImages(item_id, file_data, editStatus);
				}else{
					var product_unit_price = getUnitPrice.val();
					$(".info .col_product_unit_price").text("$" + product_unit_price);
					$(".remove_images").val('');
					alert("已成功更改貨品");
					location.href= ROOT + "product/1";
				}
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
       url: ROOT + "product/remove",
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

	$(".btn-order-reset").on("click", function(){
		if ( $("#product_item_list").find(".info") ) {
			$("#product_item_list").find(".info").removeClass("info");	
			CancelFormAction();
		}
	});

	$("#get_brand_remove_button").on("click", function(){
		$("#product_brand").val('');
		$("#product_brand_id").val('');
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
			url: ROOT + "brand/search",
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

	$("#search_brand_name").on("keyup", function( event ) {
		var keycode = ( event.keyCode ? event.keyCode : event.which );
		if ( keycode == 13 ) {
			getBrandSearchData();
		}
	});	
	
	$("#search_brand_button").on("click", showBrandPopupList);

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
	
	/* End page pagination */
	
	function showBrandPopupList() {
		var search_product = getValue("product_brand");
		$("#search_brand_name").val(search_product);
		// updateList("brand_list_popup");
		$("#brand_list_popup").modal();
		getBrandSearchData();
	}
	
	$("#product_brand").on("keyup", function( event ) {
		var keycode = ( event.keyCode ? event.keyCode : event.which );
		if ( keycode == 13 ) {
			showBrandPopupList();
		}
	});	
	
});
