{processes_css}
<div style="color:red; text-align:center">{message}</div>
<div>
	<div>
		{proc_bar}
	</div>
	<div>
		{errors}
	</div>
</div>

<form id='editsource' action="{form_editsource_action}" method="post">
<input type="hidden" name="p_id" value="{p_id}" />
<input type="hidden" name="source_type" value="{source_type}" />
<table style="border: 1px solid black">
<tr class="th">
  <td>
  		{lang_select_source}:
		<select name="activity_id" onchange="this.form.submit();">
		<option value="0" {selected_sharedcode}>{lang_Shared_code}</option>
		<!-- BEGIN block_select_activity -->
		<option value="{activity_id}" {selected_activity}>{activity_name}</option>
		<!-- END block_select_activity -->
		</select>
  		{code_or_tpl_btn}
  </td>
</tr>
<tr class="row_on">
  <td>
    <table>
		<tr>
		<td>
		<!-- BEGIN block_datas -->
		<textarea id='src' name="source" rows="20" cols="80">{data}</textarea>
		<!-- END block_datas -->
		</td>
		<td valign="top">
			{side_commands}
		</td>
		</tr>
  	</table>
  </td>
</tr>
<tr class="th">
	<td>
		<input type="submit" name='save' value="{lang_save}" />
		<input type="submit" name='cancel' value="{lang_cancel}" />
	</td>
</tr>
</table>  
</form>
