<!-- begin index_plocks.tpl -->

<!-- BEGIN B_mlist_form_init -->
<form name="{frm_delmov_name}" action="{frm_delmov_action}" method="post">
<input type="hidden" name="what" value="delall">
<!-- input type="hidden" name="folder" value=" { current_folder } " -->
<input type="hidden" name="sort" value="{current_sort}">
<input type="hidden" name="order" value="{current_order}">
<input type="hidden" name="start" value="{current_start}">
<!-- END B_mlist_form_init -->

&nbsp;	<!-- &nbsp; Lame Seperator &nbsp; --> &nbsp;

<!-- BEGIN B_arrows_form_table -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
<tr bgcolor="{arrows_backcolor}" class="{arrows_backcolor_class}">
	<td width="2%" align="left" valign="top">
		<table border="0" bgcolor="{arrows_td_backcolor}" cellspacing="0" cellpadding="0">
		<tr>
			<td align="left">
				{first_page}
			</td>
		</tr>
		</table>
	</td>
	<td width="2%" align="left" valign="top">
		<table border="0" bgcolor="{arrows_td_backcolor}" cellspacing="0" cellpadding="0">
		<tr>
			<td align="left">
				{prev_page}
			</td>
		</tr>
		</table>
	</td>
	<td width="2%" align="right" valign="top">
		<table border="0" bgcolor="{arrows_td_backcolor}" cellspacing="0" cellpadding="0">
		<tr>
			<td align="right">
				{next_page}
			</td>
		</tr>
		</table>
	</td>
	<td width="2%" align="right" valign="top">
		<table border="0" bgcolor="{arrows_td_backcolor}" cellspacing="0" cellpadding="0">
		<tr>
			<td align="right">
				{last_page}
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<!-- END B_arrows_form_table -->

&nbsp;	<!-- &nbsp; Lame Seperator &nbsp; --> &nbsp;

<!-- BEGIN B_stats_layout2 -->
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center" style="padding-bottom: 1px;">
<tr bgcolor="{stats_backcolor}" class="{stats_backcolor_class}">
	<td align="center" class="{stats_color_class}">
		<font face="{stats_font}" size="{stats_foldername_size}" color="{stats_color}">
			<strong>{stats_folder}</strong>
		</font>
	</td>
	<td align="center" class="{stats_color_class}">
		<font face="{stats_font}" size="{stats_font_size}" color="{stats_color}">
			&nbsp;&nbsp;{stats_new}&nbsp;&nbsp;{lang_new}
		</font>
	</td>
	<td align="center" class="{stats_color_class}">
		<font face="{stats_font}" size="{stats_font_size}" color="{stats_color}">
			&nbsp;&nbsp;{stats_saved}&nbsp;&nbsp;{lang_total}
		</font>
	</td>
	{form_get_size_opentag}
	<td align="center" class="{stats_color_class}">
		<font face="{stats_font}" size="{stats_font_size}" color="{stats_color}">
			&nbsp;&nbsp;{stats_size_or_button}&nbsp; {lang_size}
		</font>
	</td>
	{form_get_size_closetag}
	<td align="center" class="{stats_color_class}">
		<font face="{stats_font}" size="{stats_font_size}" color="{stats_color}">
			&nbsp;&nbsp;{stats_first}&nbsp;{stats_to_txt}&nbsp;{stats_last}
		</font>
	</td>
</tr>
</table>
<!-- END B_stats_layout2 -->

&nbsp;	<!-- &nbsp; Lame Seperator &nbsp; --> &nbsp;

<!-- BEGIN B_stats_layout1 -->
<table border="0" cellpadding="1" cellspacing="1" width="100%" align="center">
<tr class="{stats_backcolor_class}">
	<td bgcolor="{stats_backcolor}">
		<table border="0" cellpadding="0" cellspacing="1" width="100%">
		<tr>
			{form_get_size_opentag}
			<td class="{stats_color_class}">
				<font face="{stats_font}" size="{stats_foldername_size}" color="{stats_color}">
					&nbsp;<strong>{stats_folder}</strong>
				</font>
				<br />
				<font face="{stats_font}" size="{stats_font_size}" color="{stats_color}">
					&nbsp;&nbsp;&nbsp;{stats_new}&nbsp;&nbsp;:&nbsp;&nbsp;{lang_new2}<br />
					&nbsp;&nbsp;&nbsp;{stats_saved}&nbsp;&nbsp;:&nbsp;&nbsp;{lang_total2}<br />
					&nbsp;&nbsp;&nbsp;{stats_size_or_button}&nbsp;&nbsp;&nbsp;&nbsp;{lang_size2}
				</font>
			</td>
			{form_get_size_closetag}
			<td align="right">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
				{form_folder_switch_opentag}
					<td>
						<font face="{stats_font}" size="{stats_font_size}">
						{folder_switch_combobox}
						</font>
					</td>
					<td>
						<font face="{stats_font}" size="{stats_font_size}">
						&nbsp;&nbsp;{folders_btn}
						</font>
					</td>
				{form_folder_switch_closetag}
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<!-- END B_stats_layout1 -->

&nbsp;	<!-- &nbsp; Lame Seperator &nbsp; --> &nbsp;

<!-- BEGIN B_mlist_block -->
<tr bgcolor="{mlist_backcolor}" class="{mlist_backcolor_class}">
	<td align="center">
	<!-- INIT FORM ONCE -->{V_mlist_form_init}
		<input type="checkbox" name="delmov_list[]" value="{mlist_msg_num}">
	</td>
	<td align="center">
		{mlist_attach}
	</td>
	<td align="left">
		{open_newbold}<font size="{mlist_font_size}" face="{mlist_font}">{mlist_from} {mlist_from_extra}</font>{close_newbold}
	</td>
	<td align="left">
		{open_newbold}<font size="{mlist_font_size}" face="{mlist_font}"><a href="{mlist_subject_link}">{mlist_subject}</a></font>{close_newbold}
	</td>
	<td align="center">
		<font size="{mlist_font_size}" face="{mlist_font}">{mlist_date}</font>
	</td>
	<td align="center">
		<font size="{mlist_font_size_sm}" face="{mlist_font}">{mlist_size}</font>
	</td>
</tr>
<!-- END B_mlist_block -->

&nbsp;	<!-- &nbsp; Lame Seperator &nbsp; --> &nbsp;

<!-- BEGIN B_mlist_submit_form -->
<p>
<FORM action="{mlist_submit_form_action}" method="post">
	{mlist_hidden_vars}
	Pass off to mlist class 
	<input type="submit" name="submit" value="Submit to mlist">
	to navigate all results.

</form>
</p>
<!-- END B_mlist_submit_form -->


<!-- end index_plocks.tpl -->
