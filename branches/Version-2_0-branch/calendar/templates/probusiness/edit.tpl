<!-- BEGIN edit_entry -->
<script language="JavaScript">
	self.name="first_Window";
	function accounts_popup()
	{
		Window1=window.open('{accounts_link}',"Search","width=800,height=600,toolbar=no,scrollbars=yes,resizable=yes");
	}
</script>
<form action="{action_url}" method="post" name="app_form">
	{common_hidden}
	<table class="basic" border="0" align="center">
 		<tr>
 			<td class="header" style="text-align: center; height: 8px" colspan="3">
				{errormsg}
 			</td>
 		</tr>
		{row}
 		<tr style="vertical-align: top">
  		<td style="height: 50px; padding-top: 10px">
				<input type="submit" value="{submit_button}" />&nbsp;
</form>
			</td>
			<td style="padding-top: 10px">
				{cancel_button}
			</td>
  		<td style="padding-top: 10px" class="right">{delete_button}</td>
 		</tr>
</table>
<!-- END edit_entry -->
<!-- BEGIN list -->
 <tr>
  <td class="header" style="vertical-align: top; white-space: nowrap; padding-right: 13px; padding-left: 8px">
  	<span style="font-weight: bold">{field}:</span>
  </td>
  <td colspan="2" class="bg_view" style="vertical-align: top; width: 100%">
  	{data}
  </td>
 </tr>
<!-- END list -->
<!-- BEGIN hr -->

  <tr class="header">
    <td colspan="3">
     {hr_text}
    </td>
 </tr>

<!-- END hr -->
