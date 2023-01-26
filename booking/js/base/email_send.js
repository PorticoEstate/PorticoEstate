var building_id_selection = "";

$(document).ready(function ()
{
	JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'booking.uibuilding.index'}, true),
		'field_building_name', 'field_building_id', 'building_container');
});


this.get_email_recipients = function (seasons)
{
	var building_id = $('#field_building_id').val();
	var oArgs = {menuaction: 'booking.uisend_email.get_email_addresses', building_id: building_id};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		data: {seasons: seasons},
		success: function (data)
		{
			$("#email_recipients").empty();

			if (data != null)
			{
				if (data.sessionExpired)
				{
					alert('Sesjonen er utløpt - du må logge inn på nytt');
					return;
				}

				var obj = data;

				$.each(obj, function (i)
				{
					$('#email_recipients').append($('<option/>', {
						value: obj[i].email,
						text: obj[i].name + " <" + obj[i].email + ">"
					}));

				});

			}
		}
	}).done(function ()
	{
		$('#email_recipients').multiselect('rebuild');

		setTimeout(function ()
		{
			$('#email_recipients').parent().find("button.multiselect").click();

		}, 100);
	});
};

$(window).on('load', function ()
{
	$('#field_building_name').on('autocompleteselect', function (event, ui)
	{
		var building_id = ui.item.value;
		var selection = [];
		if (building_id != building_id_selection)
		{
			populateTableChkSeasons(building_id, selection);
			building_id_selection = building_id;
		}
	});

	$('#season_container').on('change', '.chkSeasons', function ()
	{
		var seasons = new Array();
		$('#season_container input[name="seasons[]"]:checked').each(function ()
		{
			seasons.push($(this).val());
		});

		get_email_recipients(seasons);

	});

	$("#email_recipients").multiselect({
		buttonClass: 'form-select',
		templates: {
			button: '<button type="button" class="multiselect dropdown-toggle" data-bs-toggle="dropdown"><span class="multiselect-selected-text"></span></button>'
		},
		includeSelectAllOption: true,
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		buttonClass: 'form-control',
		onChange: function (option)
		{
			// Check if the filter was used.
			var query = $("#email_recipients").find('li.multiselect-filter input').val();

			if (query)
			{
				$("#email_recipients").find('li.multiselect-filter input').val('').trigger('keydown');
			}
		},
		onDropdownHidden: function (event)
		{
			console.log(event);

		}
	});

	$(".btn-group").addClass('w-75');
	$(".multiselect-container").addClass('w-100');

	$('#email_recipients').on('select2:open', function (e)
	{
		$(".select2-search__field").each(function ()
		{
			if ($(this).attr("aria-controls") == 'select2-email_recipients-results')
			{
				$(this)[0].focus();
			}
		});
	});
});

if ($.formUtils)
{
	$.formUtils.addValidator({
		name: 'application_season',
		validatorFunction: function (value, $el, config, language, $form)
		{
			var n = 0;
			$('#season_container table input[name="seasons[]"]').each(function ()
			{
				if ($(this).is(':checked'))
				{
					n++;
				}
			});
			var v = (n > 0) ? true : false;
			return v;
		},
		errorMessage: 'Please choose at least 1 season',
		errorMessageKey: 'application_season'
	});

	$.formUtils.addValidator({
		name: 'email_recipients',
		validatorFunction: function (value, $el, config, language, $form)
		{
			var v = false;
			var email_recipients = $('#email_recipients option:selected');
			if (email_recipients.length > 0)
			{
				v = true;
				$(".multiselect").removeClass(['btn','is-invalid']);
				$(".multiselect").addClass(['btn','is-valid']);
			}
			else
			{
				$(".multiselect").removeClass(['is-valid']);
				$(".multiselect").addClass(['btn','is-invalid']);
			}
			return v;
		},
		errorMessage: 'select at least one recipient',
		errorMessageKey: 'select_at_least_one_recipient'
	});


	$.formUtils.addValidator({
		name: 'mailbody',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var element = document.getElementById('editor_error');
			if (element)
			{
				element.parentNode.removeChild(element);
			}
			var v = true;
			if (html_editor === 'summernote')
			{
				if ($('#field_mailbody').summernote('isEmpty'))
				{
					$('<span id="editor_error" class="help-block form-error">').text('Angi detaljer').insertAfter($('.note-editor'));
					v = false;
				}
			}
			else if (html_editor === 'ckeditor')
			{
				var data = CKEDITOR.instances['field_mailbody'].getData();

				document.getElementById("field_mailbody").value = data;

				if (!data)
				{
					$('<span id="editor_error" class="help-block form-error">').text('Angi detaljer').insertAfter($('#field_mailbody'));
					v = false;
				}
			}
			else
			{
				var data = document.getElementById("field_mailbody").value;

				if (!data)
				{
					$('<span id="editor_error" class="help-block form-error">').text('Angi detaljer').insertAfter($('#field_mailbody'));
					v = false;
				}

			}
			return v;
		},
		errorMessage: 'details are required',
		errorMessageKey: ''
	});


}

function populateTableChkSeasons(building_id, selection)
{
	var url = phpGWLink('index.php', {menuaction: 'booking.uiseason.index', sort: 'name', filter_building_id: building_id, length: -1}, true);

	var container = 'season_container';
	var colDefsSeasons = [{label: '', object: [{type: 'input', attrs: [
						{name: 'type', value: 'checkbox'}, {name: 'name', value: 'seasons[]'}, {name: 'class', value: 'chkSeasons'}
					]}
			], value: 'id', checked: selection}, {key: 'name', label: lang['Name']}
	];
	populateTableChk(url, container, colDefsSeasons);
}

function populateTableChk(url, container, colDefs)
{
	createTable(container, url, colDefs, '', 'pure-table pure-table-bordered');
}

