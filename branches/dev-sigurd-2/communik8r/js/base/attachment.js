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
	alert(oApplication.strBaseURL + '&section=email&action=attachment&' + oApplication.strMsgID2URLparts(strMsgNo) + '&part=' + strPartNo);
		oIFrame.src = oApplication.strBaseURL + '&section=email&action=attachment&' + oApplication.strMsgID2URLparts(strMsgNo) + '&part=' + strPartNo;
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
