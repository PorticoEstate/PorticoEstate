JqueryPortico.booking = {};

JqueryPortico.booking.postToUrl = function (path, params, method)
{
	method = method || "post";
	var form = document.createElement("form");
	form.setAttribute("method", method);
	form.setAttribute("action", path);
	for (var key in params)
	{
		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", params[key][0]);
		hiddenField.setAttribute("value", params[key][1]);
		form.appendChild(hiddenField);
	}
	document.body.appendChild(form);
	form.submit();
};


JqueryPortico.booking.inlineImages = function (container, url)
{
	container = document.getElementById(container);
	$.get(url, function (data)
	{
		var dlImage = document.createElement('dl');
		dlImage.setAttribute('class', 'proplist images');
		var results = data.ResultSet.Result;
		if (typeof results == 'object')
		{
			$.each(results, function (i, v)
			{
				var imgEl = dlImage.appendChild(document.createElement('dd')).appendChild(document.createElement('img'));
				var captionEl = dlImage.appendChild(document.createElement('dt'));
				imgEl.setAttribute('src', v.src.replace(/&amp;/gi, '&'));
                                imgEl.setAttribute('onClick', 'openModal(this)');
                                captionEl.appendChild(document.createTextNode(v.description));
				container.appendChild(dlImage);
			});
		}
	});
}


parseISO8601 = function (string)
{
	var regexp = "(([0-9]{4})(-([0-9]{1,2})(-([0-9]{1,2}))))?( )?(([0-9]{1,2}):([0-9]{1,2}))?";
	var d = string.match(new RegExp(regexp));
	var year = d[2] ? (d[2] * 1) : 0;
	date = new Date(year, (d[4] || 1) - 1, d[6] || 0);
	if (d[9])
		date.setHours(d[9]);
	if (d[10])
		date.setMinutes(d[10]);
	return date;
};