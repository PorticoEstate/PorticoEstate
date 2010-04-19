/*
* phpGroupWare communik8r compose JS
* Copyright (c) 2005 Dave Hall
* GPL applies
*/
//TODO OOP ME at some stage
window.onload = function()
{
	document.getElementById('msgbody').value = document.getElementById('msgbody').value.replace(/\n/g, '<br>');
	oApplication = new Application();
	var oFCKeditor = new FCKeditor('msgbody');
	oFCKeditor.BasePath = strBaseURL + '/js/fckeditor/';
	oFCKeditor.ReplaceTextarea();

	//This is done this way to stop firefox placing the button icons in wacky spots
	document.getElementById('button_undo').className = 'inactive';
	document.getElementById('button_undo').disabled = true;

	document.getElementById('button_redo').className = 'inactive';
	document.getElementById('button_redo').disabled = true;

	var strLookupURL = oApplication.strBaseURL 
			+ '/contacts/lookup'
			+ (oApplication.strGET ? (oApplication.strGET + '&') : '?')
			+ 'search=__VALUE__&comm_type=email';

	oACto = new autoComplete('to', strLookupURL);
	oACcc = new autoComplete('cc', strLookupURL);
	oACbcc = new autoComplete('bcc', strLookupURL);

	oSubject = document.getElementById('subject');
	if( oSubject.addEventListener )
	{
		oSubject.addEventListener('keyup', updateTitle, false);
	}
	else if( oSubject.attachEvent )
	{
		oSubject.attachEvent('keyup', updateTitle);
	}
	addListeners();
	setTimeout('keepAlive()', 600000);//10 mins
}

function buttonClicked(e)
{
	if( !e ) //IE
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
	
	if( oTarget.id
		&& oTarget.id.indexOf('button_') == 0 
		&& oTarget.className.indexOf('inactive') == -1 )
	{
		var strClicked = oTarget.id.substring(6);
		eval('exec' + strClicked + '();');
		e.cancelBubble = true;
	}
}

function addListeners()
{
	var oButtons = document.getElementById('buttons').childNodes;
	for( var i = 0; i < oButtons.length; i++ )
	{
		if ( oButtons.item(i).tagName.toLowerCase() != 'button')
		{
			continue;
		}
		
		if( oButtons.item(i).addEventListener )
		{
			oButtons.item(i).addEventListener('click', buttonClicked, false);
		}
		else if( oButtons.item(i).attachEvent )
		{
			oButtons.item(i).attachEvent('onclick', buttonClicked);
		}
	}
}

function updateTitle(e)
{
	if ( window.event )
	{
		e = window.event;
	}
	document.title = 'communik8r: ' + document.getElementById('subject').value;
	e.cancelBubble = true;
}

function exec_attach()
{
	strMsgID = document.getElementById('msg_id').value;
	strWinArgs = 'toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=0,height=250,width=450';
	window.open(oApplication.strBaseURL + '/attachments/' + strMsgID + oApplication.strGET + '&mode=full', 'attach' + strMsgID, strWinArgs);	
}

function exec_undo()
{
	execCmd('Undo');
}

function exec_redo()
{
	execCmd('Redo');
}

function exec_cut()
{
	execCmd('Cut');
}

function exec_copy()
{
	execCmd('Copy');
}

function exec_paste()
{
	execCmd('Paste');
}

function exec_find()
{
	execCmd('Find');
}

function exec_replace()
{
	execCmd('Replace');
}


function execCmd(strCmd)
{
	var oEditor = FCKeditorAPI.GetInstance('msgbody') ;
	return oEditor.Commands.GetCommand(strCmd).Execute() ;
}

function exec_send(strFormID)
{
	var oFCK = FCKeditorAPI.GetInstance('msgbody');

	var iAccount = document.getElementById('account').options[document.getElementById('account').selectedIndex].value;
	var strTo = document.getElementById('to').value;
	var strCC = document.getElementById('cc').value;
	var strBCC = document.getElementById('bcc').value;
	var strSubject = document.getElementById('subject').value;
	var strMsgBody = oFCK.GetXHTML(true);
	var strSignature = document.getElementById('signature_content').value.replace(/^\s+/g, "" ).replace( /\s+$/g, "");

	if( !strTo && !strCC )
	{
		window.alert('No receipients specified!');
		return;
	}

	var oXML = Sarissa.getDomDocument('http://dtds.phpgroupware.org/phpgw.dtd', 'phpgw');

	if ( !document.createElementNS ) // Yes IE is a fucked piece of shit!
	{
		var ophpGWResponse = oXML.createElement('phpgw:response');

		var ophpGWapiInfo = oXML.createElement('phpgwapi:info');
		ophpGWResponse.appendChild(ophpGWapiInfo);

		var oCommunik8rResponse = oXML.createElement('communik8r:response');
	}
	else
	{
		var ophpGWResponse = oXML.createElementNS('http://dtds.phpgroupware.org/phpgw.dtd', 
							'phpgw:response');

		var ophpGWapiInfo = oXML.createElementNS('http://dtds.phpgroupware.org/phpgwapi.dtd', 
							'phpgwapi:info');
		ophpGWResponse.appendChild(ophpGWapiInfo);

		var oCommunik8rResponse = oXML.createElementNS('http://dtds.phpgroupware.org/communik8r.dtd', 
								'communik8r:response');
	}

	var oCommunik8rMsg = oXML.createElement('communik8r:message');

	var oCommunik8rHeaders = oXML.createElement('communik8r:headers');

	var oHeaderFields;

	oHeaderFields = oXML.createElement('communik8r:message_account_id');
	oHeaderFields.appendChild(oXML.createTextNode(iAccount));
	oCommunik8rHeaders.appendChild(oHeaderFields);

	var strRcpts = strTo.split(',');
	for ( i = 0; i < strRcpts.length; i++)
	{
		strRcpts[i] = strRcpts[i].replace(/^\s*|\s*$/g,""); // .trim()
		if ( !strRcpts[i] )//skip empties
		{
			continue;
		}

		oHeaderFields = oXML.createElement('communik8r:message_to');
		oHeaderFields.appendChild( oXML.createCDATASection(strRcpts[i]) );
		oCommunik8rHeaders.appendChild(oHeaderFields);
	}

	var strRcpts = strCC.split(',');
	for ( i = 0; i < strRcpts.length; i++)
	{
		strRcpts[i] = strRcpts[i].replace(/^\s*|\s*$/g,""); // .trim()
		if ( !strRcpts[i] )//skip empties
		{
			continue;
		}
		oHeaderFields = oXML.createElement('communik8r:message_cc');
		oHeaderFields.appendChild( oXML.createCDATASection(strRcpts[i]) );
		oCommunik8rHeaders.appendChild(oHeaderFields);
	}

	var strRcpts = strBCC.split(',');
	for ( i = 0; i < strRcpts.length; i++)
	{
		strRcpts[i] = strRcpts[i].replace(/^\s*|\s*$/g,""); // .trim()
		if ( !strRcpts[i] )//skip empties
		{
			continue;
		}
		oHeaderFields = oXML.createElement('communik8r:message_bcc');
		oHeaderFields.appendChild( oXML.createCDATASection(strRcpts[i]) );
		oCommunik8rHeaders.appendChild(oHeaderFields);
	}

	oHeaderFields = oXML.createElement('communik8r:message_subject');
	oHeaderFields.setAttribute('id', 'subject');
	oSubject = oXML.createCDATASection(strSubject);
	oHeaderFields.appendChild(oSubject);
	oCommunik8rHeaders.appendChild(oHeaderFields);

	oCommunik8rMsg.appendChild(oCommunik8rHeaders);

	//little time saving hack here :)
	if ( strSignature.length )
	{
		strMsgBody += "<div id=\"signature\"><br />\n--<br />\n" + strSignature + '</div>';
	}

	var oMsgBody = oXML.createElement('communik8r:msgbody');
	oMsgBody.setAttribute('id', 'msgbody');
	oMsgBody.appendChild(oXML.createCDATASection(strMsgBody));
	oCommunik8rMsg.appendChild(oMsgBody);

	oCommunik8rResponse.appendChild(oCommunik8rMsg);

	ophpGWResponse.appendChild(oCommunik8rResponse);

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open('PUT', oApplication.strBaseURL + '/email/send/' + strMsgID + oApplication.strGET, false);
	//xmlhttp.async = false;
	xmlhttp.send( Sarissa.serialize(ophpGWResponse) );

	if ( xmlhttp.status == 200 )
	{
		window.close();
	}
	else
	{
		alert( xmlhttp.responseText );
	}
}

function keepAlive()
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open('GET', oApplication.strBaseURL + '/ping' + oApplication.strGET, false);
	xmlhttp.async = true;
	xmlhttp.send( null );
	setTimeout('keepAlive()', 600000);//10 mins
}

function refreshAttachments()
{
	function loadXML()
	{
		var oXSLTProc = new XSLTProcessor();
		oXSLTProc.importStylesheet(oXSLDoc);
		var oTarget = document.getElementById('attachments_list');
		Sarissa.updateContentFromURI(oApplication.strBaseURL + '/attachments/' + strMsgID + oApplication.strGET, 
					oTarget, 
					oXSLTProc);
	}
	oComposeWin.focus();
	var oXSLDoc = Sarissa.getDomDocument();
	oXSLDoc.async = true;
	oXSLDoc.onreadystatechange = loadXML()
	oXSLDoc.load(oApplication.strBaseURL + '/xsl/attach_list');
}
