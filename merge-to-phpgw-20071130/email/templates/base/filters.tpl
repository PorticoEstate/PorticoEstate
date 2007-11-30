<!-- BEGIN Sieve Mail Filters -->

<form action="{form_edit_filter_action}" method="post">
	<input type="hidden" name="filter_num" value="{filter_num}">
	
	<h3><center>{lang_email_filters}</center></h3>
	
	<table width="90%" border="0" cellpadding="3" cellspacing="2" align="center">
	<tr bgcolor="{row_off}" class="row_off">
		<td colspan="4" align="left">
			<font size="-1">{lang_filter_number}:&nbsp;<strong>[{filter_num}]</strong>
			&nbsp;&nbsp;
			{lang_filter_name}:&nbsp;<input size="30" name="{filter_name_box_name}" value="{filter_name_box_value}">
			</font>
		</td>
	</tr>
	
	<tr>
		<td colspan="4"><font size="-1">&nbsp;</font></td>
	</tr>
	
	<tr bgcolor="{row_on}" class="row_on">
		<td colspan="4">
			<strong>{lang_if_messages_match}</strong>
		</td>
	</tr>
	
	<!-- BEGIN B_matches_row -->
	<tr bgcolor="{row_off}" class="row_off">
		<td align="center">
			<font size="-1">{V_match_left_td}</font>
		</td>
		<td align="center">
			<font size="-1">
			<select name="{examine_selectbox_name}">
				<option value="from" {from_selected}>{lang_from}</option>
				<option value="to" {to_selected}>{lang_to}</option>
				<option value="cc" {cc_selected}>{lang_cc}</option>
				<!-- <option value="bcc" {bcc_selected}>{lang_bcc}</option>  -->
				<option value="recipient" {recipient_selected}>{lang_recipient}</option>
				<option value="sender" {sender_selected}>{lang_sender}</option>
				<option value="subject" {subject_selected}>{lang_subject}</option>
				<option value="received" {received_selected}>{lang_received_headers}</option> 
				<!-- <option value="header">{lang_header}</option> -->
				<!-- <option value="size_larger">{lang_size_larger}</option> -->
				<!-- <option value="size_smaller">{lang_size_smaller}</option> -->
				<!-- <option value="allmessages">{lang_allmessages}</option> -->
				<!-- <option value="body">{lang_body}</option> -->
			</select>
			</font>
		</td>
		<td align="center">
			<font size="-1">
			<select name="{comparator_selectbox_name}">
				<option value="contains" {contains_selected}>{lang_contains}</option>
				<option value="notcontains" {notcontains_selected}>{lang_notcontains}</option>
			</select>
			</font>
		</td>
		<td align="center">
			<font size="-1">
			<input size="20" name="{matchthis_textbox_name}" value="{match_textbox_txt}">
			</font>
		</td>
	</tr>
	<!-- END B_matches_row -->
	</table>

	<br />
	
	<table width="90%" border="0" cellpadding="3" cellspacing="2" align="center">
	<tr bgcolor="{row_on}" class="row_on">
		<td colspan="4">
			<strong>{lang_take_actions}</strong>
		</td>
	</tr>
	<!-- BEGIN B_actions_row -->
	<tr bgcolor="{row_off}" class="row_off">
		<td width="20%" align="center">
			<font size="-1">
			{V_action_widget}
			</font>
		</td>
		<td width="30%" align="center">
			<font size="-1">
			{folder_listbox}
			</font>
		</td>
		<td width="30%" align="center">
			<font size="-1">
			{lang_or_enter_text}&nbsp;
			<input size="20" name="{action_textbox_name}" value="{action_textbox_txt}">
			</font>
		</td>
		<td width="20%" align="center">
			<font size="-1">
			<input type="checkbox" name="{stop_filtering_checkbox_name}" value="True" {stop_filtering_checkbox_checked}>
			&nbsp;{lang_stop_if_matched}
			</font>
		</td>
	</tr>
	<!-- END B_actions_row -->
	</table>
	
	<br />
	
	<table width="50%" border="0" cellPadding="0" cellSpacing="0" align="center">
	<tr> 
		<td width="33%" align="center">
			<input type="submit" name="submit" value="{lang_submit}">
		</td>
		<td width="34%" align="center">
			<input type="reset" name="reset" value="{lang_clear}">
		</td>
</form>
<form action="{form_cancel_action}" method="post">
		<td width="33%" align="center">
			<input type="submit" name="cancel" value="{lang_cancel}">
		</td>
</form>
	</tr>
	</table>
	
	<br />
	<hr />
	<br />
	
	<table width="90%" border="0" cellPadding="4" cellSpacing="4" align="center">
	<tr> 
		<td colspan="2" align="center">
			<!--
			<em>Under Development</em>
			-->
			<!-- this text reminds the user to submit the filter first so AM knows about it -->
			<br />Submit the filter data to the database by clicking submit,
			<br />then you may test or apply the filter.
		</td>
	</tr>
	<tr> 
		<td width="50%" align="center">
			{test_this_filter_href}
		</td>
		<td width="50%" align="center">
			{apply_this_filter_href}
		</td>
	</tr>
	</table>
	
	<p>&nbsp;</p>

{debugdata}

<!-- END Sieve Mail Filters -->
