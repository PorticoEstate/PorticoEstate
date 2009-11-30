// TTS Search Javascript
// Written by Dave Hall
// (c) 2006 Dave Hall, All Rights Reserved

var arFields = [];


/**
* Create a date input field
*
* @todo make it work properly
* @param object oActive ???
* @param int iID the row number identifier
* @return object the input field
*/
function addDateValue(oActive, iID)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.id = 'value_' + iID;
	oInput.id = 'name_' + iID;
	return oInput;
}

/**
* Create a select option list with the available options for a date based search
*
* @param object oSelect the select tag to append the options to
*/
function addDateTypes(oSelect)
{
	var oOption = document.createElement('option');
	oOption.value = 'is';
	oOption.appendChild(document.createTextNode('is'));
	oSelect.appendChild(oOption);

	oOption = document.createElement('option');
	oOption.value = 'is_not';
	oOption.appendChild(document.createTextNode('is not'));
	oSelect.appendChild(oOption);

	oOption = document.createElement('option');
	oOption.value = 'before';
	oOption.appendChild(document.createTextNode('is before'));
	oSelect.appendChild(oOption);

	oOption = document.createElement('option');
	oOption.value = 'after';
	oOption.appendChild(document.createTextNode('is after'));
	oSelect.appendChild(oOption);
}

/**
* Add a list of available search fields
*
* @param string strSelected the currently selected item from the list
* @return object the new select option list
*/
function addFieldList(strSelected, iID)
{
	oSelect = document.createElement('select');
	oSelect.id = 'field_' + iID;
	oSelect.className = 'field';
	for ( var entry in arFields )
	{
		oOption = document.createElement('option');
		oOption.value = arFields[entry].field_name;
		oOption.appendChild(document.createTextNode(arFields[entry].descr));
		oOption.selected = arFields[entry].field_name == strSelected;
		oSelect.appendChild(oOption);
	}
	
	if ( strSelected == '' )
	{
		oSelect.options[0].selected = true;
	}

	oSelect.onchange = function(e)
	{
		if ( typeof(e) == 'undefined' )
		{
			e = window.event;
		}
		var source = e.target ? e.target : e.srcElement;
		var iID = source.id.substr(6); //strip the field_ to get the ID as an int
		for ( var i = (source.parentNode.childNodes.length - 1); i >= 0; --i )
		{
			if ( source.parentNode.childNodes[i].id == source.id )
			{
				continue;
			}
			source.parentNode.removeChild(source.parentNode.childNodes[i]);
		}
		source.parentNode.appendChild(addFieldLookup(arFields[source.options.selectedIndex], iID) );
		source.parentNode.appendChild(addSearchValue(arFields[source.options.selectedIndex], iID) );
		source.parentNode.appendChild(addRemoveButton(iID) );
	}
	return oSelect;
}

function addNewCriteria(iID, strFieldSelected, strLookupSelected, strValue)
{
	var oDiv;
	var bAppend = true;
	if ( !iID )
	{
		iID = document.getElementById('tts_search_adv_criteria').childNodes.length;
		oDiv = document.createElement('div');
		oDiv.id = 'entry_' + iID;
	}
	else
	{
		oDiv = document.getElementById('entry_' + iID);
		bAppend = false;
		while ( oDiv.hasChild )
		{
			oDiv.removeChild(oDiv.firstChild);
		}
	}

	var iFieldSelected = 0;
	if ( strFieldSelected )
	{
		iFieldSelected = getFieldSelection(strFieldSelected);
	}

	oDiv.className = 'row_' + (iID % 2 ? 'on' : 'off');
	oDiv.appendChild(addFieldList(strFieldSelected, iID) );
	//document.getElementsByTagName('body').item(0).appendChild(document.createTextNode(JSON.stringify(arFields)));
	oDiv.appendChild(addFieldLookup(arFields[0], iID ) );
	oDiv.appendChild(addSearchValue(arFields[0], iID ) );
	oDiv.appendChild(addRemoveButton(iID));
	
	if ( bAppend )
	{
		document.getElementById('tts_search_adv_criteria').appendChild(oDiv);
	}
}

function addRemoveButton(iID)
{
	var oButton = document.createElement('button');
	oButton.type = 'button';
	oButton.id = 'removebutton_' + iID;
	oButton.appendChild(document.createTextNode("-\nRemove"));
	oButton.onclick = removeEntry;
	oButton.style.position = 'relative';
	oButton.style.right = '-30px';
	return oButton;
}

function addSearchValue(oActive, iID)
{
	switch ( oActive.type )
	{
		case 'INT':
		case 'DOUBLE':
			return addNumericValue(oActive, iID);
		case 'DATE':
			return addDateValue(oActive, iID);
		case 'LOOKUP':
			return addLookupValue(oActive, iID);
		case 'TEXT':
		default:
			return addTextValue(oActive, iID);
	}
}

function addLookupValue(oActive, iID)
{
	var oSelect = document.createElement('select');
	oSelect.id = 'value_' + iID;
	var oOption;
	for ( entry in oActive.lookup_values )
	{
		oOption = document.createElement('option');
		oOption.value = oActive.lookup_values[entry].id;
		oOption.appendChild(document.createTextNode(oActive.lookup_values[entry].value));
		oSelect.appendChild(oOption);
	}
	return oSelect;
}

function addNumericValue(oActive, strID)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.id = 'value_' + strID;
	oInput.value = 0;
	return oInput;
}

function addTextValue(oActive, strID)
{
	var oInput = document.createElement('input');
	oInput.type = 'text';
	oInput.id = 'value_' + strID;
	return oInput;
}

function addFieldLookup(oActive, iID)
{
	var oSelect = document.createElement('select');
	oSelect.id = 'stype_' + iID;
	oSelect.className = 'stype';
	switch ( oActive.type )
	{
		case 'INT':
		case 'DOUBLE':
			addNumericTypes(oSelect);
			break;

		case 'DATE':
			addDateTypes(oSelect);
			break;

		case 'LOOKUP':
			addLookupTypes(oSelect);
			break;
		
		case 'TEXT':
		default:
			addTextTypes(oSelect);
	}
	return oSelect;
}

function addLookupTypes(oSelect)
{
	var oOption = document.createElement('option');
	oOption.value = 'is';
	oOption.appendChild(document.createTextNode('is'));
	oSelect.appendChild(oOption);

	oOption = document.createElement('option');
	oOption.value = 'is_not';
	oOption.appendChild(document.createTextNode('is not'));
	oSelect.appendChild(oOption);
}

function addNumericTypes(oSelect)
{
	var oOption = document.createElement('option');
	oOption.value = 'equals';
	oOption.appendChild(document.createTextNode('equals'));
	oSelect.appendChild(oOption);

	oOption = document.createElement('option');
	oOption.value = 'not_equals';
	oOption.appendChild(document.createTextNode('not equals'));
	oSelect.appendChild(oOption);

	oOption = document.createElement('option');
	oOption.value = 'greater_than';
	oOption.appendChild(document.createTextNode('greater than'));
	oSelect.appendChild(oOption);

	oOption = document.createElement('option');
	oOption.value = 'less_than';
	oOption.appendChild(document.createTextNode('less than'));
	oSelect.appendChild(oOption);
}

function addTextTypes(oSelect)
{
	var oOption = document.createElement('option');
	oOption.value = 'is';
	oOption.appendChild(document.createTextNode('is'));
	oSelect.appendChild(oOption);

	oOption = document.createElement('option');
	oOption.value = 'is_not';
	oOption.appendChild(document.createTextNode('is not'));
	oSelect.appendChild(oOption);

	oOption = document.createElement('option');
	oOption.value = 'contains';
	oOption.appendChild(document.createTextNode('contains'));
	oSelect.appendChild(oOption);

	oOption = document.createElement('option');
	oOption.value = 'not_contains';
	oOption.appendChild(document.createTextNode('does not contain'));
	oSelect.appendChild(oOption);

	oOption = document.createElement('option');
	oOption.value = 'starts';
	oOption.appendChild(document.createTextNode('starts with'));
	oSelect.appendChild(oOption);

	oOption = document.createElement('option');
	oOption.value = 'not_starts';
	oOption.appendChild(document.createTextNode('does not start with'));
	oSelect.appendChild(oOption);

	oOption = document.createElement('option');
	oOption.value = 'ends';
	oOption.appendChild(document.createTextNode('ends with'));
	oSelect.appendChild(oOption);

	oOption = document.createElement('option');
	oOption.value = 'not_ends';
	oOption.appendChild(document.createTextNode('does not end with'));
	oSelect.appendChild(oOption);
}

function displayLoading()
{
	var elmBody = document.getElementsByTagName('body').item(0);
	var elmDIV = document.createElement('div');
	elmDIV.id = 'loading';
	elmDIV.style.textAlign = 'right';
	elmDIV.appendChild(document.createTextNode('Loading ...'));
	elmBody.insertBefore(elmDIV, elmBody.firstChild);
}

/**
* Find the field object for the current field object
*
* return object the currently selected field object - null for not matched
*/
function getFieldSelection(strSelection)
{
	for ( var entry in arFields )
	{
		if ( arFields[entry].field_name == strSelection )
		{
			return entry;
		}
	}
	return null;
}

/**
* handle the onreadystate change event
*
* @param bool bAdd add a search entry
*/
function handleORSC(bAdd)
{
	if ( xhr.readyState != 4 )
	{
		return false; //ignore it
	}

	if ( xhr.status != 200 )
	{
		alert('ERROR');
		return false;
	}

	var responseData = eval(xhr.responseText);
	if ( typeof(responseData) != 'object'
		|| !responseData.length )
	{
		return false; // it is useless, so bail out
	}

	if ( !arFields.length ) //empty
	{
		arFields = responseData;
	}
	else
	{
		arFields.concat(responseData);
	}

	arFields = arFields.sort(sortFields);

	var arSelects = document.getElementById('tts_search_adv_criteria').getElementsByTagName('select');
	if ( !arSelects.length )
	{
		return false; //nothing to see here people, keep moving
	}

	var strSelected = '';
	var oOption;
	for ( var i = (arSelects.length - 1); i >= 0; --i )
	{
		if ( !arSelects[i].id.length 
			|| arSelects[i].id.substr(0, 9) != 'field_' )
		{
			continue;
		}

		if ( arSelects[i].options.selectedIndex != -1 )
		{
			strSelected = arSelects[i].options[arSelects[i].options.selectedIndex].value;
		}

		while ( arSelects[i].hasChild )
		{
			arSelects[i].removeChild(arSelects[i].firstChild);
		}

		arSelects[i].parentNode.appendChild(addFieldLookup(arFields[arSelects[i].options.selectedIndex], arSelects[i].id.substr(9) ) );
		arSelects[i].parentNode.appendChild(addSearchValue(arFields[arSelects[i].options.selectedIndex], arSelects[i].id.substr(9) ) );
	}
}

function hideLoading()
{
	var elmLoading = document.getElementById('loading');
	if ( typeof(elmLoading) != 'undefined' )
	{
		elmLoading.parentNode.removeChild(elmLoading);
	}
}

/**
* Load up the search field
*
* @param int iType the ticket type - 0 == base
* @param bool bAdd add a search entry
*/
function loadFields(iType, bAdd)
{
	xhr = new XMLHttpRequest();
	xhr.open('GET', phpGWLink('/index.php', {menuaction: 'tts.botts.get_search_fields', cat_id: iType, phpgw_return_as: 'json'}), true);
	xhr.onreadystatechange = function() { handleORSC(bAdd); };
	xhr.send('');
}

/**
* Remove button event handler
*
* @param object event object for the button click
*/
function removeEntry(e)
{
	if ( typeof(e) == 'undefined' )
	{
		e = window.event;
	}
	var source = source = e.target ? e.target : e.srcElement;
	source.parentNode.parentNode.removeChild(source.parentNode);
	renumberEntries();
}

/**
* Renumber the field list and associated divs after a delete - makes server side processing easier
*/
function renumberEntries()
{
	var oParent = document.getElementById('tts_search_adv_criteria');
	var iEntries = oParent.childNodes.length;
	for ( i = 0; i < iEntries; ++i )
	{
		if ( oParent.childNodes[i].id == 'entry_' + i )
		{
			continue; // nothing to do as it is in the right spot
		}

		oParent.childNodes[i].className = 'row_' + (i % 2 ? 'on' : 'off');

		oParent.childNodes[i].id = 'entry_' + i;

		var arID = [];
		for ( var j = (oParent.childNodes[i].childNodes.length - 1); j >= 0; --j )
		{
			arID = oParent.childNodes[i].childNodes[j].id.split('_');
			oParent.childNodes[i].childNodes[j].id = arID[0] + j;
			oParent.childNodes[i].childNodes[j].name = arID[0] + '[' + j + ']';
		}
	}
}

/**
* Sort the list the field list by description
*
* @internal callBack method for arFields.sort
*/
function sortFields(a, b)
{
	var x = a.descr.toLowerCase();
	var y = b.descr.toLowerCase();
	return (x < y) ? -1 : (x > y) ? 1 : 0;
}

// TODO Move me
function form2String()
{
	var strForm = '';
	
	var oRows = document.getElementById('tts_search_adv_criteria').childNodes;
	for ( i = oRows.length - 1; i >= 0; --i )
	{
		strForm += 'field[' + i + ']=' + encodeURI(document.getElementById('field_' + i).options[document.getElementById('field_' + i).selectedIndex].value) + '&'
				+ 'stype[' + i + ']=' + encodeURI(document.getElementById('stype_' + i).options[document.getElementById('stype_' + i).selectedIndex].value) + '&';
		if ( document.getElementById('value_' + i).tagName.toLowerCase == 'select' )
		{
			strForm += 'value[' + i + ']=' + encodeURI(document.getElementById('value_' + i).options[document.getElementById('value_' + i).selectedIndex].value);
		}
		else //must be input
		{
			strForm += 'value[' + i + ']=' + encodeURI(document.getElementById('value_' + i).value);
		}
		strForm += '&';
	}
	return strForm;
}

/**
* Submit the advanced form
*/
function submitAdvSearch()
{
	if ( !document.getElementById('tts_search_adv_criteria').childNodes.length )
	{
		return !alert('You must specify at least one field');
	}
	
	var strForm = form2String() + 'search_type=' + encodeURI(document.getElementById('tts_search_type').options[document.getElementById('tts_search_type').selectedIndex].value);

	window.location = phpGWLink('/index.php', {menuaction : 'tts.uitts.search'}) + '&' + strForm + '&search_mode=adv';
	return false;

	displayLoading();

	var xhr = new XMLHttpRequest();
	xhr.open('POST', phpGWLink('/index.php', {menuaction: 'tts.botts.search', phpgw_return_as: 'json'}), true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhr.setRequestHeader("Content-length", strPOST.length);
	xhr.onreadystatechange = function()
	{
		if ( xhr.readyState != 4 )
		{
			return false;
		}

		hideLoading();
		if ( xhr.status != 200 )
		{
			alert('ERROR');
			return false;
		}
		arEntries = eval(xhr.responseText);
		
		var elmTarget = document.getElementById('tts_search_results');
		elmTarget.innerHTML = '';

		alert('# == ' + arEntries.length);
		
		if ( !arEntries.length )
		{
			elmTarget.innerHTML = '<strong>Nothing found, try again</strong>';
			return false;
		}

	}
	xhr.send(strPOST);
	return false;
}
