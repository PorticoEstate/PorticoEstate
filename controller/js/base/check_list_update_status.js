$(document).ready(function ()
{

	// UPDATE CHECKLIST STATUS
	$("#update-check-list-status").on("submit", function (e)
	{
		e.preventDefault();

		var thisForm = $(this);

		var statusClass = $(thisForm).attr("class");

		var requestUrl = $(thisForm).attr("action");

		var submitBnt = $(thisForm).find("input[type='submit']");

		$.ajax({
			type: 'POST',
			url: requestUrl + "&" + $(thisForm).serialize(),
			success: function (data)
			{
				if (data)
				{
					var jsonObj = JSON.parse(data);

					if (jsonObj.status == 'not_saved')
					{
						$(submitBnt).val("feil ved lagring");
						if (jsonObj.message)
						{
							alert(jsonObj.message);

							var check_list_id = $(thisForm).find("input[name='check_list_id']").val();
							add_billable_hours(check_list_id);
						}

					}
					else if (jsonObj.status == '1')
					{
						$(submitBnt).val("Utført");
						$("#update-check-list-status-value").val(0);
						//          $("#update-check-list-status-icon.not_done").hide();
						//        	$("#update-check-list-status-icon-done.done").show();
					}
					else
					{
						$(submitBnt).val("Sett status: Utført");
						$("#update-check-list-status-value").val(1);
						//         $("#update-check-list-status-icon.not_done").show();
						//         $("#update-check-list-status-icon-done.done").hide();
					}
				}
			}
		});
	});
});

var alertify;

/**
 * Open a prompt for input
 * @param {type} id
 * @param {type} location_id
 * @param {type} attribute_id
 * @param {type} input_text
 * @param {type} lang_new_value
 * @returns {undefined}
 */

function add_billable_hours(check_list_id)
{

	var oArgs = {
		menuaction: 'controller..uicheck_list.add_billable_hours',
		check_list_id: check_list_id
	};
	var requestUrl = phpGWLink('index.php', oArgs, true);
alert(requestUrl);
	//	var new_value = prompt(input_text, "");

	/*
	 * @title {String or DOMElement} The dialog title.
	 * @message {String or DOMElement} The dialog contents.
	 * @value {String} The default input value.
	 * @onok {Function} Invoked when the user clicks OK button.
	 * @oncancel {Function} Invoked when the user clicks Cancel button or closes the dialog.
	 *
	 * alertify.prompt(title, message, value, onok, oncancel);
	 *
	 */
	alertify.prompt( 'input_text', 'lang_new_value', ''
               , function(evt, value)
			   {
					var new_value = value;
					if (new_value !== null && new_value !== "")
					{
						var xmlhttp = new XMLHttpRequest();
						xmlhttp.onreadystatechange = function ()
						{
							if (this.readyState == 4 && this.status == 200)
							{
								var data = JSON.parse(this.responseText);

								if (data.status == 'ok' && data.choice_id)
								{
									alertify.success('You entered: ' + value);
									var select = document.getElementById(id);
									var option = document.createElement("option");
									option.text = new_value;
									option.id = data.choice_id;
									select.add(option, select[1]);
									select.selectedIndex = "1";
								}
								else
								{
									alertify.error('Error');
								}
							}
						};
						xmlhttp.open("POST", requestUrl, true);
						xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						var params = 'new_value=' + new_value;
						xmlhttp.send(params);
					}
					else
					{
						alertify.error('Cancel');
					}
				}
               , function() { alertify.error('Cancel') });

}