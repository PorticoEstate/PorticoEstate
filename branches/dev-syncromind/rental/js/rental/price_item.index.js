function onNew_price_item()
{
	//var newName = document.getElementById('innertoolbar').value;
	var resp_id = document.getElementById('responsibility_id').value;
	
	var oArgs = {menuaction:'rental.uiprice_item.edit', responsibility_id:resp_id};
	var sUrl = phpGWLink('index.php', oArgs);
	window.location = sUrl;
}