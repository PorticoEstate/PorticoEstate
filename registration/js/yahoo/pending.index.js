	var oArgs_edit = {menuaction:'registration.uipending.edit'};
	var edit_Url = phpGWLink('index.php', oArgs_edit);

	formatLinkPending = function(elCell, oRecord, oColumn, oData)
	{
		var id = oRecord.getData(oColumn.key);
		elCell.innerHTML = '<a href="' + edit_Url + '&id=' + id + '">Link</a>'; 
	};

	function checkAll(myclass)
  	{
		controls = YAHOO.util.Dom.getElementsByClassName(myclass);
		for(i=0;i<controls.length;i++)
		{
			//for class=mychecks, they have to be interchanged
			//checkbox is located within td->div->input. To get the input-object, use controls[i].children[0].children[0]
			if(myclass=='mychecks')
			{
				if(controls[i].children[0].children[0].checked)
				{
					controls[i].children[0].children[0].checked = false;
				}
				else
				{
					controls[i].children[0].children[0].checked = true;
				}
			}
			//for the rest, always id checked
			else
			{
				controls[i].children[0].children[0].checked = true;
			}
		}
	}
	
	function saveLocationToControl()
	{
		var control_id_value = document.getElementById('control_id').value;
		
		if( !(control_id_value > 0) ){
			var choose_control_elem = document.getElementById('choose_control');
			var error_elem = YAHOO.util.Dom.getElementsByClassName('error_msg')[0];
						
			error_elem.style.display = 'block';
			
			return false;
		}else{
			var error_elem = YAHOO.util.Dom.getElementsByClassName('error_msg')[0];
			error_elem.style.display = 'none';
		}
				
		var divs = YAHOO.util.Dom.getElementsByClassName('location_submit');
		var mydiv = divs[divs.length-1];

		// styles for dont show
		

		valuesForPHP = YAHOO.util.Dom.getElementsByClassName('mychecks');
		var values_return = ""; //new Array(); 
			
		for(i=0;i<valuesForPHP.length;i++)
		{
			if(valuesForPHP[i].children[0].children[0].checked)
			{
				if(values_return != "")
					values_return +="|"+valuesForPHP[i].parentNode.firstChild.firstChild.firstChild.firstChild.nodeValue+';'+valuesForPHP[i].children[0].children[0].value;
				else
					values_return += valuesForPHP[i].parentNode.firstChild.firstChild.firstChild.firstChild.nodeValue+';'+valuesForPHP[i].children[0].children[0].value;
			}
		}
		
		if( !(values_return.length > 0) ){
			var datatable_container_elem = document.getElementById('datatable-container');
			var error_elem = YAHOO.util.Dom.getElementsByClassName('error_msg')[1];
						
			error_elem.style.display = 'block';
			
			return false;
		}else{
			var error_elem = YAHOO.util.Dom.getElementsByClassName('error_msg')[1];
			error_elem.style.display = 'none';
		}

		mydiv.style.display = "none";

		var returnfield = document.createElement('input');
		returnfield.setAttribute('name', 'values_assign');
		returnfield.setAttribute('type', 'text');
		returnfield.setAttribute('value', values_return);
		mydiv.appendChild(returnfield);
		
		var control_id_field = document.createElement('input');
		control_id_field.setAttribute('name', 'control_id');
		control_id_field.setAttribute('type', 'text');
		control_id_field.setAttribute('value', control_id_value);
		mydiv.appendChild(control_id_field);
	}

