
linktToMap = function (key, oData)
{
	var name = oData[key];
	var link = oData['link_map'];
	return '<a href="' + link + '" target="_blank">' + name + '</a>';
};

linktToGab = function (key, oData)
{
	var name = oData[key];
	var link = oData['link_gab'];
	return '<a href="' + link + '" target="_blank">' + name + '</a>';
};

linktToLocation = function (key, oData)
{
	var name = oData[key];
	var link = oData['link_location'];
	if(typeof(link) != 'undefined')
	{
		return '<a href="' + link + '" target="_blank">' + name + '</a>';
	}
	else
	{
		return name;
	}
};