/**
* phpGroupWare Date Toggler
*
* @internal Taken from news_admin
* @author Michael Totschnig?
*/
function toggle()
{
	//return false;
	myspan = document.getElementById('visible_until')
	if (document.getElementById('from').value == '0.5')
	{
		myspan.style.display = 'block';
	}
	else
	{
		myspan.style.display = 'none';
	}
	myspan = document.getElementById('end');
	if (document.getElementById('until').value == '0.5')
	{
		myspan.style.display = 'block';
	}
	else
	{
		myspan.style.display = 'none';
	}
}
