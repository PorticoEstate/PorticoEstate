$(document).ready(function ()
{

	$("#user_id").change(function ()
	{
		update_substitute_table();
	});

	$("#substitute_user_id").change(function ()
	{
		update_substitute_table();
	});

	$("#acl_form").on("submit", function (e)
	{
		e.preventDefault();
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");
		$.ajax({
			type: 'POST',
			url: requestUrl + '&phpgw_return_as=json&' + $(thisForm).serialize(),
			data: {
				save:true,
				substitute_user_id: $("#substitute_user_id").val(),
				user_id: $("#user_id").val(),
				start_time: $("#start_time").val()
			},
			success: function (data)
			{
				if (data)
				{
					if (data.sessionExpired)
					{
						alert('Sesjonen er utløpt - du må logge inn på nytt');
						return;
					}

					var obj = data;

					var submitBnt = $(thisForm).find("input[type='submit']");
					if (obj.status == "updated")
					{
						$(submitBnt).val("Lagret");
						var oArgs = {menuaction: 'property.uisubstitute.query', substitute_user_id: $("#substitute_user_id").val(), user_id: $("#user_id").val()};
						var requestUrl = phpGWLink('index.php', oArgs, true);
						JqueryPortico.updateinlineTableHelper(oTable0, requestUrl);
					}
					else
					{
						$(submitBnt).val("Feil ved lagring");
					}

					// Changes text on save button back to original
					window.setTimeout(function ()
					{
						$(submitBnt).val('Lagre');
						$(submitBnt).addClass("not_active");
					}, 1000);

					var htmlString = "";
					if (typeof (data['receipt']['error']) != 'undefined')
					{
						for (var i = 0; i < data['receipt']['error'].length; ++i)
						{
							htmlString += "<div class=\"text-center alert alert-danger\" role=\"alert\">";
							htmlString += data['receipt']['error'][i]['msg'];
							htmlString += '</div>';
						}

					}
					if (typeof (data['receipt']['message']) != 'undefined')
					{
						for (var i = 0; i < data['receipt']['message'].length; ++i)
						{
							htmlString += "<div class=\"text-center alert alert-success\" role=\"alert\">";
							htmlString += data['receipt']['message'][i]['msg'];
							htmlString += '</div>';
						}

					}
					$("#receipt").html(htmlString);
				}
			}
		});
	});
});


function update_substitute_table()
{
	var oArgs = {menuaction: 'property.uisubstitute.query',  user_id: $("#user_id").val(), substitute_user_id: $("#substitute_user_id").val()};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	JqueryPortico.updateinlineTableHelper(oTable0, requestUrl);
//	execute_async(myDataTable_0,  oArgs);
	$("#receipt").html('');
}

var addFooterDatatable = function (oTable)
{
	var api = oTable.api();
	var newTD = JqueryPortico.CreateRowChecked("delete");
	$(api.column(3).footer()).html(newTD);
};
