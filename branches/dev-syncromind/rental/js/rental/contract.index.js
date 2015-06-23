function onNew_contract()
{
	var link = 'index.php?menuaction=rental.uicontract.add&location_id=' + document.getElementById('location_id').value;
	window.location = link;
}