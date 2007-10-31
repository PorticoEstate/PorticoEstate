YAHOO.util.Event.addListener(window, "load", function() { 
	YAHOO.example.EnhanceFromMarkup = new function() { 
		/*
		var myColumnDefs = [
		    {key:"location_code",label:"Property", formatter:YAHOO.widget.DataTable.formatNumber,sortable:true},
		    {key:"loc1",label:"loc1", formatter:YAHOO.widget.DataTable.formatNumber, sortable:true},
		    {key:"loc1_name",label:"Location name",sortable:true}
		];
		*/
		this.myDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get("grid"));
		this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
		
		
		this.myDataSource.responseSchema = {
		    fields: [{ key:"location_code" },
		            { key:"loc1" },
		            {key:"loc1_name" }
		    ]
		};
		
		this.myDataTable = new YAHOO.widget.DataTable("markup", myColumnDefs, this.myDataSource,
		        {
		        	caption: "Example: Progressively Enhanced Table from Markup",
					/*
		        	sortedBy: 
		        	{	
		        		key:"location_code",dir:"desc"
		        	},
		        	*/
		        	selectionMode: "single" 
		        }
		);
		
        this.myDataTable.subscribe("rowMouseoverEvent", this.myDataTable.onEventHighlightRow); 
        this.myDataTable.subscribe("rowMouseoutEvent", this.myDataTable.onEventUnhighlightRow); 
        this.myDataTable.subscribe("rowClickEvent", this.myDataTable.onEventSelectRow); 
	};
		
	this.parseNumberFromCurrency = function(sString) {
	    // Remove dollar sign and make it a float
	    return parseFloat(sString.substring(1));
	};
}	
); 