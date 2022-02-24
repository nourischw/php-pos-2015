$(function() {
	$(".datepicker").datepicker({
		changeMonth	: true,
		changeYear	: true,
		dateFormat	: 'yy-mm-dd',
		onSelect	: function() {
			$(this).blur().change();
		}
	}).attr('readonly','readonly');

	$("#print_report").on("click", function() {
		$("#form_report").submit();
	});
	$("#print_goodsin_report").on("click", function() {
		$("#form_goodsin_report").submit();
	});
	$("#print_inventory_report").on("click", function() {
		$("#form_inventory_report").submit();
	});
	$("#print_dailysales_report").on("click", function() {
		$("#form_dailysales_report").submit();
	});
});
