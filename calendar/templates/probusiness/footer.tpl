<!-- BEGIN footer_table -->
<br clear="all" />
<hr style="border: 1px solid #dfdfdf" />
<table class="basic">
	<tr>
		{table_row}
	</tr>
</table>
<!-- END footer_table -->

<!-- BEGIN footer_row -->
<td valign="top">
	<form action="{action_url}" method="post" name="{form_name}">
		<B>{label}:</B>
		{hidden_vars}
		<select name="{form_label}" onchange="{form_onchange}">
			{row}
		</select>
           <noscript><input type="submit" value="{go}" /></noscript>
	   </form>
	  
	 </td>
<!-- END footer_row -->

<!-- BEGIN blank_row -->
		<td>{b_row}</td>
<!-- END blank_row -->


