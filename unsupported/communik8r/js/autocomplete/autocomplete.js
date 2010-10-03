//This relies on Sarissa
//This code is GPL
// Copyright Dave Hall
function autoComplete(strTextBox, strLookupURL)
{
	this.oText = document.getElementById(strTextBox);
	this.strACid = strTextBox + '_auto_complete';
	this.oDiv = null;
	this.strLookupURL = strLookupURL;
	this.oXMLDoc = null;
	var oThis = this;
	
	this.getLeft = function (element)
	{
		var offset = 0;
		while (element)
		{
			offset += element.offsetLeft;
			element = element.offsetParent;
		}
		return offset;
	}

	this.getTop = function(element)
	{
		var offset = 0;
		while (element)
		{
			offset += element.offsetTop;
			element = element.offsetParent;
		}
		return offset;
	}
	
	this.onTextBlur = function()
	{
		oThis.onblur();
	}

	this.onblur = function()
	{
		oThis.oDiv.style.visibility = "hidden";
	}

	this.onTextChange = function()
	{
		oThis.onchange();
	}

	this.onDivMouseDown = function(e)
	{
		if( !e ) //Fix Broken IE
		{
			var e = window.event;
		}

		if( e.target )
		{
			oTarget = e.target;
		}
		else if( e.srcElement )
		{
			oTarget = e.srcElement;
		}

		var strVal = '';
		if ( typeof(oTarget.textContent) != 'undefined') // Gecko / DOM3
		{
			strVal = oTarget.textContent;
		}
		else if ( typeof(element.innerText) != 'undefined')// M$IE
		{
			strVal = element.innerText;
		}
		
		var arTmpText = oThis.oText.value.split(';');
		arTmpText[arTmpText.length - 1] = strVal;
		oThis.oText.value = arTmpText.join(',') + ';';

		oThis.oText.focus();
	}

	this.onDivMouseOver = function()
	{
		oThis.className = "autocompleteHighlight";
	}

	this.onDivMouseOut = function()
	{
		oThis.className = "autocomplete";
	}

	this.onchange = function()
	{
		var arSearch = oThis.oText.value.split(';');
		var strSearch = arSearch[(arSearch.length - 1)];

		if ( strSearch.length <= 3 )
		{
			return;
		}
		
		var strURL = strLookupURL.replace(/__VALUE__/, strSearch);

		oThis.oXMLDoc = Sarissa.getDomDocument();
		oThis.oXMLDoc.onreadystatechange = oThis.update;
		oThis.oXMLDoc.load(strURL);
	}

	this.update = function()
	{
		if ( oThis.oXMLDoc.readyState == 4)
		{
			if ( !oThis.oDiv )
			{
				oThis.oDiv = document.createElement('div');
				oThis.oDiv.className = 'autocomplete';
				oThis.oDiv.style.position = 'absolute';
				oThis.oDiv.id = oThis.strACid;
				document.body.appendChild(oThis.oDiv);
				oThis.oDiv.style.top = (oThis.getTop(oThis.oText) + oThis.oText.offsetHeight) + 'px';
				oThis.oDiv.style.left = oThis.getLeft(oThis.oText) + 'px';
				oThis.oDiv.style.width = oThis.oText.offsetWidth + 'px';
			}

			//alert( Sarissa.serialize(oThis.oXMLDoc) );
			var arContacts = null;
			if ( !oThis.oXMLDoc.getElementsByTagNameNS )
			{
				arContacts = oThis.oXMLDoc.getElementsByTagName('contact');
			}
			else
			{
				arContacts = oThis.oXMLDoc.getElementsByTagNameNS('http://dtds.phpgroupware.org/communik8r.dtd', 'contact');
			}
			//alert('Found ' + arContacts.length + ' contacts');

			if ( arContacts.length > 0 )
			{

				while ( oThis.oDiv.hasChildNodes() )
				{
					oThis.oDiv.removeChild(oThis.oDiv.firstChild);
				}

				for ( var i = 0; i < arContacts.length; i++ )
				{
					var oDiv = document.createElement('div');
					oThis.oDiv.appendChild(oDiv);
					oDiv.innerHTML = arContacts[i].childNodes[0].nodeValue;
					oDiv.onmousedown = oThis.onDivMouseDown;
					oDiv.onmouseover = oThis.onDivMouseOver;
					oDiv.onmouseout = oThis.onDivMouseOut;
				}
				oThis.oDiv.style.visibility = 'visible';
			}
			else
			{
				oThis.oDiv.innerHTML = '';
				oThis.oDiv.style.visibility = 'hidden';
			}
		}
	}
	this.oText.onkeyup = oThis.onTextChange;
	this.oText.onblur = oThis.onTextBlur;
}
