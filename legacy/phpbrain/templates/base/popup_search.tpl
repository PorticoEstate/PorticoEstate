<script>
function TransferID(articleID)
{
	old_value=opener.document.add_article_form.related_articles.value;
	opener.document.add_article_form.related_articles.value=old_value + articleID + ', ';
}
</script>
<div style='width:95%; text-align:center'>{num_regs}</div>
<table width=95% bgcolor="#D3DCE3">
	<tr>
		{left}
		<td>
			<form method=POST action="{form_filters_action}">
				<table>
					<tr>
						<td>
							{lang_category}&nbsp;&nbsp;
							<select onchange="this.form.submit();">
								<option value="0">{lang_all}</option>
								{select_categories}
							</select>
						</td>
						<td>
							<input type="text" name="query" value="{value_query}">&nbsp;&nbsp;&nbsp;<input type="submit" name="search" value="{lang_search}">
						</td>
					</tr>
				</table>
			</form>
		</td>
		{right}
	</tr>
</table>
<table width=95%>
	<form name="select_articles" method="POST" action="{form_select_articles_action}">
		<tr bgcolor="{th_color}">
			<th>{head_number}</th><th>{head_title}</th><th></th>
			<!-- BEGIN table_row_block -->
			<tr bgcolor="{tr_color}"><td style="width: 6em">{number}</td><td>{title}</td><td align=center><input type="button" name="button" value="{lang_select}" onClick="TransferID({number});">
			<!-- END table_row_block -->
		</tr>
	</form>
</table>
