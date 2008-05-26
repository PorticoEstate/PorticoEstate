<!-- BEGIN navbar -->
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
	var page;

	function openwindow(url)
	{
		if (page)
		{
			if (page.closed)
			{
				page.stop;
				page.close;
			}
		}
		page = window.open(url, "pageWindow","width=700,height=600,location=no,menubar=no,directories=no,toolbar=no,scrollbars=yes,resizable=yes,status=no");
		if (page.opener == null)
		{
			page.opener = window;
		}
	}
</SCRIPT>

<table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="{table_bg_color}">
<tr background="{img_root}/bg_filler.png">
<td background="{img_root}/bg_filler.png" align="left" valign="bottom"><a href="http://www.phpgroupware.org" target="_new"><img src="{img_root}/{logo}" border="0" alt="phpGroupWare"></a></td>
<td background="{img_root}/bg_filler.png" align="center" valign="bottom" width="100%"><!--<font color="{navbar_text}" size="-1">{user_info}</font>--></td>
<td background="{img_root}/bg_filler.png" align="right" valign="bottom" rowspan="2" nowrap><a href="{home_url}"><img src="{welcome_img}" border="0" alt="{home_text}"></a>{preferences_icon}<a href="{logout_url}"><img src="{logout_img}" border="0" alt="logout_text"></a><a href="{about_url}"><img src="{img_root}/help.png" border="0" alt="{about_text}"></a></td></tr>
<tr background="{img_root}/bg_filler.png">
<td align="center" width="100%" valign="bottom" colspan="2"><img src="{img_root}/greybar.jpg" height="6"  width="100%" alt="bar"></td></tr>
</table>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
 <tr valign="top">
  <td background="{img_root}/navbar_filler.jpg" align="left">
   {applications}
  </td>
  <td width="100%">
<!-- BEGIN app_header -->
<div class = "app_header">{current_app_header}</div>
<!-- END app_header -->
<div align="center">{messages}</div>
<div align="center">{sideboxcontent}</div>
   <table border="0" cellpadding="5" width="100%">
    <tr>
     <td>
<!-- END navbar -->

<!-- BEGIN preferences --><a href="{preferences_url}"><img src="{preferences_img}" border="0" alt="{preferences_text}"></a>
<!-- END preferences -->
