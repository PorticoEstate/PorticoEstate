
var notify_contact = 0;

formatLink_notify = function (key, oData)
{
	var oArgs = {menuaction: 'addressbook.uiaddressbook_persons.view', ab_id: oData[key]};
	var strURL = phpGWLink('index.php', oArgs);
	return "<a href=" + strURL + " title='" + oData[key] + "'>" + notify_lang_view + "</a>";
};

/*	var FormatterRight_notify = function(elCell, oRecord, oColumn, oData)
 {
 elCell.innerHTML = "<div align=\"right\">"+oData+"</div>";
 }	*/

this.notify_contact_lookup = function ()
{
	if (!location_item_id)
	{
		alert(notify_lang_alert);
		return;
	}
	var width = Math.round(window.innerWidth * 0.95);
	var oArgs = {menuaction: 'property.uilookup.addressbook', column: 'notify_contact', clear_state:1};
	var strURL = phpGWLink('index.php', oArgs);
	TINY.box.show({iframe: strURL, boxid: "frameless", width: width, height: 450, fixed: false, maskid: "darkmask", maskopacity: 40, mask: true, animate: true, close: true});
};

/* This one is added dynamically from php-class property_notify::get_jquery_table_def()
 this.refresh_notify_contact=function()
 {
 }
 */


window.on_contact_updated = function (contact_id)
{
	refresh_notify_contact(contact_id);
};
