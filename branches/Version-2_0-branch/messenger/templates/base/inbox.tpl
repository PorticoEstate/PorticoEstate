<!-- BEGIN list -->
<script type="text/javascript">
//<[CDATA[
	function check_all()
	{
		for (i = 0; i < document.messages.elements.length; i++)
		{
			if (document.messages.elements[i].type == "checkbox")
			{
				if (document.messages.elements[i].checked)
				{
					document.messages.elements[i].checked = false;
				}
				else
				{
					document.messages.elements[i].checked = true;
				}
			}
		}
	}
//]]>
</script>

{app_header}

<form action="{form_action}" method="post" name="messages">
	<table>
		<thead>
			<tr>
				<td width="1%" align="center"><input type="checkbox" onClick="check_all()"></td>
				<td width="1%">&nbsp;</td>
				<td width="8%">{sort_date}</td>
				<td width="27%">{sort_from}</td>
				<td width="60%">{sort_subject}</td>
			</tr>
		</thead>
		<tbody>
			<!-- BEGIN row -->
			<tr class="{row_class}">
				<td><input type="checkbox" name="messages[]" value="{row_msg_id}" /></td>
				<td><strong>{row_status}</strong></td>
				<td>{row_date}</td>
				<td>{row_from}</td>
				<td><a href="{row_url}">{row_subject}</a></td>
			</tr>
			<!-- END row -->
		</tbody>
    </table>


	<table border="0" width="95%" align="center">
		<tr>
			<td align="right">
				{button_delete}&nbsp;
			</td>
		</tr>
	</table>
</form>
<!-- END list -->


<!-- BEGIN row_empty -->
<tr bgcolor="#FFFFFF">
	<td colspan="5" align="center">{lang_empty}</td>
</tr>
<!-- END row_empty -->
