/**
* Generic sitemgr JS functions
*
* @author Dave Hall skwashd at phpgroupware.org
* @license GPL
*/

/**
* Emulate phpGW's link function
*
* @param String strURL target URL
* @param Object oArgs Query String args as associate array object
* @returns String URL
*/
function phpGWLink(strURL, oArgs)
{
	var arURLParts = strBaseURL.split('?');
	var strNewURL = arURLParts[0] + strURL + '?';

	if ( oArgs == null )
	{
		oArgs = new Object();
	}

	for (obj in oArgs)
	{
		strNewURL += obj + '=' + oArgs[obj] + '&';
	}
	strNewURL += arURLParts[1];
	return strNewURL;
}
		
/**
* Find the absolute horizontal position of an element
*
* @param Object obj HTML Element to find location of
* @returns int the horizontal position in pixels
*/
function findPosX(obj)
{
	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
	{
		curleft += obj.x;
	}
	return curleft;
}

/**
* Find the absolute vertical position of an element
*
* @param Object obj HTML Element to find location of
* @returns int the vertical position in pixels
*/
function findPosY(obj)
{
	var curtop = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop
			obj = obj.offsetParent;
		}
	}
	else if (obj.y)
	{
		curtop += obj.y;
	}
	return curtop;
}

/**
* Move a HTML Element
*/
function moveObj(strID, iStartX, iStartY, iEndX, iEndY, iSteps)
{
	var oElm = document.getElementById(strID);
	if ( iSteps == 0 )
	{
		oElm.parentNode.removeChild(oElm);
	}
	
	var iInterval = 42; // Math.round(1000/24);
	if ( oElm )
	{
		iX = iEndX + Math.round( ( (iStartX - iEndX) / 24) * iSteps);
		oElm.style.left = iX + 'px';
		
		iY = iEndY + Math.round( ( (iStartY - iEndY) / 24) * iSteps);
		oElm.style.top = iY + 'px';
		
		--iSteps;
		setTimeout('moveObj("' + strID + '", ' + iStartX + ', ' + iStartY + ', ' + iEndX + ', ' + iEndY + ', ' + iSteps + ')', iInterval);
	}
}

function addClassName(el, sClassName)
{
	var s = el.className;
	if ( !s )
	{
		s = '';
	}
	var p = s.split(' ');
	var l = p.length;
	for (var i = 0; i < l; i++)
	{
		if (p[i] == sClassName)
		{
			return;
		}
	}
	p[p.length] = sClassName;
	el.className = p.join(' ');

}

function removeClassName(el, sClassName)
{
	var s = el.className;
	var p = s.split(' ');
	var np = [];
	var l = p.length;
	var j = 0;
	for (var i = 0; i < l; i++)
	{
		if (p[i] != sClassName)
		{
			np[j++] = p[i];
		}
	}
	el.className = np.join(' ');
}
