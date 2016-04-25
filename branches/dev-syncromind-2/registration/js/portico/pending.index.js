var main_source;
var oArgs_edit = {menuaction: 'registration.uipending.edit'};
var edit_Url = phpGWLink('index.php', oArgs_edit);

formatLinkPending = function (key, oData)
{
	var id = oData[key];
	return '<a href="' + edit_Url + '&id=' + id + '">' + lang['edit'] + '</a>';
};


var formatterCheckPending = function (key, oData)
{
	var checked = '';
	var hidden = '';
	if (oData['reg_approved'])
	{
		checked = "checked = 'checked'";
		hidden = "<input type=\"hidden\" class=\"orig_check\"  name=\"values[pending_users_orig][]\" value=\"" + oData['reg_id'] + "\"/>";
	}
	return hidden + "<center><input type=\"checkbox\" class=\"mychecks\"" + checked + "value=\"" + oData['reg_id'] + "\" name=\"values[pending_users][]\"/></center>";
};

