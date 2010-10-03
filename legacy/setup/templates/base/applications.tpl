<!-- BEGIN header -->
<script type="text/javascript">
function check_all(strTargetAction)
{
  var elms = document.getElementById('apps').getElementsByTagName('input');
  var iElmsLen = elms.length;
  var iTALen = strTargetAction.length
  for ( var i = 0; i < iElmsLen; ++i)
  {
    if (elms.item(i).type == "checkbox" && elms.item(i).name.substring(0, iTALen) == strTargetAction)
    {
      if (elms.item(i).checked)
      {
        elms.item(i).checked = false;
      }
      else
      {
        elms.item(i).checked = true;
      }
    } 
  }
}
</script>
<h1>{description}</h1>
<!-- END header -->

<!-- BEGIN app_header -->
<form action="applications.php" method="post" id="apps">
<table id="apps">
	<thead>
		<tr>
			<th colspan="2">{app_info}</th>
			<th>{app_status}</th>
			<th>{app_currentver}</th>
			<th>{app_version}</th>
			<th class="checkcell">
				{app_install}<br>
				<a href="javascript:check_all('install')"><img src="../phpgwapi/templates/base/images/{check}" title="{install_all}" alt="{install_all}"></a>
			</th>
			<th class="checkcell">
				{app_upgrade}<br>
				<a href="javascript:check_all('upgrade')"><img src="../phpgwapi/templates/base/images/{check}" title="{install_all}" alt="{install_all}"></a>
			</th>
			<th>{app_resolve}</th>
			<th class="checkcell">
				{app_remove}<br>
				<a href="javascript:check_all('remove')"><img src="../phpgwapi/templates/base/images/{check}" title="{install_all}" alt="{install_all}"></a>
			</th>
		</tr>
	</thead>
	<tbody>
<!-- END app_header -->

<!-- BEGIN apps -->
		<tr class="{bg_class}">
			<td class="appicon"><a href="applications.php?detail={appname}"><img src="../phpgwapi/templates/base/images/{instimg}" alt="{instalt}" title="{instalt}" border="0"></a></td>
			<td><strong>{appname}</strong></td>
			<td>{appinfo}</td>
			<td>{currentver}</td>
			<td>{version}</td>
			<td class="{row_install} checkcell">{install}</td>
			<td class="{row_upgrade} checkcell">{upgrade}</td>
			<td class="checkcell">{resolution}&nbsp;</td>
			<td class="{row_remove} checkcell">{remove}</td>
		</tr>
<!-- END apps -->

<!-- BEGIN detail -->
		<li>
			<strong>{name}</strong><br>
			{details}
		</li>
<!-- END detail -->

<!-- BEGIN table -->
  <tr bgcolor="{bg_color}">
    <td>{tables}</td>
  </tr>
<!-- END table -->

<!-- BEGIN hook -->
  <tr bgcolor="{bg_color}">
    <td>{hooks}</td>
  </tr>
<!-- END hook -->

<!-- BEGIN dep -->
  <tr bgcolor="{bg_color}">
    <td>{deps}</td>
  </tr>
<!-- END dep -->

<!-- BEGIN resolve -->
  <tr bgcolor="{bg_color}">
    <td>{resolution}</td>
  </tr>
<!-- END resolve -->

<!-- BEGIN submit -->
{goback}
<!-- END submit -->

<!-- BEGIN app_footer -->
  </tbody>
  <tr class="th">
    <td colspan="5">{debug} {lang_debug}</td>
    <td class="checkcell">
     <a href="javascript:check_all('install')"><img src="../phpgwapi/templates/base/images/{check}" title="{install_all}" alt="{install_all}"></a>
    </td>
    <td class="checkcell">
     <a href="javascript:check_all('upgrade')"><img src="../phpgwapi/templates/base/images/{check}" title="{upgrade_all}" alt="{upgrade_all}"></a>
    </td>
    <td>&nbsp;</td>
    <td class="checkcell">
      <a href="javascript:check_all('remove')"><img src="../phpgwapi/templates/base/images/{check}" title="{remove_all}" alt="{remove_all}"></a>
    </td>
  </tr>
</table>
<div class="button_group">
     <input type="submit" name="submit" value="{submit}">
     <input type="submit" name="cancel" value="{cancel}">
</div>
</form>
<!-- END app_footer -->

<!-- BEGIN footer -->
	{footer_text}
	<div class="banner"> </div>
</div>
<!-- END footer -->
