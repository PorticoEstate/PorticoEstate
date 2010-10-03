<!-- $Id: projects.js,v 1.2 2005/03/30 15:53:38 ceb Exp $ -->

	function writeOptions(tagname, myarray)
	{
		selectbox = document.getElementById(tagname);

		for(i=0; i < myarray.length; i++)
		{
			myoption = new Option(myarray[i][0], myarray[i][1], true);
			exists = false;
			for(j=0; j < selectbox.options.length; j++)
			{
				if (selectbox.options[j].value == myarray[i][1])
				{
					exists = true;
					j = selectbox.options.length;
				}
			}
			if(!exists)
			{
				selectbox.options[selectbox.length] = myoption;
				selectbox.options[(selectbox.length - 1)].selected = true;
			}
		}
	}

	function clearOptions(tagname)
	{
		selectbox = document.getElementById(tagname);

		for(i=0; i < selectbox.options.length; i++)
		{
			if(selectbox.options[i].selected == true)
			{
				selectbox.options[i] = null;
			}
		}
	}
	
	function clearCustomer()
	{
		var customer_fields = Array('customernr', 'orgaid', 'organame', 'customerid', 'customer');
		for(i=0; i < customer_fields.length; i++)
		{
			document.getElementById(customer_fields[i]).value = '';
		}
	}

	function factor_calculator(type)
	{
		if(type == 'hour')
		{
			document.getElementById('factor').value = document.getElementById('factor').value / 8;
		}
		else
		{
			document.getElementById('factor').value = document.getElementById('factor').value * 8;
		}
	}

	function switch_budget_type(type)
	{
		if(type == 'h')
		{
			document.getElementById('hbudget').style.display = 'block';
			document.getElementById('mbudget').style.display = 'none';
		}
		else
		{
			document.getElementById('mbudget').style.display = 'block';
			document.getElementById('hbudget').style.display = 'none';
		}
	}

	function set_factortr()
	{
		if(document.getElementById('factortype').value == 'project')
		{
			document.getElementById('td1').style.display = 'block';
			document.getElementById('td2').style.display = 'block';
			document.getElementById('td3').style.display = 'block';
		}
		else
		{
			document.getElementById('td1').style.display = 'none';
			document.getElementById('td2').style.display = 'none';
			document.getElementById('td3').style.display = 'none';
		}
	}

	function change_view(id)
	{
		value = document.getElementById(id).style.display
		if (value != 'none') 
			document.getElementById(id).style.display = "none";
		else
			document.getElementById(id).style.display = "block";
	}

	function hideColumn (colIndex)
	{
  		var table = document.all ? document.all.aTable:document.getElementById('aTable');
  		for (var r = 0; r < table.rows.length; r++)
    		table.rows[r].cells[colIndex].style.display = 'none';
	}

	function showColumn (colIndex)
	{
  		var table = document.all ? document.all.aTable:document.getElementById('aTable');
		for (var r = 0; r < table.rows.length; r++)
    		table.rows[r].cells[colIndex].style.display = '';
	}

		function getStyleSheet(name)
		{
			if(!name || !document.styleSheets) {
				return null;
			}
			var i = document.styleSheets.length;
			while(i--)
			{
				var rules = document.styleSheets[i].rules ? document.styleSheets[i].rules :
				document.styleSheets[i].cssRules;
				var j = rules.length;
				while(j--) { 
					names = rules[j].selectorText.split(",");
					for (var k=0; k<names.length; k++) {
						var p = names[k].indexOf("[class~=");
						var s = (p>=0)? names[k].substring(0,p) : names[k];
						if(s.toLowerCase() == name.toLowerCase()) 
							return rules[j]; 
					}
				}
			}
			return null;
		}

		function setStyle(name, attr, value)
		{
			var rule = getStyleSheet(name);
			if(!rule) {
				alert("could not find stylerule "+name);
				return null;
				}
			if(value) rule.style[attr] = value;
			return rule.style[attr];
		}

		function sum_switch()
		{
			value = setStyle("div.node_sum", "display");
			if (value != 'none') 
				setStyle("div.node_sum", "display", "none");
			else
				setStyle("div.node_sum", "display", "block");
		}

