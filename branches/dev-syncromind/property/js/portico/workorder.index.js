
var oArgs_project = {menuaction: 'property.uiproject.edit'};
var sUrl_project = phpGWLink('index.php', oArgs_project);

var oArgs_order = {menuaction: 'property.uiworkorder.edit'};
var sUrl_order = phpGWLink('index.php', oArgs_order);

linktToProject = function(key, oData)
{
	var id = oData[key];
	return '<a href="' + sUrl_project + '&id=' + id + '">' + id + '</a>';
};

linktToOrder = function(key, oData)
{
	var id = oData[key];
	return '<a href="' + sUrl_order + '&id=' + id + '">' + id + '</a>';
};