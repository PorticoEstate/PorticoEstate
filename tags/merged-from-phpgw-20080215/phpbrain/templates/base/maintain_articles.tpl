{message}
<div style='width:95%; text-align:center'>{num_regs}</div>
<table width=95% bgcolor="#D3DCE3">
	<tr>
		{left}
		<td>
			<form method="POST" action="{form_filters_action}">
				<table>
					<tr>
						<td>
							Category&nbsp;&nbsp;
							<select name="cat" onchange="this.form.submit();">
								<option value="0">All</option>
								{select_categories}
							</select>
						</td>
						<td>
							<select name="publish_filter" onchange="this.form.submit();">{select_publish}</select>
						</td>
						<td>
							<input type=text name="query" value="{value_query}">&nbsp;&nbsp;&nbsp;<input type=submit name="search" value="{lang_search}">
						</td>
					</tr>
				</table>
			</form>
		</td>
		{right}
	</tr>
</table>
<table width=95%>
	<form name="maintain_articles" method="POST" action="{form_maintain_articles_action}">
		<tr bgcolor="#D3DCE3">
			<th>{head_title}</th><th>{head_topic}</th><th>{head_author}</th><th>{head_date}</th><th width=80>{lang_actions}&nbsp;&nbsp;<a href="javascript:check_all('select')"><img src="{img_src_checkall}"></a></th>
		</tr>
		<!-- BEGIN table_row_block -->
		<tr bgcolor="{tr_color}"><td>{title}</td><td>{topic}</td><td>{author}</td><td>{date}</td><td align=right>{actions}<input type="checkbox" name="{name_checkbox}" value="True"></td></tr>
		<!-- END table_row_block -->
		<tr>
			<td colspan=5 align=right><input type=submit name="publish_selected" value="{lang_publish_selected}">&nbsp;<input type=submit name="delete_selected" value="{lang_delete_selected}"></td>
		</tr>
	</form>
</table>
