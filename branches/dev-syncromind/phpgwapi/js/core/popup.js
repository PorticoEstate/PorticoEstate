// $Id: popup.js 15710 2005-02-07 15:32:06Z ceb $

function open_popup (url,ext_x,ext_y)
{
	leftpos = ((screen.width - ext_x)/2);
	toppos = ((screen.height - ext_y - 20)/2);

	win=window.open(url,"x","toolbar=no,location=no,explorer=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=yes,left="+leftpos+",top="+toppos+",width="+ext_x+",height="+ext_y);

	if ((navigator.appName == "Netscape" && parseInt(navigator.appVersion) >= 3) || (navigator.appName == "Microsoft Internet Explorer" && parseInt(navigator.appVersion) >= 4))
	{
		//win.moveTo((screen.width/2)-(ext_x/2),(screen.height/2)-(ext_y/3));
		win.focus();
	}
}

function checkall(type)
{
	alert("halo");

	for(var i=0;i<document.forms[0].length;++i)
	{
 		if(document.forms[0].elements[i].name == 'sichtbar[]')
		{
			alert("Elementname: " + document.forms[0].elements[i].name + ", Elementtyp: " + document.forms[0].elements[i].type);
		}
	}
}

var checkflag = "false";
function check()
{
	//alert("hal");
	//alert("hal" + document.forms[0].elements['sichtbar[]'].length);
	for(var i=0;i<document.forms[0].elements['sichtbar[]'].length;i++)
	alert(document.forms[0].elements['sichtbar[]',i].name);
	// document.forms[0].elements['sichtbar[]'].checked=true;
}
