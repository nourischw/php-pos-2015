"use strict";

$(function() {
	function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
		$("#current_date").html(today.toDateString());
		$("#clock").html(h + ":" + m + ":" + s);
  //  document.getElementById('txt').innerHTML =
  //  h + ":" + m + ":" + s;
    var t = setTimeout(startTime, 500);
	}
	function checkTime(i) {
	    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
	    return i;
	}

	startTime();

});
