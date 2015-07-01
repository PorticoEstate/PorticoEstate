function onNew_adjustment()
{
	var oArgs = {menuaction:'rental.uiadjustment.add', responsibility_id:document.getElementById('responsibility_id').value};
	var sUrl = phpGWLink('index.php', oArgs);
	window.location = sUrl;
}