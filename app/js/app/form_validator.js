"use strict";

var NUM_TYPE_INTEGER = 1;
var NUM_TYPE_FLOAT = 2;

var rules;
var stored_email = '';
var email_not_registered = true;

(function( $ ) {
    $.getValue = function( id ) {
		return $.trim($("#" + id).val());
    },

    // Show error message
    $.showErrorMessage = function( target, error_message ) {
        $("#error_" + target).text(error_message);
        return false;
    },

    // Clear error message
    $.clearErrorMessage = function( target ) {
        $("#error_" + target).text("");
        return true;
    },

    $.setRules = function( items ) {
        rules = items;
    },
	
	$.validateItem = function( field, value ) {
		var rule = rules[field];
		var error_target = rules.error_target || field;
		var error_message = '';

		$.clearErrorMessage(error_target);

		if ( rule.required && !value ) {
			return $.showErrorMessage(error_target, sessionStorage.REQUIRED);
		}
		
		switch ( rule.format ) {
			case "radiobox":
				error_message = rule.message || sessionStorage.RADIO_NOT_CHECKED;
				if ( rule.required !== false && !value ) {
					return $.showErrorMessage(error_target, error_message);
				}
				break;

			case "checkbox":
				error_message = rule.message || sessionStorage.CHECKBOX_NOT_CHECKED;
				if ( rule.required !== false && !value ) {
					return $.showErrorMessage(error_target, error_message);
				}
				break;

			case "number":
				if ( isNaN(value) ) {
					return $.showErrorMessage(error_target, sessionStorage.ERROR_WRONG_NUMERIC_FORMAT);
				}			
				else if ( rule.not_allowed_zero ) {
					var value = parseFloat(value);
					if ( value <= 0.00 ) {
						return $.showErrorMessage(error_target, sessionStorage.ZERO_VALUE_NOT_ALLOWED);
					}
				}

				break;

			case "email":
				var email_ok = true;
				var confirm_field = 'confirm_' + field;
				
				if ( value !== '' ) {
					if ( rule.need_confirm ) {
						$.clearErrorMessage(confirm_field);
					}
					
					error_message = rule.message || sessionStorage.WRONG_EMAIL_FORMAT;

					// Check whether the email format is valid
					if ( !value.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+(\.[a-zA-Z]{2,})+$/) ) {
						email_ok = false;
						return $.showErrorMessage(error_target, error_message);
					}

					if ( rule.need_check_exists && value !== stored_email ) {
						stored_email = value;

						// Check whether the email is already registered
						var jqxhr = $.ajax({
							type: "POST",
							url: "check_member_exists",
							async: false,
							data: {email: value}
						});
						
						jqxhr.done(function( member_exists ) {
							email_not_registered = ( parseInt(member_exists) === 1 ) ? false : true;
						});
					
						if ( !email_not_registered ) {
							$.clearErrorMessage(confirm_field);
							return $.showErrorMessage(field, sessionStorage.EMAIL_ALREADY_REGISTERED);
						}
					}

					email_ok &= email_not_registered;
					if ( email_ok && rule.need_confirm && value !== getValue(confirm_field) ) {
						return $.showErrorMessage(confirm_field, sessionStorage.EMAIL_NOT_CONFIRMED);
					}
				}

				break;

			case "password":
				var confirm_field = 'confirm_' + field;
				if ( value.length < rule.lengths ) {
					$.clearErrorMessage(confirm_field);
					var error_message = 
						sessionStorage.WRONG_VALUE_LENGTH + rule.lengths + 
						sessionStorage.WRONG_VALUE_LENGTH_STRING;
					return $.showErrorMessage(error_target, error_message);
				} else {
					$.clearErrorMessage(error_target);
					if ( value !== getValue(confirm_field) ) {
						return $.showErrorMessage(confirm_field, sessionStorage.PASSWORD_NOT_CONFIRMED);
					}
					return $.clearErrorMessage(confirm_field);
				}

			default:
				break;
		}

		return $.clearErrorMessage(error_target);
	},
	
	$.checkFields = function( form ) {
		var ok = true;
		$("#" + form).find(".validateItem").each(function() {
			ok &= $.validateItem(this.id, getValue(this.id));
		});

		$("#" + form).find(".radiobox, .checkbox").each(function() {
			var value = $('input[name="' + this.name + '"]').is(":checked");
			ok &= $.validateItem(this.name, value);
		});

		// Check whether the form contains email field
		if ( $("#" + form + " .email").length > 0 ) {
			stored_email = '';
	    	var check_field = $(".email").data("check_field");
	    	ok &= $.validateItem(check_field, getValue(check_field));
		}

		// Check whether the form contains password field
		if ( $("#" + form + " .password").length > 0 ) {
	    	var check_field = $(".password").data("check_field");
	    	ok &= $.validateItem(check_field, getValue(check_field));
		}

		return ok;
	}
	
})(jQuery);

$(function() {
	$(".num_item").on({
		keydown: function( e ) {
			var key = e.charCode || e.keyCode || 0;
			var allowed_key = [
				8, 9, 37, 38, 39, 40, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 96, 97, 98, 99, 100, 
				101, 102, 103, 104, 105, 109, 110, 190
			];
			if ($.inArray(key, allowed_key) > -1) {
				return true;
			}

			return false;
		},
		
		blur: function() {
			var num_type = $(this).data("num_type");
			var value = $(this).val();
			var num_flags = ( $(this).data("num_flags") !== "undefined" ) ? $(this).data("num_flags") : '';
			/* 
				num_flags
				i = integer
				.x = float number, x = precision
				+ = positive
			*/

			if ( value && num_flags ) {
				// Convert value to integer
				if ( num_flags.indexOf('i') > -1 ) {
					value = float2int(value);
				}

				// Convert value to float number
				if ( num_flags.indexOf('.') > -1 ) {
					var dot_pos = num_flags.indexOf('.');
					var precision = parseInt(num_flags.substr(dot_pos, 1));
					value = parseFloat(value).toFixed(precision);
				}

				// Convert value to positive number
				if ( num_flags.indexOf('+') > -1 ) {
					value = Math.abs(value);
				}
			}
			$(this).val(value);
		}
	});

    $(".validateItem").on("blur", function() {
        $.validateItem(this.id, $.getValue(this.id));
    });
	
    $(".radiobox, .checkbox").on("change", function() {
    	var value = $('input[name="' + this.name + '"]').is(":checked");
        $.validateItem(this.name, value);
    });
    $(".password").on("blur", function() {
    	var check_field = $(this).data("check_field");
    	$.validateItem(check_field, $.getValue(check_field));
    });
});