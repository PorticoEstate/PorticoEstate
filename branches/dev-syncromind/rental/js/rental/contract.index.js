function onNew_contract()
{
	var oArgs = {menuaction:'rental.uicontract.add', location_id:document.getElementById('location_id').value};
	var sUrl = phpGWLink('index.php', oArgs);
	window.location = sUrl;
}