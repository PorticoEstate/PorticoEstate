

function generatefiles()
{
	var oArgs = {menuaction:'booking.uicompleted_reservation_export.index'};
	var requestUrl = phpGWLink('index.php', oArgs);
	$('#list_actions_form').prop("action", requestUrl);
	$('#list_actions_form').append('<input type="hidden" name="filter_to" value="' + $('#filter_to').val() + '"/>');
	$('#list_actions_form').append('<input type="hidden" name="generate_files" value="1"/>');
	$('#list_actions_form').submit();
}