

$(document).ready(function ()
{

	$("#datatable-container").on("click", "tr", function ()
	{

		$('td', this).removeClass('priority1');
		$('td', this).removeClass('priority2');
		$('td', this).removeClass('priority3');

	});

});

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
