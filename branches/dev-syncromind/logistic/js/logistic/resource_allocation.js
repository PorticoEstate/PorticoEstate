$(document).ready(function(){

	$("#requirement-container").on("click", function(e){
		var requirement_id = $('td', this).eq(0).text();
		updateAllocationTable( requirement_id );
    });
/*
	$("#allocation-container table .btn-sm.delete").on("click", function(e){
		var thisRow = $(this).parents("tr");
		
		var requestUrl = $(this).attr("href");
		alert(requestUrl);
		
		$.ajax({
			  type: 'POST',
			  url: requestUrl,
			  success: function(data) {
				  var obj = jQuery.parseJSON(data);
	    		  
	    		  if(obj.status == "deleted"){
	    				$(thisRow).remove();
						YAHOO.portico.updateinlineTableHelper('requirement-container');
				  }
			  },
			  error: function(XMLHttpRequest, textStatus, errorThrown) {
	      	    if (XMLHttpRequest.status === 401) {
	      	      location.href = '/';
	      	    }
			  }
		});
		
		return false;
    });
	*/
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
