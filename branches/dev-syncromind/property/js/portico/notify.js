
var notify_contact = 0;

	formatLink_notify = function(key, oData)
	{
	  	var oArgs = {menuaction:'addressbook.uiaddressbook.view_person',ab_id:oData[key]};
		var strURL = phpGWLink('index.php', oArgs);
	  	return "<a href="+strURL+" title='"+oData[key]+"'>"+notify_lang_view+"</a>";
	};

/*	var FormatterRight_notify = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<div align=\"right\">"+oData+"</div>";
	}	*/
	
	this.notify_contact_lookup = function()
	{
		if(!base_java_notify_url['location_item_id'])
		{
			alert(notify_lang_alert); // notify_lang_alert is defined in php-class property_notify::get_yui_table_def()
			return;
		}	
		var oArgs = {menuaction:'property.uilookup.addressbook',column:'notify_contact'};
		var strURL = phpGWLink('index.php', oArgs);
		Window1=window.open(strURL,"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
	}		

/* This one is added dynamically from php-class property_notify::get_jquery_table_def()
	this.refresh_notify_contact=function()
	{
	}
*/

$(document).ready(function(){

	$("#notify_contact").bind('DOMAttrModified propertychange', function(evt)
	{
		refresh_notify_contact($(this).val());
	});
});
