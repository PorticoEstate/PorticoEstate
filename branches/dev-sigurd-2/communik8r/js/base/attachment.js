//attachment handling code
function showAttachment(strMsgNo, strPartNo, bInline)
{
	strCssClass = 'typetext';
	if ( !bInline )
	{
		strCssClass = 'typeother';
	}

	if ( !(oElm = document.getElementById('iframe_' + strPartNo) ) )
	{
		var oIFrame = document.createElement('IFRAME');
		oIFrame.id = 'iframe_' + strPartNo;
		oIFrame.style.display = 'block';
		oIFrame.className = strCssClass;
		oIFrame.src = oApplication.strBaseURL + 'email/' + oApplication.strMsgID2URLparts(strMsgNo) + '/' + strPartNo + '?' + oApplication.strGET;
		document.getElementById('part_' + strPartNo).appendChild(oIFrame);
	}
	else
	{
		if ( bInline )
		{
			oElm.parentNode.removeChild(oElm);
		}
		else
		{
			oElm.style.display = ( oElm.style.display == 'block' ? 'none' : 'block');
		}
	}
}
