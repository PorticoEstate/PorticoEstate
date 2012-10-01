	var main_source;
	var oArgs_edit = {menuaction:'registration.uipending.edit'};
	var edit_Url = phpGWLink('index.php', oArgs_edit);

	formatLinkPending = function(elCell, oRecord, oColumn, oData)
	{
		var id = oRecord.getData(oColumn.key);
		elCell.innerHTML = '<a href="' + edit_Url + '&id=' + id + '">' + lang['edit'] + '</a>'; 
	};


	var formatterCheckPending = function(elCell, oRecord, oColumn, oData)
	{
		var checked = '';
		var hidden = '';
		if(oRecord.getData('reg_approved'))
		{
			checked = "checked = 'checked'";
			hidden = "<input type=\"hidden\" class=\"orig_check\"  name=\"values[pending_users_orig][]\" value=\""+oRecord.getData('reg_id')+"\"/>";
		}
		elCell.innerHTML = hidden + "<center><input type=\"checkbox\" class=\"mychecks\"" + checked + "value=\""+oRecord.getData('reg_id')+"\" name=\"values[pending_users][]\"/></center>";
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
	
	function onSave()
	{
		var divs = YAHOO.util.Dom.getElementsByClassName('user_submit');
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

		mydiv.style.display = "none";
	}

