"use strict";
var orderObject = new Object();

$(function() {
	var category_title = $("#category_title");
	var category_submit = $("#category_submit");
	var category_edit = $("#category_edit");
	var category_search = getValue("category_search");

	function fnKeyBinding() {
      shortcut.add("Enter", function() {
        getCategorySearchData();
      });
	}	
	
	function checkSearchValue(){
		if(category_search===""){
			checkCategorySearch();
			$("#category_search").donetyping(getCategorySearchData, 1500);
		}else{
			getCategorySearchData();		
		}
	}	
	
	function SelectCategory(changeClass){
		if ( changeClass.hasClass("info") ) {
			changeClass.removeClass("info");
			CancelFormAction();
		} else {
			$(".info").removeClass("info");
			changeClass.addClass("info");
			var item_id = $(".info").data("item_id");
			category_title.text('Category Information');
			category_submit.hide();
			category_edit.show();
			getCategorytData(item_id);
		}
	}
	
	function getCategorytData(item_id){
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "get_category",
            async: false,
			data : { category_id: item_id }
        });

        jqxhr.done(function(result) {
            result = JSON.parse(result);
			var category_data = result.category_data;
			$.each( category_data, function ( key, data ) {
				var category_id = data.id;
				var category_name = data.name;
				$("#category_id").val(category_id);
				$("#category_name").val(category_name);
			});
		});
	}	
	
	function getCategorySearchData(){
		var i = 0;
		var search_keyword = getValue("category_search");
		
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "category_search",
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
					<div id="list_item_row" class="list_item_row search_category" data-item_id="'+ result[i].id +'"> \
						<span class="list_column col_no_row">'+ (i+1) +'</span> \
						<span class="list_column col_category_name">'+ result[i].name +'</span> \
						<span class="list_column col_update_date">'+ result[i].last_update +'</span> \
					</div>';
				i++;			
				$("#category_item_list").append(item_row);
			});			
		});	
		
		$("#category_item_list .search_category").on("click", function(){
			SelectCategory($(this));
		});		
		checkCategorySearch();
		setSession();
	}

	function checkCategorySearch(){
		var category_search = getValue("category_search");	
		if(category_search===""){
			$("#category_item_list .search_category").remove();
			$("#category_item_list .list_item_row").show();
		}
	}
	
	function CancelFormAction(){
		$("#form_category_create")[0].reset();
		category_title.text('Category Create');
		category_submit.show();
		category_edit.hide();
	}
	
	function setSession(){
		var category_search = getValue("category_search");
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "set_category",
            async: false,
			data : { 
				setSession: "1",
				keyword: category_search
			}
        });
	}

	$("#btn-order-add").on("click", function(){
		var ans = window.confirm("你確定要加入此目錄嗎?");
		if ( !ans ) {
			return false;
		}
		
		var category_name = getValue("category_name");
		
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "create_category",
            async: false,
			data: { 
				category_name: category_name
			}
        });

        jqxhr.done(function(result) {
            result = JSON.parse(result);
            if (result.status == "success") {
                $("#resultPage .modal-body").html('<div class="alert alert-success" role="alert">新增成功</div>');
                $("#confirmPage").modal("hide");
                $("#resultPage").modal();
				
				$(".btn_category_close").on("click", function(event) {
					location.reload();
				});
            }
        });		
	})

	$("#btn-order-edit").on("click", function(){
		var ans = window.confirm("你確定要更改此目錄嗎?");
		if ( !ans ) {
			return false;
		}	
		var category_item_id = getValue("category_id");
		var category_name = getValue("category_name");
		
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "edit_category",
            async: false,
			data: { 
				category_id: category_item_id,
				category_name: category_name
			}
        });

        jqxhr.done(function(result) {
            result = JSON.parse(result);
            if (result.status == "success") {
                $("#resultPage .modal-body").html('<div class="alert alert-success" role="alert">修改成功</div>');
                $("#confirmPage").modal("hide");
                $("#resultPage").modal();
				$(".btn_category_close").on("click", function(event) {
					location.reload();
				});								
            }
        });				
	});

	$("#btn-order-delete").on("click", function(){
		var ans = window.confirm("你確定要刪除此目錄嗎?");
		if ( !ans ) {
			return false;
		}	
		var category_item_id = getValue("category_id");
		
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "remove_category",
            async: false,
			data: { 
				category_id: category_item_id,
			}
        });

        jqxhr.done(function(result) {
            result = JSON.parse(result);
            if (result.status == "success") {
                $("#resultPage .modal-body").html('<div class="alert alert-success" role="alert">刪除成功</div>');
                $("#confirmPage").modal("hide");
                $("#resultPage").modal();
				$(".btn_category_close").on("click", function(event) {
					location.reload();
				});				
            }
        });				
	});
	
	$("#btn-order-reset").on("click", function(){
		SelectCategory($(".list_item_row"));
	});
	
	$("#category_item_list .list_item_row").on("click", function(){
		SelectCategory($(this));
	});		

	fnKeyBinding();	
	checkSearchValue();	
	
});
