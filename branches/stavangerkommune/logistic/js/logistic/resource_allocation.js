$(document).ready(function(){

	$("#requirement-container table tr").live("click", function(e){
		var thisRow = $(this);
		
		var requirement_id = $(thisRow).find("td.requirement_id").find("div").text();
		
		updateAllocationTable( requirement_id );
    });
	
	$("#allocation-container table .btn-sm.delete").live("click", function(e){
		var thisRow = $(this).parents("tr");
		
		var requestUrl = $(this).attr("href");
		
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
});


function updateAllocationTable( requirement_id ){

	var oArgs = {
			menuaction:'logistic.uirequirement_resource_allocation.index',
			requirement_id: requirement_id,
			type: 'requirement_id',
			phpgw_return_as: 'json'
		};
		
		var requestUrl = phpGWLink('index.php', oArgs, true);
	
		var myColumnDefs = [ 
	        {key:"id", sortable:true}, 
	        {key:"requirement_id", sortable:true}, 
	        {key:"location_id", sortable:true}, 
	        {key:"resource_id", sortable:true} 
	    ]; 
	
		YAHOO.portico.inlineTableHelper('allocation-container', requestUrl, myColumnDefs);
}

