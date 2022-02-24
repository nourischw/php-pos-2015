var total_list_items = 0;
var total_records = 0;

function checkCheckAll() {
	var checked_all = ( $(".list_selected_row").length === total_list_items ) ? true : false;
	$("#list_checkbox_all").prop("checked", checked_all);
}

function showNoRecordMessage( list_id ) {
	$("#" + list_id + " .list_no_items_row").show();
	$("#" + list_id + " #total_records").text(0);
	$("#" + list_id + " #page_bar").empty();
	disable("#" + list_id + " .list_page_buttons");
	disable(".list_delete_button");
	$("#list_checkbox_all").removeAttr("checked");
	disable("#list_checkbox_all");
	$(".paging_bar_column").hide();
}

function countTotalListRows( list_id ) {
	total_list_items = $("#list_items .list_item_row").length;
	$("#total_records").text(total_list_items);

	if ( total_list_items < 1 ) {
		if ( total_records < 1 ) {
			showNoRecordMessage(list_id);
			$("#list_checkbox_all").removeAttr("checked");
			disable("#list_checkbox_all");
		} else {
			updateList(list_id);
			if ( $("#list_items .list_item_row").length < 1 ) {
				showNoRecordMessage(list_id);
			}

			enable(".list_page_buttons");
		}
	}
}

function updateList( list_id, page ) {
	var page = page || 1;
	var is_popup_list = $("#" + list_id).hasClass("popup_list") ? true : false;
	var list_type = ( is_popup_list ) ? " #popup_list_items" : " #list_items";
	var url = ( is_popup_list ) ? list_id : list_id.slice(0, -5) + "/search";
	
	var jqxhr = $.ajax({
        type: 'POST',
        url: ROOT + url,
        async: false,
        data: $("#form_" + list_id).serialize() + "&page=" + page
    });

	jqxhr.done(function( record_list ) {
		$("#" + list_id + list_type).html(record_list);
		total_records = $("#" + list_id + "_total_records").val();
		var total_pages = parseInt($("#" + list_id + "_total_pages").val());

		if ( total_records < 1 ) {
			showNoRecordMessage(list_id, list_type);
			return false;
		}

		enable(".list_page_buttons");
		enable(".list_delete_button");
		enable("#list_checkbox_all");

		$("#" + list_id + " .list_no_items_row").hide();
		$(".paging_bar_column").hide();

		if ( total_pages > 1 ) {
			$(".paging_bar_column").show();
			var disable_prev = ( page === 1 ) ? "disabled" : "";
			var disable_next = ( page === total_pages ) ? "disabled" : "";
			var start_page = 1;
			var end_page = Math.min(10, total_pages);

			if ( page > 5 ) {
				start_page = page - 4;
				end_page = page + 5;
				if ( end_page > total_pages ) {
					end_page = total_pages;
					start_page = Math.max(1, (end_page - 9));
				}
			}

			var page_bar = ' \
				<li class="' + disable_prev + '"><a href="#" aria-label="First" class="page_button ' + disable_prev + '" data-type="first"><span aria-hidden="true">&laquo;</span></a></li> \
				<li class="' + disable_prev + '"><a href="#" aria-label="Previous" class="page_button ' + disable_prev + '" data-type="prev"><span aria-hidden="true">&lsaquo;</span></a></li>';
			for ( var i = start_page; i <= end_page; i++ ) {
				var is_active = ( i === page ) ? "active" : "";
				page_bar += '<li class="list_pages ' + is_active + '"><a href="#" class="page_button" data-type="page">' + i + '</a></li>';
			}
			page_bar += ' \
				<li class="' + disable_next + '"><a href="#" aria-label="Next" class="page_button ' + disable_next + '" data-type="next"><span aria-hidden="true">&rsaquo;</span></a></li> \
				<li class="' + disable_next + '"><a href="#" aria-label="Last" class="page_button ' + disable_next + '" data-type="last"><span aria-hidden="true">&raquo;</span></a></li>';
		}

		$("#" + list_id + " #total_records").text(total_records);
		$("#" + list_id + " #page_bar").html(page_bar);
		if ($("#is_show_checkbox").val() == '0') {
			$(".list_checkbox_column").hide();
		} else {
			$(".list_checkbox_column").show();
		}
	});
}

$(function() {
	$("#item_list input[type=checkbox]").prop("checked", false);

	$("body").on("click", ".noProp", function( event ) {
		event.stopPropagation();
	});

	$(".datepicker").datepicker({
		changeMonth	: true,
		changeYear	: true,
		dateFormat	: 'yy-mm-dd',
		onSelect	: function() {
			$(this).blur().change();
		}
	}).attr('readonly','readonly');
	
	// Show popup list
	$(".show_popup_list_button").on("click", function( event ) {
		event.preventDefault();
		var list_id = $(this).data("list_id");
		$("#" + list_id).modal();
	});

	// Popup list search done typing 
	var searchInterval = 2000;
	$(".popup_list_search_form").on("change", ".text_field", function() {
		var typingTimer;
		var list_id = $(this).closest("form").data("list_id");
		clearTimeout(typingTimer);
		typingTimer = setTimeout(function() { updateList(list_id); }, searchInterval);
	});
	
	// Search popup list
	$(".list_search_button, .popup_list_search_button").on("click", function( event ) {
		event.preventDefault();
		var list_id = $(this).closest("form").data("list_id");
		updateList(list_id);
	});

	// Reset popup list
	$(".list_reset_button, .popup_list_reset_button").on("click", function( event ) {
		event.preventDefault();
		var list_id = $(this).closest("form").data("list_id");
		$("#form_" + list_id)[0].reset();
		updateList(list_id);
	});

	// Click page bar button function
	$(".list_pagination_bar").on("click", ".page_button", function() {
		var list_id = $(this).closest("#page_bar").data("list_id");
		var current_page = parseInt($("#" + list_id + " .active .page_button").text());
		var last_page = parseInt($("#" + list_id + "_total_pages").val());
		var type = $(this).data("type");
		switch (type) {
			case "first":
				page = 1;
				break;
			case "prev":
				page = current_page - 1;
				break;
			case "page":
				page = parseInt($(this).text());
				break;
			case "next":
				page = current_page + 1;
				break;
			case "last":
				page = last_page;
				break;
			default:
				break;
		}
		updateList(list_id, page);
	});

	// Close popup form
	$(".close_popup_button").on("click", function() {
		$(".popup_list").modal("hide");
	});
	
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
	$("#list_items").on("click", ".list_item_row", function( event ) {
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
		window.location = ROOT + "/" + getValue("page_name");
	});

	$("#close_popup_button").on("click", function() {
		$(this).closest(".popup_list").modal("hide");
	});

	$("#reset_all_button").on("click", function() {
		var ans = window.confirm("Confirm to reset all form values?");
		if ( ans ) {
			resetPage();
		}
	});

	// Interface function
	$("#list_items").on("click", ".list_delete_single_item_button", function( event ) {
		event.preventDefault();
		var obj = $(this);
		removeSingleItem(obj);
	});

	$("#list_delete_multi_item_button").on("click", function() { removeMultiItems() });
	$("#temp_save_button").on("click", function() { tempSaveRecord() });
	$("#confirm_button").on("click", function() { confirmRecord() });
	$("#mark_finished_button").on("click", function() { markFinishRecord(); });
	$("#delete_button").on("click", function() { deleteRecord(); });
	$("#print_button").on("click", function() { printRecord(); });
});