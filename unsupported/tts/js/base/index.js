/**
* TTS index page functions
*
* Written by and Copyright 2006 Dave Hall
*/

var oTabs;
var sortOpen;
var sortOverdue;

YAHOO.namespace('example.container');
// What to do once the page has loaded
function ttsIndexOnLoad()
{
	oTabs = new Tabs(2,'activetab','inactivetab','tab','tabcontent');
	oTabs.display(1);
	
	/*
	sortOpen =  new SortableTable(document.getElementById('tickets_open'), 
			['None', 'Number', 'CaseInsensitiveString', 'Date', 'CaseInsensitiveString', 'CaseInsensitiveString', 'CaseInsensitiveString', 'CaseInsensitiveString']);
	
	sortOverdue =  new SortableTable(document.getElementById('tickets_overdue'),
			['None', 'Number', 'CaseInsensitiveString', 'Date', 'CaseInsensitiveString', 'CaseInsensitiveString', 'CaseInsensitiveString', 'CaseInsensitiveString']);
	*/

	var handleCancel = function()
	{
		dlg.cancel();
	}
	
	var handleSubmit = function()
	{
		dlg.submit();
	}

	dlg = new YAHOO.widget.Dialog(document.getElementById('tts_goto_dialog'), { modal:true, visible:false, width:"350px", fixedcenter:true, constraintoviewport:true, draggable:true });
	
	var listeners = new YAHOO.util.KeyListener(document, { keys : 27 }, {fn:handleCancel,scope:YAHOO.example.container.dlg,correctScope:true} );
	
	dlg.cfg.queueProperty('postmethod','form');

	dlg.cfg.queueProperty("keylisteners", listeners);
	
	dlg.cfg.queueProperty("buttons", [ { text : 'Cancel', handler : handleCancel },
										{ text: 'Go', handler : handleSubmit, isDefault : true } ]);
	
	dlg.render();
}


function goToPopup()
{
	dlg.show();
	/*
	var ticketID = prompt(lang['ticket_no'] + ' :');
	if ( ticketID == parseInt(ticketID) )
	{
		window.location = phpGWLink('/index.php', { menuaction : 'tts.uitts.view', id : ticketID } );
		return false;
	}
	else if ( ticketID != null && ticketID != '' && ticketID != false )
	{
		alert(lang['invalid']);
		goToPopup();
	}
	else
	{
		return false;
	}
	*/
}
