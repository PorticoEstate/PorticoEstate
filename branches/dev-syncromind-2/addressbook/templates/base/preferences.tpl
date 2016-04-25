{admin_tabs}
<span style="font-weight: bold;font-family:{font};">{lang_abprefs}</span><br />
<hr style="width: 100%; height: 2px;">
<br />
<br />
<table cellpadding="2" cellspacing="2" border="0" style="text-align: left;">
  <tbody>
  	<tr>
	<td style="vertical-align: middle;background:{th_bg};horizontal-align:middle;"><span style="font-family:{font};">{lang_select_cols} </span></td>
	</tr>
	
	<tr>
	{tabs}
	</tr>

    <tr>
       <td style="vertical-align: top;">
      <form name="{select_columns_form_name}"
 action="{select_columns_form_action}" method="post">
 	<table>

	<tr>
	<td>
	{hider_open}
        <select name="{select_columns_selectbox_name}" multiple size="5">
		<!-- BEGIN B_select_columns_form_options -->
		<option value="{value}">{lang_contact_field}</option>
		<!-- END B_select_columns_form_options -->
        </select>
	</td>
	<td>
        <select name="{select_columns_comtypes_name}" multiple size="5">
		<!-- BEGIN B_select_ctypes_options -->
		<option value="{commtype_description}">{lang_comtype_field}</option>
		<!-- END B_select_ctypes_options -->
        </select>
	</td>
	</tr>
	<tr>
	<td>
	<center><input type='submit' value='{select_columns_submit_value}' name='select_fields'></center>
	{hider_close}
	</td>
	</tr>
	</table>
	<tr><td><hr /></td></tr>
	<tr>
	<td>
	<table style="vertical-align: middle;horizontal-align:left">
	<tr>
		<td style="background:{th_bg};">
		<span style="font-family:{font};">Select your default category</span>
		</td>
	</tr>

	<tr>
		<td style="vertical-align:middle;"> 
		<select name='cat_id'>
			{cat_options}
		</select>
		</td>
	</tr>
	</td>
	</tr>
</table>
      </td>

    </tr>

  </tbody>
</table>
<br />

<table cellpadding="2" cellspacing="2" border="0"
 style="text-align: left; width: 100%;">
  <tbody>
    <tr style="background:{th_bg};">
	{B_selected_rows} 
    </tr>
<tr>
<td colspan='100'>
<hr />
</td>
</tr>
<tr>
<td colspan='100'>
<input type='submit' value='{submit_save_value}' name='save'>
<input type='submit' value='{submit_cancel_value}' name='cancel'>
</td>
</tr>
  </tbody>
</table>
</form>

