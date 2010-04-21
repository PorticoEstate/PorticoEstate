/**
 * ====================================================================
 * About
 * ====================================================================
 * Communik8r Accounts Pane Handler
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
function Accounts(strDiv)
{
	this.bInitRun = false;
	this.oDiv = document.getElementById(strDiv);
	this.oXSLDoc = '';
	this.oXMLDoc = null;
	this.strCurrentElement = '';
	this.strDiv = strDiv;
	this.oTree = null;
	var self = this;

	this.init();
}

Accounts.prototype.init = function()
{
	this.load();	
}

/**
* Loads the account list
*/
Accounts.prototype.load = function()
{
	oApplication.showLoading();

	this.oTree = new dhtmlXTreeObject(this.oDiv, '100%', '100%', 0);
	this.oTree.setOnClickHandler(this.loadSummary);
	this.oTree.setOnOpenHandler(this.updateOpenState);
	this.oTree.setImagePath(oApplication.strAppURL + '/templates/base/images/');
	this.oTree.imageArray = new Array('folder-16x16.png', 'open-16x16.png', 'folder-16x16.png');
	this.oTree.setImageArrays('plus', 'sub-16x16.png', 'sub-16x16.png', 'sub-16x16.png', 'sub-16x16.png', 'sub-16x16.png');
	this.oTree.setImageArrays('minus', 'sub-open-16x16.png', 'sub-open-16x16.png', 'sub-open-16x16.png', 'sub-open-16x16.png', 'sub-open-16x16.png');
	this.oTree.lineArray = new Array('nosub-16x16.png', 'nosub-16x16.png', 'nosub-16x16.png', 'nosub-16x16.png', 'nosub-16x16.png');
//alert('Accounts.load: ' + oApplication.strBaseURL + '&section=accounts');
	this.oTree.loadXML(oApplication.strBaseURL + '&section=accounts');
	oApplication.hideLoading();
}

/**
* Load the summary for the selected account/mailbox
*
* @todo add handlers for other account types and the account itself
* @param String strTarget the target account/mailbox/folder
*/
Accounts.prototype.loadSummary = function (strTarget)
{
	this.strCurrentElement = strTarget; //oTarget.id;
	oSummary.setList(strTarget);
}

/**
* Update the "open state" for a branch of the tree
*
* @param String strID the branch ID
* @returns bool true on completetion
*/
Accounts.prototype.updateOpenState = function (strID, iState)
{
	if ( strID.split('_').length >= 3 )
	{
		var arListInfo = oApplication.id2ListInfo(strID);
	//	alert('updateOpenState: ' + oApplication.strBaseURL + '&section=email&acct_id=' + arListInfo[1] + '&mbox_name=' + arListInfo[2] + '&action=status&status=' + (iState > 0 ? 0 : 1));
	//	var strPUT = '<communik8:response><communik8r:status id="open">' + (iState > 0 ? 0 : 1) + '</communik8r:status></communik8:response>'; //lazy style
		var oXMLHTTP = new XMLHttpRequest();
//		oXMLHTTP.open('PUT', oApplication.strBaseURL + oApplication.id2ListInfo(strID).join('/') + '/status'  + oApplication.strGET, false);
		oXMLHTTP.open('GET', oApplication.strBaseURL + '&section=email&acct_id=' + arListInfo[1] + '&mbox_name=' + arListInfo[2] + '&action=status&status=' + (iState > 0 ? 0 : 1), false);
		oXMLHTTP.async = true;
	//	oXMLHTTP.send(strPUT);
		oXMLHTTP.send(null);
		if ( oXMLHTTP.status != 200 )
		{
			alert('Unable to fetch mailboxes');
			return false;
		}
	}
	return true;
}
