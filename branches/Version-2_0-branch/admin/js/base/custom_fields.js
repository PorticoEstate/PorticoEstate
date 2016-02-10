// Written by Dave Hall 2006
//TODO Convert

var addPanelID = 0;
var addFieldType = 0;
var highlighted = 0;

/**
* saves the new field in the database
*/
function addApply()
{
	var field = {
					name	: document.getElementById('add_field_name').value,
					type	: document.getElementById('add_field_type').options[document.getElementById('add_field_type').selectedIndex].value,
					label	: document.getElementById('add_field_label').value,
					appname	: document.getElementById('add_field_appname').value
					//value 	: document.getElementById('add_field_label').value
				};
	
	if ( field['type'] == 4 )
	{
		field['list'] = getList('add');
	}
	
	var strField =  JSON.stringify(field);
	
	var xhr = new XMLHttpRequest();
	xhr.open('PUT', phpGWLink('/index.php', {menuaction : 'admin.bo_custom_fields.add_field'}), true);
	xhr.onreadystatechange = function()
	{
		if ( xhr.readyState == 4 )
		{
			switch (xhr.status)
			{
				case 200:
					field['id'] = JSON.parse(xhr.responseText);
					appendField(field);
					dialogCancel('add');
					break;
				default:
					alert(xhr.responseText);
			}
		}
	}
	xhr.send(strField);
}

function appendField(field)
{
	
	var tr = document.createElement('tr');
	tr.id = 'row_' + field['id'];
	
	var td = document.createElement('td');
	td.appendChild(document.createTextNode(field['id']));
	tr.onclick = 'higlight(this.id);';
	tr.appendChild(td);
	
	var td = document.createElement('td');
	td.appendChild(document.createTextNode(field['name']));
	tr.appendChild(td);
	
	//I got lazy and used innerHTML - feel free to switch to createElement
	var td = document.createElement('td');
	td.innerHTML = "\t<span class=\"cbStyled\" id=\"mockCheckbox\" + field['id']" + "\">\n"
			+ "\t\t<a class=\"mock_checkbox_checked\" onkeypress=\"toggleCheckbox(this, event.keyCode, 'mockCheckbox" + field['id'] + "');\" onclick=\"toggleCheckbox(this,'', 'mockCheckbox" + field['id'] + "' );return false;\" href=\"#\">\n"
			+ "\t\t\t&nbsp\n"
			+ "\t\t</a>\n"
			+ "\t</span>";
	tr.appendChild(td);
	
	var target = document.getElementById('custom_fields_list');
	target.appendChild(tr);
}

/**
* Handles add button being clicked
*/
function addField(appname)
{
	document.getElementById('modal_bg').style.display = 'block';
	var dialog = document.getElementById('fields_dialog_add');
	resetDialog('add');
	addShowPanel(0); // go back to start
	dialog.style.visibility = 'hidden';//this is done to make it appear smoother on firefox
	dialog.style.display = 'block';
	centerDialog(dialog);
	dialog.style.visibility = 'visible';
}

/**
* Advance the add field wizard to the next pane
*/
function addForward()
{
	switch(addPanelID)
	{
		case 0:
			addShowPanel(1);
			buttonEnable('add_back');
			buttonDisable('add_forward');
			document.getElementById('add_field_name').focus();
			break;
		case 1:
			switch (addFieldType)
			{
				case 1: //text
				case 2: //number
				case 3: //date
				{
					addShowPanel(4);
					buttonDisable('add_back');
					buttonDisable('add_forward', true);
					buttonEnable('add_apply', true);
					break;
				}
				
				case 4: //list
				{
					addShowPanel(2);
					buttonEnable('add_back');
					break;
				}
				
				case 5: //db lookup
				{
					addShowPanel(3);
					buttonEnable('add_back');
					break;
				}
				default:
				{
					return false; //invalid
				}
			}
			break;
		case 2:
			addShowPanel(4);
			break;
		case 3:
			addShowPanel(4);
			break;
		default:
			return false; //invalid
	}
	//buttonDisable('add_forward');
}

/**
* Reverse the add field wizard to the next pane
*/
function addBack()
{
	switch(addPanelID)
	{
		case 1:
			addShowPanel(0);
			buttonDisable('add_back');
			buttonEnable('add_forward');
			break;
		case 2:
		case 3:
		case 4:
			addShowPanel(1);
			buttonEnable('add_back');
			buttonEnable('add_forward');
			break;
		default:
			buttonDisable('add_back');
			return false; //invalid
	}
}

function addFieldPanel1Updated()
{
	if ( document.getElementById('add_field_type').selectedIndex > 0 
		&& document.getElementById('add_field_name').value.length > 0
		&& document.getElementById('add_field_label').value.length
	)
	{
		addFieldType = document.getElementById('add_field_type').selectedIndex;
		buttonEnable('add_forward');
	}
	else
	{
		buttonDisable('add_forward');
	}
}

function addShowPanel(newID)
{
	document.getElementById('fields_add_panel_' + addPanelID).style.display = 'none';
	document.getElementById('fields_add_panel_' + newID).style.display = 'block'
	addPanelID = newID;
}

/* TODO move me to api core */
function centerDialog(elm)
{
	var elmHeight = elm.clientHeight;
	var elmWidth = elm.clientWidth;
	
	var winHeight = getWindowHeight();
	var winWidth = getWindowWidth();
	
	//Put it where we want it
	elm.style.top = parseInt((winHeight - elmHeight) / 2) + 'px';
	elm.style.left = parseInt((winWidth - elmWidth) / 2) + 'px';
}

function dialogCancel(dialogName)
{
	var dialog = document.getElementById('fields_dialog_' + dialogName);
	dialog.style.display = 'none';
	document.getElementById('modal_bg').style.display = 'none';
}

function disableField(id)
{
	var xhr = new XMLHttpRequest();
	xhr.open('GET', phpGWLink('/index.php', {menuaction : 'admin.bo_custom_fields.toggle_field', id : id, state: 0}), true);
	xhr.onreadystatechange = function()
	{
		if ( xhr.readyState == 4 )
		{
			switch (xhr.status)
			{
				case 200:
					var targetCb = document.getElementById('mockCheckbox' + id).getElementsByTagName('a').item(0);
					targetCb.className = 'mock_checkbox';
					break;
				default:
					alert(xhr.responseText);
			}
		}
	}
	xhr.send(strField);
}

function editField()
{
	if ( highlighted == 0 )
	{
		return false; //nothing to see here folks keep moving
	}
	
	var xhr = new XMLHttpRequest();
	xhr.open('GET', phpGWLink('/index.php', {menuaction : 'admin.bo_custom_fields.get_field', id : highlighted}), true);
	xhr.onreadystatechange = function()
	{
		if ( xhr.readyState == 4 )
		{
			if ( xhr.status == 200 )
			{
				field =  JSON.parse(xhr.responseText);
				
				document.getElementById('edit_id').innerHTML = field['id'];
				document.getElementById('edit_name').innerHTML = field['name'];
				document.getElementById('edit_label').innerHTML = field['label'];
				var typeOptions = document.getElementById('edit_type').options;
				for ( var i = 0; i < typeOptions.length; ++i )
				{
					if ( field['type_id'] == typeOptions[i].value )
					{
						typeOptions[i].selected = true;
					}
					else
					{
						typeOptions[i].selected = false;
					}
				}
				
				if ( field['type_id'] < 4 )
				{
					document.getElementById('view_list').style.display = 'none';
				}
				else
				{
					for ( var i = 0; i < field['values'].length; ++i )
					{
						
					}
				}

				document.getElementById('modal_bg').style.display = 'block';
				var dialog = document.getElementById('fields_dialog_edit');
				dialog.style.visibility = 'hidden';//this is done to make it appear smoother on firefox
				dialog.style.display = 'block';
				centerDialog(dialog);
				dialog.style.visibility = 'visible';
			}
			else
			{
				alert(JSON.parse(xhr.responseText));
			}
		}
	}
	xhr.send();
}

function disableNotRemove()
{
	id = document.getElementById('remove_id').value;
	if ( id > 0 )
	{
		disableField(id);
	}
	dialogCancel('remove');
}

function doRemove()
{
	var id = document.getElementById('remove_id').value;
	var xhr = new XMLHttpRequest();
	xhr.open('DELETE', phpGWLink('/index.php', {menuaction : 'admin.bo_custom_fields.delete_field', id : id}), true);
	xhr.onreadystatechange = function()
	{
		if ( xhr.readyState == 4 )
		{
			if ( xhr.status == 200 )
			{
				var target = document.getElementById('row_' + id);
				target.parentNode.removeChild(target);
			}
			else
			{
				alert(JSON.parse(xhr.responseText));
			}
			dialogCancel('remove');
		}
	}
	xhr.send();

}

function doSearch(search, listElmName, listTagName, idPrefix, col )
{
	var elms = document.getElementById(listElmName).getElementsByTagName(listTagName);
	
	if ( search.value.length == 0 )
	{
		showAll(elms);
	}
	
	if ( isNaN( parseInt(search.value) ) )
	{
		searchTerm = search.value.replace('/', '\/').replace('\\', '\\\\');
		var regex = new RegExp(searchTerm);
		for ( var i = 0; i < elms.length; ++i )
		{
			if ( elms[i].childNodes[col].innerHTML.match(regex) )
			{
				elms[i].style.display = 'table-row';
			}
			else
			{
				elms[i].style.display = 'none';
			}
		}
	}
	else
	{	
		for ( var i = 0; i < elms.length; ++i )
		{
			if ( elms[i].id != idPrefix + search.value )
			{
				elms[i].style.display = 'none';
			}
			else
			{
				elms[i].style.display = 'table-row';
			}
		}
	}
}

function highlight(id)
{
	if ( id == highlighted)
	{
		return true; // nothing to do here as it alread highlighted
	}
	
	var target = document.getElementById('row_' + id);
	
	if ( highlighted > 0 )
	{
		removeClassName(document.getElementById('row_' + highlighted), 'highlight');
	}
	target.className = 'highlight ' + target.className;//this hack prevents the next match row overriding the highlight
	highlighted = id;
}

function removeField()
{
	if ( highlighted == 0 )
	{
		return false; //nothing to see here folks keep moving
	}
	
	var xhr = new XMLHttpRequest();
	xhr.open('GET', phpGWLink('/index.php', {menuaction : 'admin.bo_custom_fields.get_field', id : highlighted}), true);
	xhr.onreadystatechange = function()
	{
		if ( xhr.readyState == 4 )
		{
			if ( xhr.status == 200 )
			{
				field =  JSON.parse(xhr.responseText);
				
				document.getElementById('remove_id').value = field['id'];
				document.getElementById('remove_field_id').innerHTML = field['id'];
				document.getElementById('remove_appname').innerHTML = ( field['appname'].length ? field['appname'] : 'global');
				document.getElementById('remove_name').innerHTML = field['name'];
				
				document.getElementById('modal_bg').style.display = 'block';
				var dialog = document.getElementById('fields_dialog_remove');
				dialog.style.visibility = 'hidden';//this is done to make it appear smoother on firefox
				dialog.style.display = 'block';
				centerDialog(dialog);
				dialog.style.visibility = 'visible';
				document.getElementById('remove_yes').focus();
			}
			else
			{
				alert(JSON.parse(xhr.responseText));
			}
		}
	}
	xhr.send();
}

function resetDialog(dialogName)
{
	var dialog = document.getElementById('fields_dialog_' + dialogName);
	var elms = dialog.getElementsByTagName('input');
	for ( i = 0; i < elms.length; ++i )
	{
		elms[i].value = '';
	}

	elms = dialog.getElementsByTagName('select');
	for ( i = 0; i < elms.length; ++i )
	{
		elms[i].selectedIndex = 0;
	}
}

function showAll(targetElms)
{
	for ( var i = 0; i < targetElms.length; ++i )
	{
		targetElms[i].style.display = 'table-row';
	}
}

/**
* Handles add button being clicked
*/
function viewField()
{
	if ( highlighted == 0 )
	{
		return false; //nothing to see here folks keep moving
	}
	
	var xhr = new XMLHttpRequest();
	xhr.open('GET', phpGWLink('/index.php', {menuaction : 'admin.bo_custom_fields.get_field', id : highlighted}), true);
	xhr.onreadystatechange = function()
	{
		if ( xhr.readyState == 4 )
		{
			if ( xhr.status == 200 )
			{
				field =  JSON.parse(xhr.responseText);
				
				document.getElementById('view_id').innerHTML = field['id'];
				document.getElementById('view_appname').innerHTML = ( field['appname'].length ? field['appname'] : 'global');
				document.getElementById('view_name').innerHTML = field['name'];
				document.getElementById('view_label').innerHTML = field['label'];
				document.getElementById('view_type').innerHTML = field['type_descr'];
				
				if ( field['type_id'] < 4 )
				{
					document.getElementById('view_list').style.display = 'none';
				}
				else
				{
					for ( var i = 0; i < field['values'].length; ++i )
					{
						
					}
				}

				document.getElementById('modal_bg').style.display = 'block';
				var dialog = document.getElementById('fields_dialog_view');
				dialog.style.visibility = 'hidden';//this is done to make it appear smoother on firefox
				dialog.style.display = 'block';
				centerDialog(dialog);
				dialog.style.visibility = 'visible';
			}
			else
			{
				alert(JSON.parse(xhr.responseText));
			}
		}
	}
	xhr.send();
}
