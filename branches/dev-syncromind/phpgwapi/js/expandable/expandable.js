// Expandable lists
// Author's site is dead and he is uncontable
// General suggestion is that the code is public domain
// See http://web.archive.org/web/20041015055043/www.gazingus.org/ for original source

var CLASS_NAME="expandable";
var DEFAULT_DISPLAY="none";
var XMLNS="http://www.w3.org/1999/xhtml";
function initExpandableLists()
{
	if(!document.getElementsByTagName)
	{
		return;
	}

	switchNode=function(id)
	{
		var node=document.getElementById(id);
		if(node&&/^switch/.test(node.className))node.onclick();
	}

	actuate=function()
	{
		var sublist=this.parentNode.getElementsByTagName("ul")[0];
		if(sublist.style.display=="block")
		{
			sublist.style.display="none";
			this.firstChild.data="+";
			this.className="switch-off";
			this.title=this.title.replace("collapse","expand");
		}
		else
		{
			sublist.style.display="block";
			this.firstChild.data="-";
			this.className="switch-on";
			this.title=this.title.replace("expand","collapse");
		}

		return false;
	}

	if ( typeof(document.createElementNS == 'function') )
	{
		var template=document.createElementNS(XMLNS,"a");
	}
	else
	{
		var template=document.createElement("a");
	}

	template.appendChild(document.createTextNode(" "));
	
	var list,i=0;
	var pattern=new RegExp("(^| )"+CLASS_NAME+"( |$)");
	
	while((list=document.getElementsByTagName("ul")[i++]))
	{
		if(pattern.test(list.className)==false)
		{
			continue;
		}

		var item,j=0;
		while((item=list.getElementsByTagName("li")[j++]))
		{
			var sublist=item.getElementsByTagName("ul")[0];
			if(sublist==null)continue;
			var symbol;
			switch(sublist.style.display)
			{
				case "none":
					symbol="+";
					break;
				case "block":
					symbol="-";
					break;
				default:
					var display=DEFAULT_DISPLAY;
					if(sublist.currentStyle)
					{
						display=sublist.currentStyle.display;
					}
					else if(document.defaultView&&document.defaultView.getComputedStyle&&document.defaultView.getComputedStyle(sublist,""))
					{
						var view=document.defaultView;
						var computed=view.getComputedStyle(sublist,"");
						display=computed.getPropertyValue("display");
					}

					symbol=(display=="none")?"+":"-";
					sublist.style.display=display||"block";
					break;
			}

			var actuator=template.cloneNode(true);
			var uid="switch"+i+"-"+j;
			actuator.id=uid;
			actuator.href="javascript:switchNode('"+uid+"')";
			actuator.className="switch-"+((symbol=="+")?"off":"on");
			actuator.title=((symbol=="+")?"expand":"collapse")+" list";
			actuator.firstChild.data=symbol;
			actuator.onclick=actuate;
			item.insertBefore(actuator,item.firstChild);
		}

	}
}
var oldhandler=window.onload;
window.onload=(typeof oldhandler=="function")?function()
{
	oldhandler();
	initExpandableLists();
}
:initExpandableLists;
