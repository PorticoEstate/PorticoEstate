var vendor_id = 0;
this.fetch_vendor_email = function ()
{
	if (document.getElementById('vendor_id').value)
	{
		base_java_url['vendor_id'] = document.getElementById('vendor_id').value;
	}

	if (document.getElementById('vendor_id').value != vendor_id)
	{
		base_java_url['action'] = 'get_vendor';
		base_java_url['field_name'] = 'mail_recipients';
		var oArgs = base_java_url;
		var strURL = phpGWLink('index.php', oArgs, true);
		JqueryPortico.updateinlineTableHelper(oTable1, strURL);
		vendor_id = document.getElementById('vendor_id').value;
	}
};

window.on_vendor_updated = function ()
{
	fetch_vendor_email();
};


this.preview = function (id)
{
	var oArgs = {menuaction: 'property.uiexternal_communication.view', id: id, preview_html: true};
	var strURL = phpGWLink('index.php', oArgs);
	Window1 = window.open(strURL, 'Search', "left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
};

$(document).ready(function ()
{
	var do_preview = $("#do_preview").val();

	if (do_preview)
	{
		preview(do_preview);
	}
});