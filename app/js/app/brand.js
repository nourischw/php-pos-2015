"use strict";
var orderObject = new Object();

$(function() {
	var brand_title = $("#brand_title");
	var brand_submit = $("#brand_submit");
	var brand_edit = $("#brand_edit");
	var brand_search = getValue("brand_search");

	function fnKeyBinding() {
      shortcut.add("Enter", function() {
        getBrandSearchData();
      });
	}		
	
	function checkSearchValue(){
		if(brand_search===""){
			checkBrandSearch();
		}else{
			getBrandSearchData();		
		}
	}	
	
	function SelectBrand(changeClass){
		console.log(changeClass);
		if ( changeClass.hasClass("info") ) {
			changeClass.removeClass("info");
			CancelFormAction();
		} else {
			$(".info").removeClass("info");
			changeClass.addClass("info");
			var item_id = $(".info").data("item_id");
			brand_title.text('Brand Information');
			brand_submit.hide();
			brand_edit.show();
			getBrandtData(item_id);
		}
	}
	
	function getBrandtData(item_id){
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "get_brand",
            async: false,
			data : { brand_id: item_id }
        });

        jqxhr.done(function(result) {
            result = JSON.parse(result);
			var brand_data = result.brand_data;
			$.each( brand_data, function ( key, data ) {
				var brand_id = data.id;
				var brand_name = data.name;
				var brand_remark = data.remark;
				$("#brand_id").val(brand_id);
				$("#brand_name").val(brand_name);
				$("#brand_remark").val(brand_remark);
			});
		});
	}	
	
	function getBrandSearchData(){
		var i = 0;
		var search_keyword = getValue("brand_search");
		
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "brand_popup_list",
            async: false,
			data : { 
				keyword: search_keyword,
				page: "1"
			}
        });

        jqxhr.done(function(result) {
			$(".list_item_row").hide();
            result = JSON.parse(result);
			$.each( result, function () {
				if(result[i].update_by===""){
					result[i].update_by = "---";
				}
				var item_row = ' \
					<div id="list_item_row" class="list_item_row search_brand" data-item_id="'+ result[i].id +'"> \
						<span class="list_column col_no_row">'+ (i+1) +'</span> \
						<span class="list_column col_brand_name">'+ result[i].name +'</span> \
						<span class="list_column col_update_date">'+ result[i].update_time +'</span> \
						<span class="list_column col_update_by">'+ result[i].update_by +'</span> \
					</div>';
				i++;			
				$("#brand_item_list").append(item_row);
			});			
		});	
		
		$("#brand_item_list .search_brand").on("click", function(){
			SelectBrand($(this));
		});		
		checkBrandSearch();
		setSession();
	}

	function checkBrandSearch(){
		var brand_search = getValue("brand_search");	
		if(brand_search===""){
			$("#brand_item_list .search_brand").remove();
			$("#brand_item_list .list_item_row").show();
		}
	}
	
	function CancelFormAction(){
		$("#form_brand_create")[0].reset();
		brand_title.text('Brand Create');
		brand_submit.show();
		brand_edit.hide();
	}
	
	function setSession(){
		var brand_search = getValue("brand_search");
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "set_brand",
            async: false,
			data : { 
				setSession: "1",
				keyword: brand_search
			}
        });
	}

	$("#btn-order-add").on("click", function(){
		var ans = window.confirm("你確定要加入此牌子嗎?");
		if ( !ans ) {
			return false;
		}
		
		var brand_name = getValue("brand_name");
		var brand_remark = getValue("brand_remark");
		
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "brand/create",
            async: false,
			data: { 
				brand_name: brand_name,
				brand_remark: brand_remark
			}
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
	})

	$("#btn-order-edit").on("click", function(){
		var ans = window.confirm("你確定要更改此牌子嗎?");
		if ( !ans ) {
			return false;
		}	
		var brand_item_id = getValue("brand_id");
		var brand_name = getValue("brand_name");
		var brand_remark = getValue("brand_remark");
		
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "brand/edit",
            async: false,
			data: { 
				brand_id: brand_item_id,
				brand_name: brand_name,
				brand_remark: brand_remark
			}
        });

        jqxhr.done(function(result) {
            result = JSON.parse(result);
            if (result.status == "success") {
                $("#resultPage .modal-body").html('<div class="alert alert-success" role="alert">修改成功</div>');
                $("#confirmPage").modal("hide");
                $("#resultPage").modal();
				$(".btn_brand_close").on("click", function(event) {
					location.reload();
				});								
            }
        });				
	});

	$("#btn-order-delete").on("click", function(){
		var ans = window.confirm("你確定要刪除此牌子嗎?");
		if ( !ans ) {
			return false;
		}	
		var brand_item_id = getValue("brand_id");
		
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "brand/remove",
            async: false,
			data: { 
				brand_id: brand_item_id,
			}
        });

        jqxhr.done(function(result) {
            result = JSON.parse(result);
            if (result.status == "success") {
                $("#resultPage .modal-body").html('<div class="alert alert-success" role="alert">刪除成功</div>');
                $("#confirmPage").modal("hide");
                $("#resultPage").modal();
				$(".btn_brand_close").on("click", function(event) {
					location.reload();
				});				
            }
        });				
	});
	
	$("#btn-order-reset").on("click", function(){
		SelectBrand($(".list_item_row"));
	});
	
	$("#brand_search").donetyping(getBrandSearchData, 1500);
	
	$("#brand_item_list .list_item_row").on("click", function(){
		SelectBrand($(this));
	});		

	checkSearchValue();	
	fnKeyBinding();	
	
});
