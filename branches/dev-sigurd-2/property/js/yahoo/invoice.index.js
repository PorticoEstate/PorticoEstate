//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

		//define SelectButton
	 	var oMenuButton_0, oMenuButton_1;
	 	var selectsButtons = [
		{order:0, var_URL:'cat_id',	 name:'btn_cat_id',	 style:'',dependiente:''},
		{order:1, var_URL:'user_lid',name:'btn_user_lid',style:'',dependiente:''}
		]

		// define buttons
		var oNormalButton_0, oNormalButton_1, oNormalButton_2, oNormalButton_3;
		var normalButtons = [
		{order:0, name:'btn_search',funct:"onSearchClick"},
		{order:1, name:'btn_new',	funct:"onNewClick"},
		{order:2, name:'btn_save',	funct:"onSave"},
		{order:3, name:'btn_export',funct:"onDownloadClick"}
		]

		// define Text buttons
		var textImput = [
			{order:0, name:'query',	id:'txt_query'}
		]

		// define the hidden column in datatable
		var config_values = {
		column_hidden : [0],
		date_search : 0 //if search has link "Data search"
		}

		this.particular_setting = function()
		{
			if(flag_particular_setting=='init')
			{


			}
			else if(flag_particular_setting=='update')
			{
				//borrar hijos div
				div_message= YAHOO.util.Dom.get("message");
				if ( div_message.hasChildNodes() )
				{
					while ( div_message.childNodes.length >= 1 )
				    {
				        div_message.removeChild( div_message.firstChild );
				    }
				}

				//if exist 'values_ds.message'
				 if(window.values_ds.message)
				 {
				 	// succesfull
				 	if(window.values_ds.message[0].message)
				 	{
				 		for(i=0; i<values_ds.message[0].message.length; i++)
					 	{

					 		oDiv=document.createElement("DIV");
					 		txtNode = document.createTextNode(values_ds.message[0].message[i].msg);
					 		oDiv.appendChild(txtNode);
					 		oDiv.style.color = '#009900';
					 		oDiv.style.fontWeight = 'bold';

					 		div_message.appendChild(oDiv);
					  	}
				 	}

				 	// error
				 	if(window.values_ds.message[0].error)
				 	{
				 		for(i=0; i<values_ds.message[0].error.length; i++)
					 	{

					 		oDiv=document.createElement("DIV");
					 		txtNode = document.createTextNode(values_ds.message[0].error[i].msg);
					 		oDiv.appendChild(txtNode);
					 		oDiv.style.color = '#FF0000';
					 		oDiv.style.fontWeight = 'bold';

					 		div_message.appendChild(oDiv);
					  	}
				 	}

				 }
			}
 			/*
 			//---sum total----
 			div_footer = document.getElementById("footer");
			// delete if exists chlid
			if (YAHOO.util.Dom.inDocument("footer_datatable"))
			{
				div_footer.removeChild(div_footer.firstChild);
			}
			// add chil
			div = document.createElement("div");
			div.setAttribute("id", "footer_datatable");
			sum_total = document.createTextNode(values_ds.sum_total);
			div.appendChild(sum_total);
			div_footer.appendChild(div);
			*/
			//--focus for txt_query---
			YAHOO.util.Dom.get(textImput[0].id).value = path_values.query;
			YAHOO.util.Dom.get(textImput[0].id).focus();
		}

	    var myFormatDate = function(elCell, oRecord, oColumn, oData)
	   	{
	    	elCell.innerHTML = YAHOO.util.Number.format(oData, {decimalPlaces:2, decimalSeparator:",", thousandsSeparator:" "});
	    }
		/********************************************************************************
		 *
		 */
		  this.onSave = function()
		  {
			//get the last div in th form
			var divs= YAHOO.util.Dom.getElementsByClassName('field');
			var mydiv = divs[divs.length-1];
			//remove all child of mydiv
			if ( mydiv.hasChildNodes() )
		    while ( mydiv.childNodes.length >= 1 )
		    {
		        mydiv.removeChild( mydiv.firstChild );
		    }

			// styles for dont show
			mydiv.style.display = 'none';

			//get all controls of datatable
			valuesForPHP = YAHOO.util.Dom.getElementsByClassName('myValuesForPHP');
			var myclone = null;
			//add all control to form
			for(i=0;i<valuesForPHP.length;i++)
			{
				myclone = valuesForPHP[i].cloneNode(false);
				mydiv.appendChild(myclone);
			}
			// find out the unique form
			formObject = document.getElementsByTagName('form');
			// modify the 'form' for send it as POST using asyncronize call
			YAHOO.util.Connect.setForm(formObject[0]);

			execute_ds();

		  }



	//----------------------------------------------------------
		YAHOO.util.Event.addListener(window, "load", function()
		{
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






