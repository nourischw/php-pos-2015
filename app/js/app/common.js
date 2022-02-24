"use strict";

// Define the root path of the JavaScript file
var PATH_JSON = ROOT + "app/js/app/json/";

// Define edit type
var EDIT_TYPE_NEW = 1;
var EDIT_TYPE_UPDATE = 2;

$(function() {
	if ( !sessionStorage.length ) {
		var jqxhr = $.ajax({
			url: PATH_JSON + "error_message.json",
			dataType: "json"
		});

		jqxhr.done(function( data ) {
			$.each(data, function( key, value ) {
				sessionStorage.setItem(key, value);
			});
		});
	}

	$("#home_button").on("click", function() {
		window.location = ROOT;
	});

	$(".redirect_button").on("click", function( event ) {
		event.preventDefault();
		window.location = ROOT + $(this).data("redirect_page");
	});

	$(".close_result_box").on("click", function() {
		$(".result_alert_box").fadeOut("fast");
	});
});

function getValue( id ) {
	return document.getElementById(id).value.trim();
}

function getNameValue( name ) {
	return document.getElementsByName(name).value.trim();
}

function getData( id, data_name ) {
	return $("#" + id).data(data_name);
}

function enable( item ) {
	$(item).prop("disabled", false);
}

function disable( item ) {
	$(item).prop("disabled", true);
}

function isSelected( item ) {
	$(item).prop("selected", true);
}

function float2int( value ) {
    return value | 0;
}

function setFloat( value, precision ) {
	return parseFloat(value).toFixed(precision);
}

function encryptPassword(pwd_field, require_onetime_token) {
	var original_password = getValue(pwd_field);
	var sha_password = new jsSHA(original_password, "ASCII");
	var password_hash = sha_password.getHash("SHA-512", "HEX");
	if ( require_onetime_token ) {
		var onetime_token = generateOnetimeToken();
		password_hash += onetime_token;
		sha_password = new jsSHA(password_hash, "ASCII");
		password_hash = sha_password.getHash("SHA-512", "HEX");
	}
	$("#" + pwd_field + "_hash").val(password_hash);
}

function generateOnetimeToken() {
	var onetime_token = null;
	var jqxhr = $.ajax({
		url: ROOT + "generate_onetime_token",
        async: false
	});

	jqxhr.done(function( token ) {
		onetime_token = token;
	});

	return onetime_token;
}