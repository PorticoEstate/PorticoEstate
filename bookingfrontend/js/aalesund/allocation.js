$(document).ready(function () {
	addDateRangePicker();
});

function addDateRangePicker() {
	$('input[name="from_"]').daterangepicker({
		singleDatePicker: true,
		timePicker: true,
		timePicker24Hour: true,
		timePickerIncrement: 15,
		locale: {
			format: 'DD/MM/YYYY HH:mm',
			cancelLabel: 'Clear',
			firstDay: 1
		}
	});

	$('input[name="to_"]').daterangepicker({
		singleDatePicker: true,
		timePicker: true,
		timePicker24Hour: true,
		timePickerIncrement: 15,
		locale: {
			format: 'DD/MM/YYYY HH:mm',
			cancelLabel: 'Clear',
			firstDay: 1
		}
	});

	$('input[name="repeat_until"]').daterangepicker({
		singleDatePicker: true,
		autoUpdateInput: false,
		autoApply: true,
		locale: {
			cancelLabel: 'Clear',
			firstDay: 1
		}
	});

	$('#from_date').on('apply.daterangepicker', function(ev, picker) {
		const date = picker.startDate.format('DD/MM/YYYY HH:mm');

		$('#from_date').val(date);
	});

	$('#to_date').on('apply.daterangepicker', function(ev, picker) {
		const date = picker.startDate.format('DD/MM/YYYY HH:mm');

		$('#to_date').val(date);
	});

	$('#repeat_date').on('apply.daterangepicker', function(ev, picker) {
		const date = picker.startDate.format('DD/MM/YYYY');

		$('#repeat_date').val(date);
	});
}
