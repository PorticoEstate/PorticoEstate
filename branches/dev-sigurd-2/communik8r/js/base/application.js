/**
 * ====================================================================
 * About
 * ====================================================================
 * Communik8r Application wide functions
 * @version 0.9.17.500
 * @author: Dave Hall skwashd at phpgroupware.org
 *
 * ====================================================================
 * Licence
 * ====================================================================
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 or
 * the GNU Lesser General Public License version 2.1 as published by
 * the Free Software Foundation (your choice of the two).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License or GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * or GNU Lesser General Public License along with this program; if not,
 * write to the Free Software Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 * or visit http://www.gnu.org
 *
 */

 /**
 * Application wide functions
 */
function Application()
{
	/**
	* The base URL for communik8r
	*/
	this.strBaseURL = strBaseURL;
	this.strAppURL = strAppURL;

	/**
	* Currently selected message - tracking used for reply/forward
	*/
	this.strCurrentMessage = '';

	/**
	* The GET arguments used when script was called
	*/
	this.strGET = location.search;

	/**
	* @var int iLoading Count the number of times loading has been called
	*/
	this.iLoading = 0;
}

/**
* Open a window for composing a message
*/
Application.prototype.compose = function(strRecipient)
{
	this._openComposeWin('new/email', strRecipient);
}

/**
* Ask user if they want to close the window
*
* @param String the confirmation message to display to user
*/
Application.prototype.confirmClose = function(strMsg)
{
	if ( strMsg && confirm(strMsg) )
	{
		window.close();
	}
}

/**
* Delete a message
* @internal delete is a reserved word in js :(
*/
Application.prototype.delete_msg = function()
{
	if ( !this.strCurrentMessage )
	{
		return false; //no message seleted, so none to delete
	}
	
	var strUrl = this.strBaseURL + 'email/'
		+ this.strMsgID2URLparts(this.strCurrentMessage.substring(4, this.strCurrentMessage.length) ) 
		+ this.strGET;
	
	var oRequest = new XMLHttpRequest();
	oRequest.open('DELETE', strUrl, false);
	oRequest.async = false; //if this one goes asycnit fucks up, why? nfi
	oRequest.send(null);
	if ( oRequest.status == 200 )
	{
		oSummary.delete_msg(oApplication.strCurrentMessage);
	}
	else
	{
		alert('deleting ' + this.strCurrentMessage + ': FAILED, sorry');
	}
}

/**
* Show the Account editing window
*
* @param String iAcctID the account ID of the account to edit
* @returns String the window name, empty string on failure
*/
Application.prototype.editAccount = function(strAcctID)
{
	var strWinName = 'communik8r_edit_account' + strAcctID;
	var strWinArgs = 'toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=0,height=600,width=800';
alert(this.strBaseURL + '&section=accounts&action=' + strAcctID  + '&type=email');
	if ( window.open(this.strBaseURL + '&section=accounts&action=' + strAcctID  + '&type=email', strWinName, strWinArgs) ) //&type is for new accounts
	{
		return strWinName;
	}
	return '';	
}

/**
* Create a new windows for replying to a message
*/
Application.prototype.forward = function()
{
	this._openComposeWin('forward/email/' + this.strCurrentMessage);
}

/**
* Hide the "Loading" message
*/
Application.prototype.hideLoading = function()
{
	this.iLoading--;
	if ( !this.iLoading )
	{
		document.getElementById('msg_loading').style.display = 'none';
	}
}

/**
* Convert a "<type>_<acct_id>_<mailbox/folder name>" to URL components
*
* @access private
* @param String strID folder/mailbox id
* @returns array components [0 - account id, 1 - mailbox/folder name]
*/
Application.prototype.id2ListInfo = function(strID)
{
	var arFolderInfo = strID.split('_');
	var strTmp = '';
	if( arFolderInfo.length > 3)
	{
		for(i = 2; i > arFolderInfo.length; i++)
		{
			strTmp += arFolderInfo[i];
		}
		return [arFolderInfo[0], arFolderInfo[1], strTmp];
	}
	else
	{
		return [arFolderInfo[0], arFolderInfo[1], arFolderInfo[2]];
	}
}

/**
* Open a window with content to allow user to create a new message
*/
Application.prototype.new_message = function()
{
	return this._openComposeWin('new/email');
}

/**
* Print the current message
*/
Application.prototype.print = function()
{
	window.print();
}

/**
* Refresh the current view
* @internal TODO Make more granular
*/
Application.prototype.refresh = function()
{
	window.location.reload();
}

/**
* Create popup for replying to a message
*/
Application.prototype.reply = function()
{
	this._openComposeWin('reply/email/' + this.strCurrentMessage);
}

/**
* Create popup for replying to a message
*/
Application.prototype.reply_to_all = function()
{
	this._openComposeWin('reply_to_all/email/' + this.strCurrentMessage);
}

/**
* Show the help window
*
* @param String strSection the target section for the manual
* @returns String the window name, empty string on failure
*/
Application.prototype.showHelp = function(strSection)
{
	if ( !strSection )
	{
		strSection = 'top';
	}

	var strWinName = 'communik8r_help';
	var strWinArgs = 'toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=0,height=600,width=800';

	if ( window.open(this.strBaseURL + '&section=help'+ '#' + strSection, strWinName, strWinArgs) )
	{
		return strWinName;
	}
	return '';	
}

/**
* Show the "Loading" message
*/
Application.prototype.showLoading = function()
{
	this.iLoading++;
	document.getElementById('msg_loading').style.display = 'block';
}

/**
* Show the Settings window
*
* @returns String the window name, empty string on failure
*/
Application.prototype.showSettings = function()
{
	var strWinName = 'communik8r_prefs';
	var strWinArgs = 'toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=0,height=600,width=800';

//	if ( window.open(this.strBaseURL + 'settings' + this.strGET, strWinName, strWinArgs) )
	if ( window.open(this.strBaseURL + '&section=settings', strWinName, strWinArgs) )
	{
		return strWinName;
	}
	return '';
}

/**
* Convert a message ID string ({iAcct}_{iMailbox}_{iDbPk}) to a URL fragment ({iAcct}/{iMailbox}/{iDbPk})
*
* @param string strMsgNo the message ID string
* @returns string URL fragment
*/
Application.prototype.strMsgID2URLparts = function(strMsgNo)
{
//alert('strMsgID2URLparts: ' + strMsgNo);
	if ( strMsgNo != '' && strMsgNo.indexOf('_') )
	{
		var arIDparts = strMsgNo.split('_');
		return 'acct_id=' + arIDparts[0] + '&mbox_name=' + arIDparts[1] + '&msg_id=' + arIDparts[2];
	}
	return '';
}

/**
* Update what buttons are active
*/
Application.prototype.updateButtons = function (strCurMsg)
{
	this.strCurrentMessage = strCurMsg;
	if ( strCurMsg != '')
	{
		oButtons.enableAll();
	}
	else
	{
		oButtons.disableAll();
	}

}

Application.prototype._openComposeWin = function(strURL, strTo)
{
	oDate = new Date();
	var strWinName = 'communik8r_' + (oDate.getMilliseconds() * (oDate.getMinutes() / ( oDate.getFullYear() / oDate.getHours() ) ) ); //FIXME Need to grab message number from server!
	var strWinArgs = 'toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=0,height=600,width=800';
	if ( window.open(this.strBaseURL + strURL + this.strGET + ( strTo ? '&to=' + strTo : ''), strWinName, strWinArgs) )
	{
		return strWinName;
	}
	return '';
}
