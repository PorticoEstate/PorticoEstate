
var myFormatterCheck = function (key, oData)
{
	var checked = '';
	var hidden = '';
	if (oData['responsible_item'])
	{
		checked = "checked = 'checked'";
		hidden = "<input type=\"hidden\" class=\"orig_check\"  name=\"values[assign_orig][]\" value=\"" + oData['responsible_contact_id'] + "_" + oData['responsible_item'] + "_" + oData['location_code'] + "\"/>";
	}

	return hidden + "<center><input type=\"checkbox\" " + checked + " class=\"mychecks\"  name=\"values[assign][]\" value=\"" + oData['location_code'] + "\"/></center>";
};

var FormatterCenter = function (key, oData)
{
	return "<center>" + oData[key] + "</center>";
}