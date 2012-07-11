//--------------------------------------------------------
// Declaration of gab.index vars
//--------------------------------------------------------
	//define SelectButton
 	var selectsButtons = '';

	// define checkbox
	var oCheckButton_0;

	// define buttons
	var oNormalButton_0, oNormalButton_1, oNormalButton_2, oNormalButton_3;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"},
	{order:2, name:'btn_export', funct:"onDownloadClick"},
	{order:3, name:'btn_reset', funct:"onResetClick"}
	]

	// define Text buttons
	var textImput = [
	{order:0, name:'address',	id:'txt_address'},
	{order:1, name:'location_code',	id:'txt_location_code'},
	{order:2, name:'gaards_nr',	id:'txt_gaards_nr'},
	{order:3, name:'bruksnr',	id:'txt_bruksnr'},
	{order:4, name:'feste_nr',	id:'txt_feste_nr'},
	{order:5, name:'seksjons_nr',	id:'txt_seksjons_nr'},
	{order:6, name:'check_payments',	id:'txt_check_payments'}
	]

	var toolTips =
	[
	 	{name:'map',title:'Map', description:'View map',ColumnDescription:''},
	 	{name:'gab',title:'Gab', description:'View gab-info',ColumnDescription:''},
	 	{name:'btn_export', title:'download', description:'Download table to your browser',ColumnDescription:''}
	]


	// define the hidden column in datatable
	var config_values = {
	 date_search : 0 //if search has link "Data search"
	};

	var fields_add = new Array();
/****************************************************************************************/
	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//nothing
		}
		else if(flag_particular_setting=='update')
		{
			if (oCheckButton_0.get("checked"))
			{
				if (fields_add == '' || fields_add == null)
				{
					for(var x=0; x<values_ds.headers.length; x++)
			   		{
						myDataTable.insertColumn({key:values_ds.headers[x], label:values_ds.headers[x].replace(/\_/g,"/")});
						fields_add[x] = values_ds.headers[x];
				   	}
					var oColumn = myDataTable.getColumn('hits');
				   	// Show Column
				   	myDataTable.showColumn(oColumn);
				}
			}
			else if (YAHOO.lang.isArray(fields_add))
			{
				for(var x=0; x<fields_add.length; x++)
		   		{
					 var oColumn = myDataTable.getColumn(fields_add[x]);
					 myDataTable.removeColumn(oColumn);
			   	}
				var oColumn = myDataTable.getColumn('hits');
			   	// Hide Column
			   	myDataTable.hideColumn(oColumn);
				fields_add = new Array();
			}
			myDataTable.render();
		}

		//--focus for txt_query---
		YAHOO.util.Dom.get(textImput[0].id).value = path_values.address;
		YAHOO.util.Dom.get(textImput[0].id).focus();
	}
/****************************************************************************************/
	  this.onPaymentsClick = function()
	   {
			if (oCheckButton_0.get("checked")) {
				YAHOO.util.Dom.get("txt_check_payments").value = 1;
			} else {
				YAHOO.util.Dom.get("txt_check_payments").value = 0;
			}
	   }
/****************************************************************************************/
	  this.onResetClick = function()
	   {
			oCheckButton_0.set("checked", false);
			YAHOO.util.Dom.get("txt_check_payments").value = 0;
	   }
/****************************************************************************************/
	  this.myParticularRenderEvent = function()
	  {

		   	for(var i=0; i < myColumnDefs.length;i++)
			{
				if( myColumnDefs[i].sortable )
				{
					YAHOO.util.Dom.getElementsByClassName( 'yui-dt-resizerliner' , 'div' )[i].style.background  = '#D8D8DA url(phpgwapi/js/yahoo/assets/skins/sam/sprite.png) repeat-x scroll 0 -100px';
				}

				if( !myColumnDefs[i].visible && myColumnDefs[i].key != "hits")
				{
					var sKey = myColumnDefs[i].key;
					myDataTable.hideColumn(sKey);
				}
				//title columns alwyas center
				YAHOO.util.Dom.getElementsByClassName( 'yui-dt-resizerliner', 'div' )[0].style.textAlign = 'center';
			}

		    var fields = new Array();
			for(var x= 0; x<values_ds.headers_all.length; x++)
			{
				fields[x] = values_ds.headers_all[x];
		    }

		    myDataSource.responseSchema =
		    {
				resultsList: "records",
				fields: fields,
				metaFields : {
	            			  totalRecords: 'totalRecords' // The totalRecords meta field is a "magic" meta, and will be passed to the Paginator.
	        				 }
		    };

	  }
/****************************************************************************************/
	YAHOO.util.Event.addListener(window, "load", function()
			{
				//avoid render buttons html
				YAHOO.util.Dom.getElementsByClassName('toolbar','div')[0].style.display = 'none';
				var loader = new YAHOO.util.YUILoader();
				loader.addModule({
					name: "anyone", //module name; must be unique
					type: "js", //can be "js" or "css"
				    fullpath: property_js //'property_js' have the path for property.js, is render in HTML
				    });

				loader.require("anyone");

				//Insert JSON utility on the page

			    loader.insert();

				oCheckButton_0 = new YAHOO.widget.Button('txt_check', {label:"", value:"0",checked:false});
				oCheckButton_0.on("click", eval('onPaymentsClick'));
			});



