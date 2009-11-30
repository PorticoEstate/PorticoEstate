<!-- $Id$ -->
<h1>{lang_todo_action}</h1>
{error}
<form method="post" action="{actionurl}">
		<label for="values_title">{lang_title}:</label>
		<input id="values_title" type="text" name="values[title]" value="{title}" size="50"><br>
		
		<label for="values_descr">{lang_descr}:</label>
		<textarea name="values[descr]" rows="10" cols="80">{descr}</textarea><br>
		
		
		<label for="values_new_parent">{lang_parent}</label>
		<select id="values_new_parent" name="new_parent"><option value="">{lang_none}</option>{todo_list}</select><br>

		<label for="values_start_date">{lang_start_date}:</label>
		{start_select_date}
		{selfortoday}
		<label for="selfortoday">{lang_selfortoday}</label><br>

		<td>{lang_end_date}:</td>
		<td>{end_select_date}</td>
		<td>{lang_daysfromstartdate}</td>
		<td>{daysfromstartdate}</td>

		<label for="">{lang_completed}:</td>
		<td>{stat_list}</td><br>

		<label for="new_cat">{lang_category}</label>
		<select id="new_cat" name="new_cat"><option value="0">{lang_none}</option>{cat_list}</select><br>

		<td>{lang_urgency}:</td>
		<td>{pri_list}</td><br>
		<label for="assinged_user">{lang_assigned_user}:</label>
		<select id="assigned_user" name="assigned[]" multiple><option value="">{lang_nobody}</option>{user_list}</select><br>
		
		<td>{lang_access}:</td>
		<td>{access_list}</td><br>

		<label for="assigned_group">{lang_assigned_group}</label>
		<select id="assigned_group" name="assigned_group[]" multiple><option value="">{lang_none}</option>{group_list}</select><br>
	</tr>
</table>

<!-- BEGIN add -->

<table width="90%" border="0" cellspacing="0" cellpadding="0">
	<tr valign="bottom">
		<td height="35" width="50%">
			<div align="center">
			<input type="submit" name="submit" value="{lang_save}">
		</div>
		</td>
		<td height="35" width="50%">
			<div align="center">
			<input type="reset" name="reset" value="{lang_reset}">
			</div>
		</td>
	</tr>
</table>
</form>
</center>
         
<!-- END add -->

<!-- BEGIN edit -->

<table width="90%" border="0" cellspacing="0" cellpadding="0">
	<tr valign="bottom">
		<td height="35" width="50%">
			<div align="center">
			<input type="submit" name="submit" value="{lang_save}">
		</div></form>
		</td>
		<td height="35" width="50%">
			<div align="center">
			{delete}
			</div>
		</td>
	</tr>
</table>
</center>
         
<!-- END edit -->
