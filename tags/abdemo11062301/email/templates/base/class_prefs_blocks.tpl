<!-- begin class_prefs_blocks.tpl -->

&nbsp; <!-- == block sep == --> &nbsp; 

<!-- BEGIN B_tr_blank -->
<tr>
	<td colspan="2">
		&nbsp;<br />
	</td>
</tr>
<!-- END B_tr_blank -->

&nbsp; <!-- == block sep == --> &nbsp;

<!-- BEGIN B_tr_sec_title -->
<tr bgcolor="{th_bg}" class="th">
	<td colspan="2" valign="middle">
		<strong>{section_title}</strong>
		&nbsp; &nbsp; &nbsp; {show_help_lnk}
	</td>
</tr>
<!-- END B_tr_sec_title -->

&nbsp; <!-- == block sep == --> &nbsp;

<!-- BEGIN B_tr_long_desc -->
<tr bgcolor="{back_color}" class="{back_color_class}">
	<td colspan="2" align="center" valign="middle">
		<strong>{lang_blurb}</strong>: <p>{long_desc}</p>&nbsp;<br />
	</td>
</tr>
<!-- END B_tr_long_desc -->

&nbsp; <!-- == block sep == --> &nbsp; 

<!-- BEGIN B_tr_textarea -->
<tr bgcolor="{back_color}" class="{back_color_class}">
	<td align="left" width="{left_col_width}">
		{lang_blurb}
	</td>
	<td align="center" valign="middle" width="{right_col_width}">
		<textarea name="{pref_id}" rows="6" cols="50">{pref_value}</textarea>
	</td>
</tr>
<!-- END B_tr_textarea -->

&nbsp; <!-- == block sep == --> &nbsp; 

<!-- BEGIN B_tr_textbox -->
<tr bgcolor="{back_color}" class="{back_color_class}">
	<td align="left" width="{left_col_width}">
		{lang_blurb}
	</td>
	<td align="center" valign="middle" width="{right_col_width}">
		<input type="text" name="{pref_id}" value="{pref_value}">
	</td>
</tr>
<!-- END B_tr_textbox -->

&nbsp; <!-- == block sep == --> &nbsp; 

<!-- BEGIN B_tr_passwordbox -->
<tr bgcolor="{back_color}" class="{back_color_class}">
	<td align="left" width="{left_col_width}">
		{lang_blurb}
	</td>
	<td align="center" valign="middle" width="{right_col_width}">
		<input type="password" name="{pref_id}" value="{pref_value}">
	</td>
</tr>
<!-- END B_tr_passwordbox -->

&nbsp; <!-- == block sep == --> &nbsp; 

<!-- BEGIN B_tr_combobox -->
<tr bgcolor="{back_color}" class="{back_color_class}">
	<td align="left" width="{left_col_width}">
		{lang_blurb}
	</td>
	<td align="center" valign="middle" width="{right_col_width}">
		<select name="{pref_id}">
			{pref_value}
		</select>
	</td>
</tr>
<!-- END B_tr_combobox -->

&nbsp; <!-- == block sep == --> &nbsp; 

<!-- BEGIN B_tr_checkbox -->
	<td align="left" width="{left_col_width}" bgcolor="{back_color}" class="{back_color_class}">
		{lang_blurb}
	</td>
	<td align="center" valign="middle" width="{right_col_width}" bgcolor="{back_color}" class="{back_color_class}">
		<input type="checkbox" name="{pref_id}" value="{checked_flag}" {pref_value}>
	</td>
</tr>
<!-- END B_tr_checkbox -->

&nbsp; <!-- == block sep == --> &nbsp; 

<!-- BEGIN B_submit_btn_only -->
<tr>
	<td colspan="2" align="center">
		<input type="submit" name="{btn_submit_name}" value="{btn_submit_value}">
	</td>
</tr>
<!-- END B_submit_btn_only -->

&nbsp; <!-- == block sep == --> &nbsp; 

<!-- BEGIN B_submit_and_cancel_btns -->
<tr>
	<td align="center">
		<input type="hidden" name="{ex_acctnum_varname}" value="{ex_acctnum_value}">
		<input type="submit" name="{btn_submit_name}" value="{btn_submit_value}">
	</td>
	<td align="center">
		<input type="button" name="{btn_cancel_name}" value="{btn_cancel_value}" onClick="parent.location='{btn_cancel_url}'">
	</td>
</tr>
<!-- END B_submit_and_cancel_btns -->

&nbsp; <!-- == block sep == --> &nbsp; 

<!-- end class_prefs_blocks.tpl -->
