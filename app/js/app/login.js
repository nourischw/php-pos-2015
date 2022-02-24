"use strict";
$(function() {
    var $formLogin = $("#form_login");
    var $loginFailed = $("#login_failed");
    var $loginProcess = $("#login_process");
    $("#password").empty();

    $formLogin.on("submit", function(event) {
        event.preventDefault();

        $(".alert").hide();
        if (shop_code === "") {
            $loginFailed.stop(true, true).fadeIn("fast");
            return false;
        }

        // Process login
        $loginProcess.stop(true, true).fadeIn("fast");

        var jqxhr = $.ajax({
            url: "login_process",
            type: "POST",
            async: false,
            data: $("#form_login").serialize()
        });

        jqxhr.done(function(result) {
            if ( parseInt(result) === 1) {
                window.location = ROOT;
            } else {
                $loginProcess.hide();
                $loginFailed.stop(true, true).fadeIn("fast");
            }
        });
        
        return false;
    });
});