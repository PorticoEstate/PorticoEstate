
var oArgs = {menuaction: 'property.uigeneric.index', type: 'dimb', type_id:0};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'ecodimb_name', 'ecodimb_id', 'ecodimb_container', 'descr');

$(document).ready(function ()
{
	$.formUtils.addValidator({
		name: 'naming',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var v = false;
			var firstname = $('#firstname').val();
			var lastname = $('#lastname').val();
			var company_name = $('#company_name').val();
			var department = $('#department').val();
			if ((firstname != "" && lastname != "") || (company_name != "" && department != ""))
			{
				v = true;
			}
			return v;
		},
		errorMessage: lang['Name or company is required'],
		errorMessageKey: ''
	});
});

function set_tab(tab)
{
	$("#active_tab").val(tab);
}

function reserveComposite ()
{
	var text = "Composite " + schedule.rental['data']['id'] + ", application_id " + $('#application_id').val();
	$('#tempMessage').append("<li>" + text + "</li>");
}