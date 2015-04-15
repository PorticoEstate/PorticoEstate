$(document).ready(function(){

	$("#requirement-container tr").on("click", function(e){
		var requirement_id = $('td', this).eq(0).text();
		updateAllocationTable( requirement_id );
    });
});

function updateAllocationTable(requirement_id)
{
	if(!requirement_id)
	{
		return;
	}

	var oArgs = {
			menuaction:'logistic.uirequirement_resource_allocation.index',
			requirement_id: requirement_id,
			type: "requirement_id"
		};

		var requestUrl = phpGWLink('index.php', oArgs, true);

		JqueryPortico.updateinlineTableHelper('allocation-container', requestUrl);
}
