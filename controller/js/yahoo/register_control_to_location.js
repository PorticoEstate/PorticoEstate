	var formatterCheckLocation = function(elCell, oRecord, oColumn, oData)
	{
		var checked = '';
		var hidden = '';
		if(oRecord.getData('location_registered'))
		{
			checked = "checked = 'checked'";
			hidden = "<input type=\"hidden\" class=\"orig_check\"  name=\"values[control_location_orig][]\" value=\""+oRecord.getData('location_code')+"\"/>";
		}
		elCell.innerHTML = hidden + "<center><input type=\"checkbox\" class=\"mychecks\"" + checked + "value=\""+oRecord.getData('location_code')+"\" name=\"values[control_location][]\"/></center>";
	}

	var FormatterCenter = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center>"+oData+"</center>";
	}

	function checkAll(myclass)
  	{
		controls = YAHOO.util.Dom.getElementsByClassName(myclass);
		for(i=0;i<controls.length;i++)
		{
			if(!controls[i].disabled)
			{
				//for class=transfer_idClass, they have to be interchanged
				if(myclass=="mychecks")
				{
					if(controls[i].checked)
					{
						controls[i].checked = false;
					}
					else
					{
						controls[i].checked = true;
					}
				}
				//for the rest, always id checked
				else
				{
					controls[i].checked = true;
				}
			}
		}
	}
	
	function saveLocationToControl()
	{
		var control_id_value = document.getElementById('control_id').value;
		
		if( !(control_id_value > 0) )
		{
			var choose_control_elem = document.getElementById('choose_control');
			var error_elem = YAHOO.util.Dom.getElementsByClassName('error_msg')[0];
						
			error_elem.style.display = 'block';
			
			return false;
		}
		else
		{
			var error_elem = YAHOO.util.Dom.getElementsByClassName('error_msg')[0];
			error_elem.style.display = 'none';
		}

		var divs = YAHOO.util.Dom.getElementsByClassName('location_submit');
		var mydiv = divs[divs.length-1];

		// styles for dont show

		valuesForPHP		= YAHOO.util.Dom.getElementsByClassName('mychecks');			
		valuesForPHP_orig	= YAHOO.util.Dom.getElementsByClassName('orig_check');

		var myclone = null;
		//add all control to form
		for(i=0;i<valuesForPHP.length;i++)
		{
			myclone = valuesForPHP[i].cloneNode(true);
			mydiv.appendChild(myclone);
		}
		
		for(i=0;i<valuesForPHP_orig.length;i++)
		{
			myclone = valuesForPHP_orig[i].cloneNode(true);
			mydiv.appendChild(myclone);
		}


		var control_id_field = document.createElement('input');
		control_id_field.setAttribute('name', 'control_id');
		control_id_field.setAttribute('type', 'text');
		control_id_field.setAttribute('value', control_id_value);
		mydiv.appendChild(control_id_field);

/*
		if( !(true) )
		{
			var datatable_container_elem = document.getElementById('datatable-container');
			var error_elem = YAHOO.util.Dom.getElementsByClassName('error_msg')[0];

			error_elem.style.display = 'block';

			return false;
		}
		else
		{
			var error_elem = YAHOO.util.Dom.getElementsByClassName('error_msg')[0];
			error_elem.style.display = 'none';
		}
*/
		mydiv.style.display = "none";
	}

