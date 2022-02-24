var total_list_items = 0;

function closePopupFormBox() {
	$("#fixed_background, .list_popup_form_box").stop(true, true).fadeOut("fast");
}

function checkCheckAll() {
	var checked_all = ( $(".list_selected_row").length === total_list_items ) ? true : false;
	$("#list_checkbox_all").prop("checked", checked_all);
}

/* Pagination functions */

// Get page by page button
function getPage( page, last_page, type ) {
	switch (type) {
		case "first":
			return 1;
		case "prev":
			page--;
			return page;
		case "next":
			page++;
			return page;
		case "last":
			return last_page;
	}
}

function disablePrevButtons( list_id ) {
	$("#" + list_id + " #page_bar .prev_pages")
		.addClass("disabled")
		.find(".page_button")
		.prop("disabled", true);
}

// Update paging bar
function getDisplayPages( list_id, current_page, total_page ) {
	$("#" + list_id + " #page_bar .list_page_row")
		.removeClass("active")
		.eq(current_page-1)
		.addClass("active");

	var start_page = 0;
	var end_page = 10;

	if ( current_page > 5 ) {
		var start_page = current_page - 5;
		var end_page = current_page + 5;
		
		if ( end_page > total_page ) {
			end_page = total_page;
			start_page = Math.max(1, (total_page - 10));
		}
	}
	
	$("#" + list_id + " #page_bar .list_page_row").hide();
	for ( i = start_page; i < end_page; i++ ) {
		$("#" + list_id + " #page_bar .list_page_row").eq(i).show();
	}

	if ( current_page === 1 ) {
		$("#" + list_id + " .prev_buttons")
			.addClass("disabled")
			.find(".page_button")
			.removeAttr("href")
			.prop("disabled", true);
	} else {
		$("#" + list_id + " .prev_buttons")
			.removeClass("disabled")
			.find(".page_button")
			.prop("disabled", false);
	}
	
	if ( current_page === total_page ) {
		$("#" + list_id + " .next_buttons")
			.addClass("disabled")
			.find(".page_button")
			.removeAttr("href")
			.prop("disabled", true);
	} else {
		$("#" + list_id + " .next_buttons")
			.removeClass("disabled")
			.find(".page_button")
			.prop("disabled", false);
	}	
}

function reloadPaginationBar( list_id, total_pages ) {
	$(".list_page_row").remove();
	
	var page_bar = ' \
		<li class="prev_buttons"><a href="#" aria-label="First" class="page_button" data-type="first"><span aria-hidden="true">&laquo;</span></a></li> \
		<li class="prev_buttons"><a href="#" aria-label="Previous" class="page_button" data-type="prev"><span aria-hidden="true">&lsaquo;</span></a></li>';
	for ( var i = 1; i <= total_pages; i++ ) {
		page_bar += '<li class="list_page_row"><a href="#" class="page_button" data-type="page">' + i + '</a></li>';
	}
	page_bar += ' \
		<li class="next_buttons"><a href="#" aria-label="Next" class="page_button" data-type="next"><span aria-hidden="true">&rsaquo;</span></a></li> \
		<li class="next_buttons"><a href="#" aria-label="Last" class="page_button" data-type="last"><span aria-hidden="true">&raquo;</span></a></li>';

	$("#" + list_id + " #page_bar")
		.html(page_bar)
		.find(".list_page_row")
		.eq(0)
		.addClass("active");
	
	if ( total_pages > 10 ) {
		for ( var i = 10; i < total_pages; i++ ) {
			$("#" + list_id + " #page_bar .list_page_row").eq(i).hide();
		}
	}

	disablePrevButtons();
}

$(function() {
	$("#item_list input[type=checkbox]").prop("checked", false);

	// Close popup form
	$(".popup_close_button").on("click", closePopupFormBox);
	
	// Checkbox function
	$("#list_checkbox_all").on("click", function() {
		var checkbox_all_checked = ( $(this).is(":checked") ) ? true : false;
		$(".list_item_checkbox").prop("checked", checkbox_all_checked);
		if ( checkbox_all_checked ) {
			$("#list_items .list_item_row").addClass("list_selected_row");
		} else {
			$("#list_items .list_item_row").removeClass("list_selected_row");
		}
	});
	
	$("#list_items").on("click", ".list_checkbox", function( event ) {
		event.stopPropagation();
		if ( $(this).is(":checked") ) {
			$(this).closest(".list_item_row").addClass("list_selected_row")
		} else {
			$(this).closest(".list_item_row").removeClass("list_selected_row");
		}
		checkCheckAll();
	});

	// Item Row function
	$("#item_list").on("click", ".list_item_row", function( event ) {
		if ( $(this).hasClass("list_selected_row") ) {
			$(this)
				.removeClass("list_selected_row")
				.find(".list_item_checkbox")
				.prop("checked", false);
		} else {
			$(this)
				.addClass("list_selected_row")
				.find(".list_item_checkbox")
				.prop("checked", true);
		}
		
		checkCheckAll();
	});

	$(".clear_search_button").on("click", function() {
		var page_name = getValue("page_name");
		window.location = ROOT + "/" + page_name;
	});

	$("#close_popup_button").on("click", function() {
		$(this).closest(".popup_list_block").modal("hide");
	});
});