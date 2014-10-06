
			function move(fboxname, tboxname, sboxname, cboxname) {
				var arrFbox = new Array();
				var arrTbox = new Array();
				var arrLookup = new Array();
				var i;

				fbox = document.body_form.elements[fboxname];
				tbox = document.body_form.elements[tboxname];

				for (i = 0; i < tbox.options.length; i++) 
				{
					arrLookup[tbox.options[i].text] = tbox.options[i].value;
					arrTbox[i] = tbox.options[i].text;
				}
				var fLength = 0;
				var tLength = arrTbox.length;
				for(i = 0; i < fbox.options.length; i++) 
				{
					arrLookup[fbox.options[i].text] = fbox.options[i].value;
					if (fbox.options[i].selected && fbox.options[i].value != "") 
					{
						arrTbox[tLength] = fbox.options[i].text;
						tLength++;
					}
					else 
					{
						arrFbox[fLength] = fbox.options[i].text;
						fLength++;
					}
				}
				arrFbox.sort();
				arrTbox.sort();
				fbox.length = 0;
				tbox.length = 0;

				var c;
				for(c = 0; c < arrFbox.length; c++) 
				{
					var no = new Option();
					no.value = arrLookup[arrFbox[c]];
					no.text = arrFbox[c];
					fbox[c] = no;
				}
				for(c = 0; c < arrTbox.length; c++) 
				{
					var no = new Option();
					no.value = arrLookup[arrTbox[c]];
					no.text = arrTbox[c];
					tbox[c] = no;
				}

				if(sboxname && cboxname)
				{
					move_cbo(sboxname, cboxname);
				}
			}

		function move_cbo(sboxname, cboxname) {
			sbox = document.body_form.elements[sboxname];
			cbox = document.body_form.elements[cboxname];
			if(sbox.length > 0)
			{
				sel_opt = sbox.options[sbox.selectedIndex].text;
			}
			else
			{
				sel_opt="";
			}
			sbox.length = 0;
			for(c = 0; c < cbox.length; c++) 
			{
				var no = new Option();
				no.value = cbox[c].value;
				no.text = cbox[c].text;
				if(no.text == sel_opt)
				{
					i = c;
				}
				sbox[c] = no;
			}
			if(i>0)
			{
				sbox.options[i].selected = true;
			}
		}

		function process_list(allboxname, myboxname) {
			mybox = document.body_form.elements[myboxname];
			for(c = 0; c < mybox.options.length; c++) 
			{
				mybox.options[c].selected = true;
			}
		}

		function showHide(sDiv)
		{
			var oDiv = document.getElementById(sDiv);
			if (oDiv)
				oDiv.style.display = oDiv.style.display == "none" ? "" : "none";
		}

