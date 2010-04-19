/**
 * ====================================================================
 * About
 * ====================================================================
 * Communik8r Message Pane Handler
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
function Message(strDiv)
{
	this.oDiv = document.getElementById(strDiv);
	this.oXSLDoc;
	this.oXMLDoc;
	this.strMsgId;
	var self = this;
	
	this.init = function()
	{
		//do nothing for now
	}

	this.clicked = function(e)
	{
	}

	this._onUpdateNode = function()
	{
		self.oDiv.scrollTop = 0; //get message to top
		self.oDiv.innerHTML = autoLinks(self.oDiv.innerHTML);
		oApplication.hideLoading();
	}

	this.transform = function()
	{
		if ( self.oXSLDoc.readyState == 4 )
		{
			var oXSLTProc = new XSLTProcessor();
			oXSLTProc.importStylesheet(self.oXSLDoc);
			oApplication.showLoading();
			Sarissa.updateContentFromURI(self.getMsgURI(), self.oDiv, oXSLTProc, self._onUpdateNode);
		}
	}

	this.init();
}


Message.prototype.getMsgURI = function()
{
	var arTmp = this.strMsgId.split('_');
	if ( arTmp.length > 4 )
	{
		var arNew = [arTmp[0], arTmp[1], '', arTmp[3]];
		for ( var i = 2; i > (arTmp.length - 1); i++)
		{
			arNew[2] += arTmp[i].toString();
		}
		arTmp = arNew;
	}
	return oApplication.strBaseURL + arTmp.join('/') + oApplication.strGET;
}

Message.prototype.loadMessage = function(strMsgId)
{
	if ( strMsgId == '' )
	{
		this.oDiv.innerHTML = '&nbsp;'; //hack to blank preview panel when no message selected
		oApplication.updateButtons(''); //should disable them all
		return;
	}
	
	//oApplication.showLoading();
	oApplication.updateButtons(strMsgId);//should enable them all
	this.strMsgId = strMsgId;
	if ( typeof(this.oXSLDoc) == 'object')
	{
		this.transform();
	}
	else
	{
		this.oXSLDoc = Sarissa.getDomDocument();
		this.oXSLDoc.onreadystatechange = this.transform;
		this.oXSLDoc.load(oApplication.strBaseURL + 'xsl/message');
	}
}
