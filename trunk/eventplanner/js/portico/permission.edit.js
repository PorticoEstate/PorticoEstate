
var lang;
var oArgs = {menuaction: 'eventplanner.uipermission.subject'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'subject_name', 'subject_id', 'subject_container', 'name');


$(document).ready(function ()
{

	var oArgs = {menuaction: 'eventplanner.uipermission.object', object_type: $("#object_type").val()};
	var strURL = phpGWLink('index.php', oArgs, true);
	JqueryPortico.autocompleteHelper(strURL, 'object_name', 'object_id', 'object_container', 'name');

	$("#object_type").change(function ()
	{
		$('#object_id').val('');
		$('#object_name').val('');
	});


	$.formUtils.addValidator({
		name: 'permission',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var v = false;
			var permission_1 = $("#permission_1").is(':checked');
			var permission_2 = $("#permission_2").is(':checked');
			var permission_4 = $("#permission_4").is(':checked');
			var permission_8 = $("#permission_8").is(':checked');

			if (permission_1 || permission_2 || permission_4 || permission_8)
			{
				v = true;
			}

			return v;
		},
		errorMessage: lang['permission is required'] || 'permission is required',
		errorMessageKey: ''
	});

});
