<!-- BEGIN list -->
<script language="JavaScript1.1" type="text/javascript"><!--
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
  // -->
</script>


  <table class="basic_noCollapse" align="center">
    {rows}
  </table>

<!-- END list -->

<!-- BEGIN app_row -->
    <tr class="header">
      <td valign="middle"><img src="{app_icon}" alt="[ {app_name} ]" /><a name="{a_name}"></a></td>
      <td width="95%" valign="middle"><strong>&nbsp;&nbsp;{app_name}</strong></td>
    </tr>
<!-- END app_row -->

<!-- BEGIN app_row_noicon -->
    <tr class="admin95"><td colspan="2" class="middle"><strong>&nbsp;&nbsp;{app_name}</strong> <a name="{a_name}"></a></td></tr>
<!-- END app_row_noicon -->

<!-- BEGIN link_row -->
    <tr><td colspan="2">&nbsp;&nbsp;&#8226;&nbsp;<a href="{pref_link}">{pref_text}</a></td></tr>
<!-- END link_row -->

<!-- BEGIN spacer_row -->
    <tr><td colspan="2">&nbsp;</td></tr>
<!-- END spacer_row -->

