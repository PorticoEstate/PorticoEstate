//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

		//define SelectButton
	 	var selectsButtons = [
		]

		// define buttons
		var oNormalButton_0,oNormalButton_1,oNormalButton_2,oNormalButton_3,oNormalButton_4,oNormalButton_5;
		var normalButtons = [
		    {order:0, name:'btn_test_cron',	funct:"onTestCronClick"},
		    {order:1, name:'btn_enable',	funct:"onEnableClick"},
		    {order:2, name:'btn_disable',	funct:"onDisableClick"},
		    {order:3, name:'btn_delete',	funct:"onDeleteClick"},
			{order:4, name:'btn_search',	funct:"onSearchClick"},
			{order:5, name:'btn_new',		funct:"onNewClick"}
		]

		// define Link Buttons
		var linktoolTips = [
		 ]

	    // define Text buttons
	    var textImput = [
	    {order:0, name:'query',	id:'txt_query'}
	    ]

		var toolTips = [
		]

		var config_values = {
			date_search	: 0,
			PanelLoading: 0
		}

		this.particular_setting = function()
		{
			if(flag_particular_setting=='init')
			{
				oNormalButton_0.focus();
			}
			else if(flag_particular_setting=='update')
			{
			}
		}
		
	/* ****************************************************************************** */
		this.myParticularRenderEvent = function()
		{
		}
	/* ****************************************************************************** */
		this.onTestCronClick = function()
		{
			actionToPHP("test_cron");
		}
	/* ****************************************************************************** */
		this.onEnableClick = function()
  		{
			actionToPHP("enable_alarm");
  		}
	
	/* ****************************************************************************** */
		this.onDisableClick = function()
		{
			actionToPHP("disable_alarm");
		}	
	/* ****************************************************************************** */
		this.onDeleteClick = function()
		{
			actionToPHP("delete_alarm");
		}
		
	/* ****************************************************************************** */
		this.actionToPHP = function( action_button )
  		{		
			//look up "values[action_button]" hidden button AND change news values
			my_hdn_action = YAHOO.util.Dom.get("values[action_button]");
			new_name = "values["+action_button+"]";
			my_hdn_action.setAttribute("id",new_name);
			my_hdn_action.setAttribute("name",new_name);
			my_hdn_action.setAttribute("value",true);
			
			//get the last div in th form
			var divs= YAHOO.util.Dom.getElementsByClassName('field');
			// choose div (id = controlsForm_container) 
			var mydiv = divs[divs.length-1];
			//remove all child of mydiv
			if ( mydiv.hasChildNodes() )
			{
			    while ( mydiv.childNodes.length >= 1 )
			    {
			        mydiv.removeChild( mydiv.firstChild );
			    }
			}
			 
			// styles for dont show
			mydiv.style.display = 'none';

			//get all controls of datatable
			valuesForPHP = YAHOO.util.Dom.getElementsByClassName('myValuesForPHP');
			var myclone = null;
			//add all control to form.
			for(i=0;i<valuesForPHP.length;i++)
			{
				//checkBoxs don't have children.. for that reason the argument is FALSE
				myclone = valuesForPHP[i].cloneNode(true);
				mydiv.appendChild(myclone);
			}

			// find out the unique form
			formObject = document.getElementsByTagName('form');
			// modify the 'form' for send it as POST using asyncronize call
			YAHOO.util.Connect.setForm(formObject[0]);
			
			//get back olds values for values[action_button]
			old_name = "values[action_button]";
			my_hdn_action.setAttribute("id",old_name);
			my_hdn_action.setAttribute("name",old_name);
			my_hdn_action.setAttribute("value",false);

			maintain_pagination_order();
			execute_ds();			 
  		}		
		
	/* ****************************************************************************** */
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
		});