
function searchUser()
{
	if ($.trim($('#username').val()) === '')
	{
		alert('enter username');
		return false;
	}

	var oArgs = {menuaction: 'rental.uiresultunit.search_user'};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$('.loading').css({"visibility": "visible"});

	var data = {"username": document.getElementById('username').value};
	JqueryPortico.execute_ajax(requestUrl, function (result)
	{

		document.getElementById('custom_message').innerHTML = '';

		if (typeof (result.message) !== 'undefined')
		{
			document.getElementById('custom_message').innerHTML = result.message.msg;
		}

		if (typeof (result.error) !== 'undefined')
		{
			document.getElementById('custom_message').innerHTML = result.error.msg;
		}

		if (typeof (result.data) !== 'undefined')
		{
			document.getElementById('username').value = result.data.username;
			document.getElementById('firstname').value = result.data.firstname;
			document.getElementById('lastname').value = result.data.lastname;
			if (typeof (result.data.email) !== 'undefined')
			{
				document.getElementById('email').value = result.data.email;
			}
			document.getElementById('account_id').value = result.data.account_id;
		}
		$('.loading').css({"visibility": "hidden"});

	}, data, "POST", "JSON");
}

function addDelegate()
{
	if ($('#account_id').val() === '')
	{
		alert('search user');
		return false;
	}

	var oArgs = {menuaction: 'rental.uiresultunit.add'};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	var data = {};
	data['id'] = document.getElementById('unit_id').value;
	data['level'] = document.getElementById('unit_level').value;
	data['account_id'] = document.getElementById('account_id').value;

	$('.loading').css({"visibility": "visible"});

	JqueryPortico.execute_ajax(requestUrl, function (result)
	{

		document.getElementById('message0').innerHTML = '';

		if (typeof (result.message) !== 'undefined')
		{
			document.getElementById('message0').innerHTML = result.message.msg;
		}

		if (typeof (result.error) !== 'undefined')
		{
			document.getElementById('message0').innerHTML = result.error.msg;
		}
		document.getElementById('username').value = '';
		document.getElementById('firstname').value = '';
		document.getElementById('lastname').value = '';
		document.getElementById('email').value = '';
		document.getElementById('account_id').value = '';
		document.getElementById('custom_message').innerHTML = '';

		$('.loading').css({"visibility": "hidden"});

		oTable0.fnDraw();

	}, data, "POST", "JSON");
}

getRequestData = function (dataSelected, parameters)
{

	var data = {};

	$.each(parameters.parameter, function (i, val)
	{
		data[val.name] = {};
	});

	var n = 0;
	for (var n = 0; n < dataSelected.length; ++n)
	{
		$.each(parameters.parameter, function (i, val)
		{
			data[val.name][n] = dataSelected[n][val.source];
		});
	}

	return data;
};

function removeDelegate(oArgs, parameters)
{

	var api = $('#datatable-container_0').dataTable().api();
	var selected = api.rows({selected: true}).data();
	var nTable = 0;

	if (selected.length == 0)
	{
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function (result)
	{

		JqueryPortico.show_message(nTable, result);

		oTable0.fnDraw();

	}, data, 'POST', 'JSON');
}