/**
* Generic Event Handler
*
* @link based on http://www.ditchnet.org/wp/2005/06/15/ajax-freakshow-drag-n-drop-events-2/
* @author
* @author Dave Hall skwashd at phpgroupware.org
*/
function Evnt(evt)
{
	this.evt = evt ? evt : window.event; 
	this.source = evt.target ? evt.target : evt.srcElement;
	this.x = evt.pageX ? evt.pageX : evt.clientX;
	this.y = evt.pageY ? evt.pageY : evt.clientY;
}

Evnt.prototype.consume = function ()
{
	if (this.evt.stopPropagation)
	{
		this.evt.stopPropagation();
		this.evt.preventDefault();
	}
	else if (this.evt.cancelBubble)
	{
		this.evt.cancelBubble = true;
		this.evt.returnValue  = false;
	}
}

Evnt.addEventListener = function (target,type,func,bubbles)
{
	if (document.addEventListener)
	{
		target.addEventListener(type,func,bubbles);
	}
	else if (document.attachEvent)
	{
		target.attachEvent("on"+type,func,bubbles);
	}
	else
	{
		target["on"+type] = func;
	}
}

Evnt.removeEventListener = function (target,type,func,bubbles) 
{
	if (document.removeEventListener)
	{
		target.removeEventListener(type,func,bubbles);
	}
	else if (document.detachEvent)
	{
		target.detachEvent("on"+type,func,bubbles);
	}
	else
	{
		target["on"+type] = null;
	}
}
