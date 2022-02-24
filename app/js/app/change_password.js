var ChangePassword = {
	rules: {
		"old_password": {
			"required": true
		},
		"new_password": {
			"lengths": 8,
			"format": "password"
		}
	},

	init: function() {
		document.getElementById("old_password").value = "";
		this.setRules();
		this.bindObject();
	},

	bindObject: function() {
		document.getElementById("submit_button").addEventListener("click", this.checkForm);
	},

	setRules: function() {
		$.setRules(this.rules);
	},

	checkForm: function(event) {
		event.preventDefault();
		var ok = $.checkFields("form_change_password");
		if (!ok) {
			return false;
		}
		
		document.getElementById("form_change_password").submit();
	}
}

document.addEventListener("DOMContentLoaded", function() { 
    ChangePassword.init();
});