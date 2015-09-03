function onCreateBilling()
{
	var oArgs = {menuaction:'rental.uibilling.add', contract_type:document.getElementById('contract_type').value};
	var sUrl = phpGWLink('index.php', oArgs);
	window.location = sUrl;
}