/**
* TTS view screen functions
*
* Written by and Copyright 2006 Dave Hall
*/

var oTabs;
// What to do once the page has loaded
window.onload = function()
{
	oTabs = new Tabs(4,'activetab','inactivetab','tab','tabcontent');
	oTabs.display(1);
};
