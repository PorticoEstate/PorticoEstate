<!-- BEGIN list -->
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
	var phpinfo;

	function openwindow(url)
	{
		if (phpinfo)
		{
			if (phpinfo.closed)
			{
				phpinfo.stop;
				phpinfo.close;
			}
		}
		phpinfo = window.open(url, "phpinfoWindow","width=700,height=600,location=no,menubar=no,directories=no,toolbar=no,scrollbars=yes,resizable=yes,status=no");
		if (phpinfo.opener == null)
		{
			phpinfo.opener = window;
		}
	}
</SCRIPT>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
 {rows}
</table>
<!-- END list -->

<!-- BEGIN app_row -->
 <tr height="60" bgcolor="{icon_backcolor}">
  <td width="5%" align="center" valign="middle" background="../phpgwapi/templates/idots/images/panel.png"><img src="{app_icon}" alt="[ {app_name} ]"> <a name="{a_name}"></a></td>
  <td width="95%" valign="middle" background="../phpgwapi/templates/idots/images/panel.png"><strong>&nbsp;&nbsp;{app_name}</strong></td>
 </tr>
<!-- END app_row -->

<!-- BEGIN app_row_noicon -->
 <tr bgcolor="{icon_backcolor}">
  <td colspan="2" width="95%" valign="middle"><strong>&nbsp;&nbsp;{app_name}</strong> <a name="{a_name}"></a></td>
 </tr>
<!-- END app_row_noicon -->

<!-- BEGIN link_row -->
 <tr>
  <td colspan="2">&nbsp;&#8226;&nbsp;<a href="{pref_link}">{pref_text}</a></td>
 </tr>
<!-- END link_row -->

<!-- BEGIN spacer_row -->
 <tr>
  <td colspan="2">&nbsp;</td>
 </tr>
<!-- END spacer_row -->
