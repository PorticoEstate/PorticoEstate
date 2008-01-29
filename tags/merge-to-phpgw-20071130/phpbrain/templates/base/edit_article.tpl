<form method="POST" action="{form_action}">
{hidden_fields}
<table  width="100%" border="0" cellspacing="1" cellpadding="3" style='border:1px solid black'>
	{message}
	<!-- BEGIN answer_question_block -->
	<tr class="th">
		<td colspan="2"><b>{lang_head_question}:</b></td>
	</tr>
	<tr>
		<td align="right">{lang_summary}: </td><td>{question_summary}</td>
	</tr>
	<tr>
		<td align="right">{lang_details}:</td><td>{question_details}</td>
	</tr>
	<!-- END answer_question_block -->
	<!-- BEGIN article_id_block -->
	<tr class="th">
		<td align=right>
			{lang_articleID}:
		</td>
		<td>
			{show_articleID}
		</td>
	</tr>
	<!-- END article_id_block -->
	<tr class="row_on">
		<td width="10%" align="right">
			<span style="font:normal 12px sans-serif">{lang_category}:</span>
		</td>
		<td width="90%">
			<select name="cat_id">
				<option value="0">{lang_none}</option>
				{select_category}
			</select>
		</td>
	</tr>
	<tr class="row_off">
		<td align="right">
			<span style='font:normal 12px sans-serif'>{lang_title}:</span>
		</td>
		<td>
			<input type="text" size="40" name="title" value="{value_title}">
		</td>
	</tr>
	<tr class="row_on">
		<td align=right>
			<span style="font:normal 12px sans-serif">{lang_topic}:</span>
		</td>
		<td>
			<input type="text" size="40" name="topic" value="{value_topic}">
		</td>
	</tr>
	<tr class="row_off">
		<td align="right">
			<span style="font:normal 12px sans-serif">{lang_keywords}:</span>
		</td>
		<td>
			<input type="text" size="40" name="keywords" value="{value_keywords}">
		</td>
	</tr>
	<tr class="row_on">
		<td colspan="2">
			<textarea name="exec[text]" id="exec_text" rows="10" cols="80">{value_text}</textarea>
		</td>
	</tr>
	<tr class="th">
		<td colspan="2">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="2">
			{btn_save}{btn_cancel}
		</td>
	</tr>
</table>
</form>