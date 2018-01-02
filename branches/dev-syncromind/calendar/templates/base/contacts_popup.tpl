<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>{lang_title}</title>
		<script type="text/javascript" src="{webserver_url}/phpgwapi/js/core/base.js"></script>
		<script type="text/javascript" src="{webserver_url}/phpgwapi/js/core/events.js"></script>
		<script type="text/javascript" src="{webserver_url}/phpgwapi/js/sarissa/sarissa.js"></script>
		<script type="text/javascript">

			var iCat = 0;
			var strBaseURL = '{base_url}';
			
			function updateCat()
			{
				var oSelect = document.getElementById('search_cat');
				iCat = oSelect.options[oSelect.selectedIndex].value;
			}

			function doSearch()
			{
				var oSearch = document.getElementById('search_search');
				
				if ( oSearch.value.length < 3 && oSearch.value != '*' )
				{
					return true;
				}

				document.getElementById('search_list').innerHTML = '';
				
				var oArgs = 
					{
						menuaction : 'calendar.uicalendar.participants_popup',
						lookup : oSearch.value,
						con_type: (document.getElementById('search_contype_2').checked ? 2 : 1)
					};
				
				if ( iCat )
				{
					oArgs.cat_id = iCat;
				}
				
				var strURL = phpGWLink('index.php', oArgs, true);

				var oHTTP = new XMLHttpRequest();
				oHTTP.open('GET', strURL, true);
				oHTTP.onreadystatechange = function()
				{
					if ( oHTTP.readyState == 4 )
					{
						document.getElementById('search_results').innerHTML = 
							"<select id=\"search_list\" name=\"search_list\" multiple=\"multiple\" size=\"10\">" 
								+ oHTTP.responseText 
								+ "</select>";
					}
				}
				oHTTP.send('');
			}

			function addContact()
			{
				var oSource = document.getElementById('search_list');
				var oTarget = document.getElementById('selected_list');
				
				for ( var i = 0; i < oSource.options.length; ++i )
				{
					if (  oSource.options.item(i).selected )
					{
						oSource.options.item(i).selected = false;
						oTarget.appendChild(oSource.options.item(i).cloneNode(true));
					}
				}
				
				var oOpenerSelect = window.opener.document.getElementById('event_participants');
				removeAllChildren(oOpenerSelect);
				adopt(oTarget, oOpenerSelect);
				for ( i = 0; i < oOpenerSelect.options.length; ++i)
				{
					oOpenerSelect.options.item(i).selected = true;
				}
				
				return false;
			}

			function removeContact()
			{
				var oSource = document.getElementById('selected_list');

				for ( var i = 0; i < oSource.options.length; ++i )
				{
					if (  oSource.options[i].selected )
					{
						oSource.options[i] = null;
						i = 0;//this is a hack that i am not proud of, but it works :)
					}
				}

				var oOpenerSelect = window.opener.document.getElementById('event_participants');
				removeAllChildren(oOpenerSelect);
				adopt(oSource, oOpenerSelect);
				for ( i = 0; i < oOpenerSelect.options.length; ++i)
				{
					oOpenerSelect.options.item(i).selected = true;
				}
				return false;
			}

			function removeAllChildren(oTarget)
			{
				if ( oTarget.childNodes )
				{
					while ( oTarget.childNodes.length )
					{
						oTarget.removeChild(oTarget.firstChild);
					}
				}
			}

			function adopt(oSource, oTarget)
			{
				if ( oSource.childNodes.length )
				{
					for ( var i = 0; i < oSource.childNodes.length; ++i )
					{
						oTarget.appendChild(oSource.childNodes.item(i).cloneNode(true));
					}
				}
			}

			window.onload = function()
			{
				if ( window.opener )
				{
					var oSelect = document.getElementById('selected_list');
					adopt(window.opener.document.getElementById('event_participants'), oSelect);
				}
				else
				{
					alert('Not called as a popup window. Closing now!');
					window.close();
				}
			}
		</script>

		<style>
			body, button
			{
				background-color: #d9d9d9;
			}

			body, button, div, fieldset, p, td
			{
				font-size: 12px;
			}

			legend
			{
				font-size: 13px;
				font-weight: bold;
			}

			button img
			{
				vertical-align: middle;
			}
			
			label, input, #cal_contacts_search select, .mock_label
			{
				display: block;
				float: left;
				margin-bottom: 10px;
				width: 350px;
			}

			input.radio
			{
				width: auto;
			}

			label, .mock_label
			{
				font-weight: bold;
				margin-right: 10px;
				text-align: right;
				width: 100px;
			}

			label.radio
			{
				text-align: left;
			}

			#cal_contacts_search br
			{
				clear: left;
			}

			#cal_contacts_selection
			{
				height: 220px;
				margin-top: 20px;
			}

			#cal_contacts_selection select, #cal_contacts_selection div
			{
				height: 200px;
				width: 200px;
			}

			#cal_contacts_selection div
			{
				float: left;
			}
			
			#cal_contacts_selection #search_select_btns
			{
				margin: 70px 10px;
				text-align: center;
				width: 120px;
			}
			
			#cal_contacts_selection div button
			{
				margin: 5px 10px;
				width: 100px;
			}

			#cal_contacts_selection button img
			{
				vertical-align: middle;
			}

			#btn_grp
			{
				bottom: 5px;
				display: block;
				position: absolute;
				right: 5px;
				text-align: right;
			}
		</style>
	</head>
	<body>
		<form action="#" onSubmit="return false;">
			<fieldset id="cal_contacts_search">
				<legend>{lang_show_contacts}</legend>

				<label for="search_cat">{lang_category}:</label>
				<select id="search_cat" name="cat" onChange="updateCat();">
					<option value="0" selected="selected">{lang_all}</option> 
					<!-- BEGIN cat_option -->
					<option value="{cat_id}">{cat_name}</option>
					<!-- END cat_option -->
				</select><br />

				<label for="search_search">{lang_search}:</label>
				<input type="text" name="search" id="search_search" onkeyUp="doSearch();" autocomplete="off" /><br />

				<span class="mock_label">{lang_type}:</span>
				<input type="radio" id="search_contype_1" name="contype" checked="checked" value="1" class="radio" />
				<label for="search_contype_1" class="radio">{lang_person}</label>

				<input type="radio" id="search_contype_2" name="contype" value="2" class="radio" />
				<label for="search_contype_2" class="radio">{lang_org}</label>
			</fieldset>

			<fieldset id="cal_contacts_selection">
				<legend>{lang_contacts}</legend>

				<div id="search_results">
					<select id="search_list" name="search_list" multiple="multiple" size="10"></select>
				</div>

				<div id="search_select_btns">
					<button onClick="addContact();">{lang_add}<img src="{img_add}" alt="{lang_add}" /></button><br />
					<button onClick="removeContact();"><img src="{img_remove}" alt="{lang_remove}" />{lang_remove}</button>
				</div>

				<div>
					<select id="selected_list" name="selected_list" multiple="multiple" size="10"></select>
				</div>
			</fieldset>

			<div id="btn_grp">
				<button onClick="window.close();"><img src="{img_close}" alt="{lang_close}" />{lang_close}</button>
			</div>
		</form>
	</body>
</html>
