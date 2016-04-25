var cat_id = 0;
var fileuploader_action;

this.update_fileuploader_action = function ()
{
	cat_id = document.getElementById("cat_id").value;
	fileuploader_action = {
		menuaction: 'property.fileuploader.add',
		upload_target: 'manual.bodocuments.addfiles',
		id: cat_id
	};
	//	console.log(fileuploader_action);
	refresh_files();
}

this.fileuploader = function ()
{
	if (!fileuploader_action)
	{
		cat_id = document.getElementById("cat_id").value;
		fileuploader_action = {
			menuaction: 'property.fileuploader.add',
			upload_target: 'manual.bodocuments.addfiles',
			id: cat_id
		};
	}

	if (!cat_id)
	{
		alert('velg kategori først');
		return;
	}
	var requestUrl = phpGWLink('index.php', fileuploader_action);
	TINY.box.show({iframe: requestUrl, boxid: 'frameless', width: 750, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true, close: true, closejs: function ()
		{
			refresh_files()
		}});
}


function refresh_files()
{
	var oArgs = {menuaction: 'manual.uidocuments.get_files', cat_id: cat_id};
	var strURL = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper('datatable-container_0', strURL);
}

function lightbox_hide()
{
	TINY.box.hide();
	return true;
}


