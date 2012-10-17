	var FormatterRight = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<div align=\"right\">"+oData+"</div>";
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
			
				if(controls[i].checked)
				{
					controls[i].checked = false;
				}
				else
				{
					controls[i].checked = true;
				}
			}
		}
	}

