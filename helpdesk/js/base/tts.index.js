
JqueryPortico.formatLinkRelated = function (key, oData)
{

	if (!oData['child_date'])
	{
		return '';
	}

	var child_date = oData['child_date'][key];
	var date_info = child_date.date_info;
	if (!date_info.length)
	{
		return '';
	}

	var name = date_info[0]['entry_date'];
	var link = date_info[0]['link'];
	var title = child_date['statustext']['statustext'] || '';


	return '<a href="' + link + '" title="' + title + '">' + name + '</a>';
};

JqueryPortico.formatTtsIdLink = function (key, oData)
{

	var name = oData[key] + oData['new_ticket'];
	var link = oData['link'];
	return '<a href="' + link + '">' + name + '</a>';
};

JqueryPortico.searchLinkTts = function (key, oData)
{

	var name = oData[key];
	return '<a id="' + name + '" onclick="searchData(this.id);">' + name + '</a>';
};

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

function close_ticket_with_comment(parameters)
{
	var selected = new Array();
	var aTrs = oTable.fnGetNodes();
	for (var i = 0; i < aTrs.length; i++)
	{
		if ($(aTrs[i]).hasClass('context-menu-active'))
		{
			selected.push(i);
			return selected;
		}
		if ($(aTrs[i]).hasClass('selected'))
		{
			selected.push(i);
		}
	}

	let data = [];
	var oArgs = {
		menuaction: 'helpdesk.uitts.close_ticket_with_comment'
	};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	for (let n = 0; n < selected.length; n++)
	{
//		console.log(selected[n]);
		var aData = oTable.fnGetData(selected[n]); //complete dataset from json returned from server
//		console.log(parameters);

		len = parameters.parameter.length;

		for (let j = 0; j < len; j++)
		{
//			let obj = {};
//			let parameter_name = parameters.parameter[j]['name'];
//			obj[parameter_name] = aData[parameters.parameter[j]['source']];
//			data.push(obj);
			requestUrl +='&id[]=' + aData[parameters.parameter[j]['source']];
		}

	}
//		console.log(data);
//	console.log(selected);


	let input_text = 'Avslutt saken med notat';
	let lang_new_value = 'Notat';

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

	alertify.prompt(input_text, lang_new_value, ''
		, function (evt, value)
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

						if (data.status == 'ok')
						{
							alertify.success('You entered: ' + value);
							oTable.fnDraw();
						}
						else
						{
							alertify.error('Error');
						}
					}
				};
				xmlhttp.open("POST", requestUrl, true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				var params = 'note=' + new_value;
				xmlhttp.send(params);
			}
			else
			{
				alertify.error('Cancel');
			}
		}
	, function ()
	{
		alertify.error('Cancel')
	});

}