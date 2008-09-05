YAHOO.util.Event.addListener(window, "load", function() {
   	var Dom = YAHOO.util.Dom;
	var oSelectedTR;
	var myDataTableTemp ;
	var prefixSelected = "mckahstvcx";


	function create_menu_list(stValues,source) {
		var temp1 = new Array();
		temp1 = stValues.split('/');
		var MenuButtonMenu = new Array();
		for(i=0 ; i < temp1.length -1 ; i++ )
		{
			temp2 = temp1[i].split('#');
			temp2.push(source);
			MenuButtonMenu.push({text: temp2[1], value: '', onclick: { fn: onMenuItemClick , obj: temp2} });
		}
		return MenuButtonMenu;
	 }

	 function onMenuItemClick(p_sType, p_aArgs, p_oItem) {
		//resset values
	 	oMenuButtonCategory.set("label", ("<em>!no category</em>"));
	 	oMenuButtonDistrict.set("label", ("<em>!no district</em>"));
	 	oMenuButtonPartOFTown.set("label", ("<em>!no part of town</em>"));
	 	oMenuButtonOwnerId.set("label", ("<em>!Show all</em>"));


	 	if(p_oItem[2]=='CatId')
	 		oMenuButtonCategory.set("label", ("<em>" + p_oItem[1] + "</em>"));
	 	if(p_oItem[2]=='DistId')
	 		oMenuButtonDistrict.set("label", ("<em>" + p_oItem[1] + "</em>"));
	 	if(p_oItem[2]=='PartOFTownId')
	 		oMenuButtonPartOFTown.set("label", ("<em>" + p_oItem[1] + "</em>"));
	 	if(p_oItem[2]=='OwnerId')
	 		oMenuButtonOwnerId.set("label", ("<em>" + p_oItem[1] + "</em>"));

	 	//-----alert('valor = '+p_oItem[0]+' origen= '+p_oItem[2]);

	 	/*var ds = phpGWLink('index.php', {menuaction: "property.uilocation.index",
									   address: arraySearch[0].value,
									   // para el campo 'check_payments'
									   location_code: arraySearch[1].value,
									   gaards_nr: arraySearch[2].value,
									   bruksnr: arraySearch[3].value,
									   feste_nr: arraySearch[4].value,
									   seksjons_nr: arraySearch[5].value}, true);*/


	}

	 var hd_CatId = document.getElementById( 'values_cat_id');
	 var MenuButton4CatId = create_menu_list (hd_CatId.value,'CatId');
     var oMenuButtonCategory = new YAHOO.widget.Button("btn_cat_id", { type: "menu", label: "<em>!no category</em>", name: "categorybutton", id: "categorybutton", menu: MenuButton4CatId});


	 var hd_DistId = document.getElementById( 'values_district_id');
     var MenuButton4DistId = create_menu_list (hd_DistId.value,'DistId');
     var oMenuButtonDistrict = new YAHOO.widget.Button("btn_district_id", { type: "menu", label: "<em>!no district</em>", id: "districtbutton", menu: MenuButton4DistId});


	 var hd_PartOFTownId = document.getElementById( 'values_part_of_town_id');
     var MenuButton4PartOFTownId = create_menu_list (hd_PartOFTownId.value,'PartOFTownId');
     var oMenuButtonPartOFTown = new YAHOO.widget.Button("btn_part_of_town_id", { type: "menu", label: "<em>!no part of town</em>", id: "partOFTownbutton", menu: MenuButton4PartOFTownId});


	 var hd_OwnerId = document.getElementById( 'values_owner_list');
     var MenuButton4OwnerId = create_menu_list (hd_OwnerId.value,'OwnerId');
     var oMenuButtonOwnerId = new YAHOO.widget.Button("btn_owner_id", { type: "menu", label: "<em>!Show all</em>", id: "ownerIdbutton", menu: MenuButton4OwnerId});


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
