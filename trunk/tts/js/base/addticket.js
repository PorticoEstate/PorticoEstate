/*
* Add Ticket JavaScript functions
* @author Dave Hall skwashd at phpgroupware org
* @copyright Copyright (c) 2006 Free Software Foundation Inc
*/

function updateGroup()
{
	var tGroup = document.getElementById('ticket_group');
	var tAssigned = document.getElementById('ticket_assignedto');
	
	tAssigned.disbaled = true; // until it is updated and valid again
	
	if ( tGroup.selectedIndex != 0 )
	{
		oParams = { 
					menuaction	: 'tts.uitts.get_users',
					group_id	: tGroup.options[tGroup.selectedIndex].value
				};

		req = new XMLHttpRequest();
		req.open('GET', phpGWLink('/index.php', oParams), true);
		req.onreadystatechange = function()
		{
			if ( req.readyState == 4 )
			{
				tAssigned.innerHTML = ''; //hack to clear the list
				var option;
				var users = eval(req.responseText);
				if ( users.length )
				{
					for ( var user_key in users )
					{
						option = document.createElement('option');
						option.value = users[user_key].account_id;
						option.appendChild(document.createTextNode(users[user_key].account_name) );
						tAssigned.appendChild(option);
					}
					tAssigned.disabled = false;
				}
			}
		}
		req.send(null);
	}
	else
	{
		tAssigned.disabled = true;
	}
}

function updateCats()
{
	var tCatTop = document.getElementById('ticket_cat_top');
	var tCategory = document.getElementById('ticket_category');
	
	tCategory.disbaled = true; // until it is updated and valid again
	
	if ( tCatTop.selectedIndex != 0 )
	{
		oParams = { 
					menuaction	: 'tts.uitts.get_cats',
					cat_id	: tCatTop.options[tCatTop.selectedIndex].value
				};

		req = new XMLHttpRequest();
		req.open('GET', phpGWLink('/index.php', oParams), true);
		req.onreadystatechange = function()
		{
			if ( req.readyState == 4 )
			{
				tCategory.innerHTML = ''; //hack to clear the list
				var option;
				var cats = eval(req.responseText);
				if ( cats.length )
				{
					for ( var cat_key in cats )
					{
						option = document.createElement('option');
						option.value = cats[cat_key].cat_id;
						option.appendChild(document.createTextNode(cats[cat_key].name) );
						tCategory.appendChild(option);
					}
					tCategory.disabled = false;
				}
			}
		}
		req.send(null);
	}
	else
	{
		tAssigned.disabled = true;
	}
}

function attachAllEvents()
{
	var tGroup = document.getElementById('ticket_group');
	if ( tGroup.selectedIndex != 0 )
	{
		updateGroup();
	}
	else
	{
		Evnt.addEventListener(tGroup, 'change', updateGroup, false);
	}
	
	var tCatTop = document.getElementById('ticket_cat_top');
	if ( tCatTop.selectedIndex != 0 )
	{
		updateCats();
	}
	else
	{
		Evnt.addEventListener(tCatTop, 'change', updateCats, false);
	}
}

Evnt.addEventListener(window, 'load', attachAllEvents, false);
