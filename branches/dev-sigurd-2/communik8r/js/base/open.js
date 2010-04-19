function openCommunik8r(strUrl)
{
	var strQuery = '';
	if(location.search.length)
	{
		var strQuery = location.search;
	}
	strWinArgs = 'toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=0,maximize=1';
	window.open(strUrl + strQuery, 'communik8r', strWinArgs);
}
