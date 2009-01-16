YAHOO.util.Event.addListener(window, "load", function() {

	YAHOO.example.EnhanceFromMarkup = new function() {

			var table = YAHOO.util.Dom.getElementsByClassName  ( 'datatable' , 'table' );

			var ds = phpGWLink('index.php', {menuaction: "newdesign.uinewdesign.gab"}, true);
			//alert( ds );
			this.myDataSource = new YAHOO.util.DataSource(ds);
	        this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;

			// Compute fields from column definitions
			var fields = new Array();
	        for(var i=0; i < myColumnDefs.length;i++) {
	        	fields[i] = myColumnDefs[i].key;
	        }

	        // When responseSchema.totalRecords is not indicated, the records
	        // returned from the DataSource are assumed to represent the entire set
	        this.myDataSource.responseSchema = {
	            resultsList: "records",
	            fields: fields
	        };

	        var container = YAHOO.util.Dom.getElementsByClassName( 'datatable-container' , 'div' );


	        this.myDataTable = new YAHOO.widget.DataTable(container[0], myColumnDefs, this.myDataSource,
	        	{initialRequest:"&1"}
	        );

			// Get a Column
	var oColumn = this.myDataTable.getColumn(1);

	// Hide Column
	this.myDataTable.hideColumn(oColumn);

    };
});
