<form name="search" method=POST action={form_search_action}>
	<table width=100% style='border:1px solid black; margin-bottom:5px'>
		<tr>
			<td colspan=3 class="th" style='text-align:left'><b>{lang_search_kb}:</b></td>
		</tr>
		<tr>
			<td colspan=3 style='padding:10px 0 10px 0'>{lang_enter_words}:</td>
		</tr>
		<tr>
			<td style='width:75%; text-align:center'>
				<input type="text" name="query" style='width:99%'>
			</td>
			<td style='width:5%; text-align:center'>
				<input type="submit" name="Search" value="{lang_search}">
			</td>
			<td valign=bottom style='width:20%; text-align:left; padding-left:10px'>
				<a href="{link_adv_search}">{lang_advanced_search}</a>
			</td>
		</tr>
	</table>
</form>
<br>
<div align="center" style='border:1px solid black;'>
<form method=POST action="{form_question_action}">
	<table width=100%>
		<tr class=th>
			<td align=left colspan=2><b>{lang_post_question}:</b></td>
		</tr>
		<tr>
			<td style="width: 6em">{lang_summary}:</td><td><input type=text name="summary" style="width:90%"></td>
		</tr>
		<tr>
			<td valign=top>{lang_details}:</td><td><textarea name="details" style="width:90%; height:100px"></textarea></td>
		</tr>
		<tr>
			<td>{lang_select_cat}:</td>
			<td>
				<select name="cat_id">
					<option value="0" selected>{lang_none}</option>
					{select_category}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2><br><b>{posting_process}</b><br><br></td>
		</tr>
		<tr>
			<td colspan=2><input type=submit name=submit value="{lang_submit}">&nbsp;&nbsp;&nbsp;<input type=submit name=cancel value="{lang_cancel}"></td>
		</tr>
	</table>
</form>
</div>
