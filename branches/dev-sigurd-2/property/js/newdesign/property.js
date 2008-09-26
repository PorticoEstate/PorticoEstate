this.filter_data = function(query)
{
	strQuery = query;
	buildQuery(strQuery);
	document.getElementById('txt_query').value = strQuery;
}



 YAHOO.util.Event.addListener(window, "load", function() {
     var Dom = YAHOO.util.Dom;
  var oSelectedTR;
  var myDataTableTemp ;
 //********************ce**********************

   var type_id = YAHOO.util.Dom.get( 'type_id' );

   var hd_CatId, hd_DistId, hd_PartOFTownId, hd_OwnerId = null;
   var MenuButton4CatId, MenuButton4PartOFTownId, MenuButton4DistId, MenuButton4OwnerId = new Array();
   var array_cat_id, array_district_id, array_part_of_town_id, array_owner_list = new Array();
   var oMenuButtonCategory, oMenuButtonPartOFTown, oMenuButtonDistrict, oMenuButtonOwnerId = null;
   var oPushButton1 = null;

   var menu_values_district_id, menu_values_cat_id, menu_values_part_of_town_id, menu_values_owner_list = null;










 /********************************************************************************
 * create a array whith values strValues (..#../..#). Necesary for selected nested
 */
  function create_array_values_list(stValues) {
   var temp1,temp2,temp3 = new Array();

   temp1 = stValues.split('/');
   for(i=0 ; i < temp1.length -1 ; i++ ) // -1 because la string has a '/' at last
   {
    temp2 = temp1[i].split('#');
    temp3[i] = new Array();
    for(j=0 ; j < temp2.length ; j++ )
    {
     temp3[i][j]=temp2[j];
    }
   }
   return temp3;
   }


 /********************************************************************************
 * stValues:  values of select control, separate whit / and #
 * source: indicate the variable-name passed in the URL by GET
 */
  function create_menu_list(stValues,source) {
   var temp1, temp2, MenuButtonMenu = new Array();
   temp1 = stValues.split('/');
   for(i=0 ; i < temp1.length -1 ; i++ ) // -1 because the string has a '/' at last
   {
    temp2 = temp1[i].split('#');
    temp2.push(source);
    //temp2.push(i); se usara para el check
    var obj_temp = {id: temp2[3], text: temp2[1], value: temp2[0], onclick: { fn: onMenuItemClick , obj: temp2} };
    /*if(i==0)
     obj_temp.checked = true;*/
    MenuButtonMenu.push(obj_temp);

 }
   return MenuButtonMenu;
   }

 /********************************************************************************
 * p_oItem: values passed
 * p_oItem[0]: id
 * p_oItem[1]: texto
 * p_oItem[2]:variable-name GET
 * p_oItem[3]:order option of the select
 */
   function onMenuItemClick(p_sType, p_aArgs, p_oItem) {
    //create objet URL whith filter-values empty
    //path_values =  {menuaction: "property.uilocation.index", type_id: type_id.value, status:'', cat_id:'', district_id:'', part_of_town_id:'', filter:'', query:''};

    if(p_oItem[2]=='cat_id')
    {
     //assign label to control selected
     oMenuButtonCategory.set("label", ("<em>" + p_oItem[1] + "</em>"));
     //use field ID for put the value selected
     oMenuButtonCategory.set("value", p_oItem[0]);
          //assign filter-values
          path_values.cat_id = p_oItem[0];
    }
    if(p_oItem[2]=='district_id'){
     oMenuButtonDistrict.set("label", ("<em>" + p_oItem[1] + "</em>"));
     oMenuButtonDistrict.set("value", p_oItem[0]);
     path_values.district_id = p_oItem[0];
     }
    if(p_oItem[2]=='part_of_town_id'){
     oMenuButtonPartOFTown.set("label", ("<em>" + p_oItem[1] + "</em>"));
     oMenuButtonPartOFTown.set("value", p_oItem[0]);
     path_values.part_of_town_id = p_oItem[0];
     }
    if(p_oItem[2]=='filter'){
     oMenuButtonOwnerId.set("label", ("<em>" + p_oItem[1] + "</em>"));
     oMenuButtonOwnerId.set("value", p_oItem[0]);
     path_values.filter = p_oItem[0];

     }

  //get values of all selected controls
    path_values.cat_id = oMenuButtonCategory.get("value");
    path_values.district_id = oMenuButtonDistrict.get("value");
    path_values.part_of_town_id = oMenuButtonPartOFTown.get("value");
    path_values.filter = oMenuButtonOwnerId.get("value");

	  //**********nota falta el filtro texto ************
		//destroy actual ContextMenu & DataTable
	    myContextMenu.destroy();
		myDataTable.destroy();

		//create DataSource & ContextMenu & DataTable
     init_datatable();

    // Update select PART OF TOWN
    var callback ={
      success: function(o) {
         eval("values = "+o.responseText);
         var new_value = values.hidden.part_of_town_id[0].value;
         var new_id = values.hidden.part_of_town_id[0].id;
         MenuButton4PartOFTownId = create_menu_list (new_value,'part_of_town_id');

       try{
       oMenuButtonPartOFTown.getMenu().clearContent();
       oMenuButtonPartOFTown.getMenu().itemData = MenuButton4PartOFTownId;
       }
       catch(c)
       { alert(c);
       }
	      oMenuButtonPartOFTown.set("value",new_id);
      },
      failure: function(o) {window.alert('Server or your connection is death.');},
      //cache:false
    }

    try{
        YAHOO.util.Connect.asyncRequest('URL',ds, callback);
    }catch(c) {
        alert(c);
    }
  }

  this.onSearchClick = function()
  {


      //get values of all selected controls
        path_values.cat_id = oMenuButtonCategory.get("value");
        path_values.district_id = oMenuButtonDistrict.get("value");
        path_values.part_of_town_id = oMenuButtonPartOFTown.get("value");
        path_values.filter = oMenuButtonOwnerId.get("value");

        path_values.query = document.getElementById('txt_query').value;




            myContextMenu.destroy();
            myDataTable.destroy();

            //create DataSource & ContextMenu & DataTable
         init_datatable();
   }

   this.onDownloadClick = function()
   {
		ds_download = phpGWLink('index.php',download_values );
		window.open(ds_download,'window');
   }


  this.init_filter = function()
  {
    //create button
     oPushButton1 = new YAHOO.widget.Button("btn_search");
     oPushButton1.on("click", onSearchClick);

     oBtnExport = new YAHOO.widget.Button("btn_export");
     oBtnExport.on("click", onDownloadClick);


    //create select controls
    hd_CatId = document.getElementById('values_cat_id');
    MenuButton4CatId = create_menu_list (hd_CatId.value,'cat_id');
    array_cat_id = create_array_values_list(hd_CatId.value);
    menu_values_cat_id = { type: "menu", label:"<em>"+ array_cat_id[0][1]+"</em>", id: "categorybutton", value:"", menu: MenuButton4CatId};
    oMenuButtonCategory = new YAHOO.widget.Button("btn_cat_id", menu_values_cat_id);

    hd_DistId = document.getElementById('values_district_id');
       MenuButton4DistId = create_menu_list (hd_DistId.value,'district_id');
       array_district_id = create_array_values_list(hd_DistId.value);
       menu_values_district_id = { type: "menu", label:"<em>"+ array_district_id[0][1]+"</em>", id: "districtbutton",  value:"", menu: MenuButton4DistId};
       oMenuButtonDistrict = new YAHOO.widget.Button("btn_district_id", menu_values_district_id);

    hd_PartOFTownId = document.getElementById('values_part_of_town_id');
    MenuButton4PartOFTownId = create_menu_list (hd_PartOFTownId.value,'part_of_town_id');
    array_part_of_town_id = create_array_values_list(hd_PartOFTownId.value);
    menu_values_part_of_town_id = { type: "menu", label: "<em>"+array_part_of_town_id[0][1]+"</em>", id: "partOFTownbutton",  value:"", menu: MenuButton4PartOFTownId};
    oMenuButtonPartOFTown = new YAHOO.widget.Button("btn_part_of_town_id", menu_values_part_of_town_id);

    hd_OwnerId = document.getElementById('values_owner_list');
    MenuButton4OwnerId = create_menu_list (hd_OwnerId.value,'filter');
    array_owner_list = create_array_values_list(hd_OwnerId.value);
    menu_values_owner_list = { type: "menu", label: "<em>"+array_owner_list[0][1]+"</em>", id: "ownerIdbutton",  value:"", menu: MenuButton4OwnerId};
    oMenuButtonOwnerId = new YAHOO.widget.Button("btn_owner_id", menu_values_owner_list);
 }

//*************************************************************** alejandro *******************************************************************

  function ActionToPHP(task,argu)
 	{
  		var callback = { success:success_handler, failure:failure_handler, timeout: 10000 };
  		var sUrl = phpGWLink('index.php', {menuaction: "property.bolocation.delete",location_code:argu[0].value}, true);
  		var postData = "";
		for(cont=0; cont < argu.length; cont++)
  		{
   			postData = "&"+argu[cont].variable + "=" + argu[cont].value ;
  		}
		postData = "task="+task+postData;
  		var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback,postData);
	}

  function success_handler(o)
  {
     window.alert(o.responseText);
   }

   function failure_handler(o)
   {
     window.alert('Server or your connection is death.');
   }


   function onContextMenuBeforeShow(p_sType, p_aArgs)
   {
   var oTarget = this.contextEventTarget;

      if (this.getRoot() == this) {

    if(oTarget.tagName != "TD")
    {
     oTarget = Dom.getAncestorByTagName(oTarget, "td");
    }

    oSelectedTR = Dom.getAncestorByTagName(oTarget, "tr");
    oSelectedTR.style.backgroundColor  = 'blue' ;
             oSelectedTR.style.color = "white";
             YAHOO.util.Dom.addClass(oSelectedTR, prefixSelected);
             //alert(YAHOO.util.Dom.get(oSelectedTR).className);

         }
     }

     function onContextMenuHide(p_sType, p_aArgs) {
    if (this.getRoot() == this && oSelectedTR) {
     oSelectedTR.style.backgroundColor  = "" ;
          oSelectedTR.style.color = "";
             Dom.removeClass(oSelectedTR, prefixSelected);
      }
   }

     function onContextMenuClick(p_sType, p_aArgs, p_myDataTable)
     {
   var task = p_aArgs[1];
            if(task)
            {
                // Extract which TR element triggered the context menu
                var elRow = p_myDataTable.getTrEl(this.contextEventTarget);
                if(elRow)
                {
                    switch(task.groupIndex)
                    {
                        case 0:     // View
                            var oRecord = p_myDataTable.getRecord(elRow);
       						break;
                        case 1:     // Edit
                            var oRecord = p_myDataTable.getRecord(elRow);
       						break;
                        case 2:     // Delete row upon confirmation
                            var oRecord = p_myDataTable.getRecord(elRow);
                            if(confirm("Are you sure you want to delete ?"))
                            {
	                             alert(oRecord.getData("location_code"));return false;
	                             ActionToPHP("deleteitem",[{variable:"id",value:oRecord.getData("location_code")}]);
	                             p_myDataTable.deleteRow(elRow);
                         	}
                         	break;
                        case 3:     // Filter
                            var oRecord = p_myDataTable.getRecord(elRow);
       						break;
                    }
                }
            }
        };


    function GetMenuContext()
  {
   return [[
             { text: "View"}],[
             { text: "Edit"}],[
                 /*submenu: { id: "applications", itemdata: [
                           {text:"Filter 1", onclick: { fn: onMenuItemClick, obj: 0 }},
                           {text:"Filter 2", onclick: { fn: onMenuItemClick, obj: 1 }},
                           {text:"Filter 3", onclick: { fn: onMenuItemClick, obj: 2 }},
                           {text:"Filter 4", onclick: { fn: onMenuItemClick, obj: 3 }}
                       ] }}],[*/
             { text: "Delete"}],[
             { text: "New"}]
         ];
  }
var flag = 0;
//var myColumnDefs;
var table, myDataSource,myDataTable, myContextMenu ;
table = YAHOO.util.Dom.getElementsByClassName  ( 'datatable' , 'table' );


eval("var path_values = {"+base_java_url+"}");
eval("var download_values = {"+download_java_url+"}");

ds_download = phpGWLink('index.php',download_values );



var ds;
var myPaginator = null
var flag = 0;

	this.buildQuery = function(strQuery)
	{

		path_values.query = strQuery;

		myContextMenu.destroy();
		myDataTable.destroy();

		init_datatable();
	}

	this.init_datatable = function()
	{
		ds = phpGWLink('index.php',path_values , true);

			myDataSource = new YAHOO.util.DataSource(ds);
			myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;

   		// Compute fields from column definitions



	   	var fields = new Array();
	   	for(var i=0; i < myColumnDefs.length;i++)
   		{
			fields[i] = myColumnDefs[i].key;
	   	}


	   // When responseSchema.totalRecords is not indicated, the records
	   // returned from the DataSource are assumed to represent the entire set
	   myDataSource.responseSchema =
	   {
			resultsList: "records",
			    fields: fields,
			    metaFields : {
            				  totalRecords: 'totalRecords' // The totalRecords meta field is a "magic" meta, and will be passed to the Paginator.
        					 }
	   };
			var container = YAHOO.util.Dom.getElementsByClassName( 'datatable-container' , 'div' );

			//--- OK INICIAL --this.myDataTable = new YAHOO.widget.DataTable(container[0], myColumnDefs, this.myDataSource,{initialRequest:"&1"});

			/*************** BEGIN *************************************************** */






			    var buildQueryString = function (state,dt) {
			        path_values.start = state.pagination.recordOffset;

					myContextMenu.destroy();
	 				myDataTable.destroy();

			        init_datatable();


			        myPaginator_config.updateOnChange = true;

			        myDataTable.subscribe("updateOnChangeChange", myPaginator.setPage(5));
			        myPaginator. updateVisibility  ( );
			        //myDataTable.paginator();

					//var tmp = myPaginator.initialPage;
					//myPaginator.render();
					//exit();
					};


			    var myPaginator_config = {
			        containers         : ['paging'],
			        //updateOnChange	   : true,
			        pageLinks          : 30,
			        rowsPerPage        : 15, //MAXIMO el PHPGW me devuelve 15 valor configurado por preferencias
			        rowsPerPageOptions : [15,30,60],
			        template           : "<strong>{CurrentPageReport}</strong> {PreviousPageLink} {PageLinks} {NextPageLink} {RowsPerPageDropdown}"
			    }


			   myPaginator = new YAHOO.widget.Paginator(myPaginator_config);

			  var myTableConfig = {
			        initialRequest         : '&1', //'startIndex=0&results=25'
			        generateRequest        : buildQueryString,
			        paginationEventHandler : YAHOO.widget.DataTable.handleDataSourcePagination,
			        paginator              : myPaginator
			    };




	     myDataTable = new YAHOO.widget.DataTable(container[0], myColumnDefs, myDataSource, myTableConfig);

			/* *************************************************************************** */

   myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:myDataTable.getTbodyEl()});
   var _submenuT = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:myDataTable.getTbodyEl()});
   oContextMenuItems =  myContextMenu;

   myContextMenu.addItems(GetMenuContext(_submenuT));

   myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
   myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);

   myDataTable.subscribe("rowClickEvent",
   function (oArgs)
   {
		var elTarget = oArgs.target;
		var oRecord = this.getRecord(elTarget);
		Exchange_values(oRecord);
   }
   );




   myContextMenu.subscribe("beforeShow", onContextMenuBeforeShow);
   myContextMenu.subscribe("hide", onContextMenuHide);
   //Render the ContextMenu instance to the parent container of the DataTable
   myContextMenu.subscribe("click", onContextMenuClick, myDataTable);

   //cramirez, fire call init_datatable again before click in column
    myDataTable.subscribe("beforeSortedByChange",beforeSorted,myDataTable);

   myContextMenu.render(container[0]);

   var oColumn = myDataTable.getColumn(0);

	// Hide Column
	oColumn.className = "hide_field";

   for(var i=0; i < myColumnDefs.length;i++)
	        {
	        	if( myColumnDefs[i].sortable )
	        	{
		        	YAHOO.util.Dom.getElementsByClassName( 'yui-dt-col-'+ myColumnDefs[i].key , 'div' )[0].style.backgroundColor  = '#D4DBE7';
	        	}

	        	if( !myColumnDefs[i].visible )
	        	{
		        	YAHOO.util.Dom.getElementsByClassName( 'yui-dt-col-'+ myColumnDefs[i].key , 'div' )[0].style.display = 'none';
	        	}

	        }
       }


function beforeSorted(p_sType, p_aArgs, p_myDataTable)
     {
	 path_values.order = p_sType.newValue.column.source;
	 path_values.start = 0;
	 if(path_values.sort == 'ASC' || path_values.sort == '')
	 {
	 	path_values.sort = 'DESC';
	 }
	 else
	 {
	 	path_values.sort = 'ASC';
	 }

	 //destroy actual ContextMenu & DataTable
     myContextMenu.destroy();
	 myDataTable.destroy();
	 //myPaginator.destroy();

	 init_datatable();



     }


	YAHOO.widget.DataTable.Formatter.myCustom = this.myCustomFormatter;

  init_datatable();
  init_filter();

 });


 this.muestra = function()
   {
   	var oColumn = myDataTable.getRow();
   	alert(oColumn);
   }
