/** Written by & (c) Dave Hall 2006 **/
function updateManager()
{
	var manager = document.getElementById('group_manager');
	var curManagerVal = manager.options[manager.selectedIndex].value;
	while ( manager.childNodes.length )
	{
		manager.removeChild(manager.firstChild);
	}
	
	var users = document.getElementById('account_user');
	var userOption;
	for ( i=0; i < users.options.length; ++i )
	{
		if ( users.options[i].selected )
		{
			userOption = users.options[i].cloneNode(true);
			if ( userOption.value != curManagerVal )
			{
				userOption.selected = false;
			}
			manager.appendChild(userOption);
		}
	}
}
