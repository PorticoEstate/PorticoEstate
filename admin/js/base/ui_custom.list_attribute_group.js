function onNew_group()
{
	var oArgs = {
		menuaction: 'admin.ui_custom.edit_attrib_group',
		appname: document.getElementById('appname').value,
		location: document.getElementById('location').value
	};
	var sUrl = phpGWLink('index.php', oArgs);
	window.location = sUrl;
}

