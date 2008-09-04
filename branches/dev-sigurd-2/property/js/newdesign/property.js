YAHOO.util.Event.addListener(window, "load", function() {

	YAHOO.example.EnhanceFromMarkup = new function() {

			var table = YAHOO.util.Dom.getElementsByClassName  ( 'datatable' , 'table' );
			var type_id = YAHOO.util.Dom.get( 'type_id' );

			var ds = phpGWLink('index.php', {menuaction: "property.uilocation.index",type_id:type_id.value}, true);
			//alert( ds );
			this.myDataSource = new YAHOO.util.DataSource(ds);
	        this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;

			// Compute fields from column definitions
			//alert(myColumnDefs[2].key);
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

    };
});
