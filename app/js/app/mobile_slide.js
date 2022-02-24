var slider_show = false;

$(function() {
	// Show / hide the slider menu when click on the slider menu button
	$(".slider_menu_button").on("click", function() {
		// Process if the slide menu is displaying, hide the slide menu
		if (slider_show) {
			hideSlider();
		} 
		
		// Process if the slide menu is hidden, display the slide menu
		else {
			slider_show = true;
			$("#slider_menu_outer_container").css("width", "260px");
			$("#slider_menu_container").show().animate({ "left" : "0px" }, "fast");
			hideSlider_setting();
		}
	});

	$(".content").click(function() {
		hideSlider();
	});
});

function hideSlider() {
	slider_show = false;
	$("#slider_menu_container").animate({ "left" : "-300px" }, "fast", function() {
		$(this).hide();
		$("#slider_menu_outer_container").css("width", "34px");
	});
}

var slider_show_setting = false;

$(function() {
	// Show / hide the slider menu when click on the slider menu button
	$(".slider_menu_button_setting").on("click", function() {
		// Process if the slide menu is displaying, hide the slide menu
		if (slider_show_setting) {
			hideSlider_setting();
		} 
		
		// Process if the slide menu is hidden, display the slide menu
		else {
			slider_show_setting = true;
			$("#slider_menu_outer_container_setting").css({"width":"260px","position":"absolute","float":"right"});
			$("#slider_menu_container_setting").show().animate({ "right" : "0px" }, "fast");
			hideSlider();
		}
	});

	$(".content").click(function() {
		hideSlider_setting();
	});
});

function hideSlider_setting() {
	slider_show_setting = false;
	$("#slider_menu_container_setting").animate({ "right" : "-300px" }, "fast", function() {
		$(this).hide();
		$("#slider_menu_outer_container_setting").css("width", "34px");
	});
}