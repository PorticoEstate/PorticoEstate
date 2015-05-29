
formatLinkTicket = function(key, oData)
{
	if(!oData[key])
	{
		return '';
	}
	
	var ticket = oData[key];
	return "<a href='"+ ticket.url +"' title='"+ ticket.statustext +"'>" + ticket.text + "</a>";
};