"use strict";

// $(function() {
	
	
// });

function printRecord() {
	var record_id = getValue("record_id");
	var url = ROOT + "goods_in/print/" + record_id;
	var win = window.open(url, '_new');
	win.focus();
}