<!-- begin message_main.tpl -->
<script type="text/javascript">
function do_action(act)
{
	document.delmov.what.value = act;
	document.delmov.submit();
}
</script>
<!-- BEGIN B_x-phpgw-type -->
<center>
<h1>THIS IS A phpGroupWare-{application} EMAIL</h1>
In the future, this will process a specially formated email msg.<hr />
</center>
<!-- END B_x-phpgw-type -->
{widget_toolbar}
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
<tr>
	<td align="center" width="20%">
		{view_option_ilnk}&nbsp;<font size="2" face="{theme_font}">{view_option}</font>
	</td>
	<td align="center" width="20%">
		{view_headers_ilnk}&nbsp;<font size="2" face="{theme_font}">{view_headers_href}</font>
	</td>
	<td align="center" width="20%">
		{view_raw_message_ilnk}&nbsp;<font size="2" face="{theme_font}">{view_raw_message_href}</font>
	</td>
	<td align="center" width="20%">
		{view_printable_ilnk}&nbsp;<font size="2" face="{theme_font}">{view_printable_href}</font>
	</td>
	
	<form name="{frm_delmov_name}" action="{frm_delmov_action}" method="post">
	<input type="hidden" name="what" value="delete">
	<input type="hidden" name="sort" value="{move_current_sort}">
	<input type="hidden" name="order" value="{move_current_order}">
	<input type="hidden" name="start" value="{move_current_start}">
	<input type="hidden" name="{move_postmove_goto_name}" value="{move_postmove_goto_value}">
	<input type="hidden" name="{mlist_checkbox_name}" value="{mlist_embedded_uri}">
	<td width="20%" align="right">
		<font face="{ctrl_bar_font}" size="{ctrl_bar_font_size}">{delmov_listbox}&nbsp;</font>
	</td>
	</form>
</tr>
</table>
<!-- style specially for the return to FOLDER_LINK to get a better color for that link using class in that A element -->
<STYLE type="text/css">
<!--
  a.c_backto { text-decoration: underline; background: {reply_btns_bkcolor}; color: {reply_btns_text}; }
  A.c_backto:link { text-decoration:underline; background: {reply_btns_bkcolor}; color: {reply_btns_text}; }
  A.c_backto:visted { text-decoration:underline; background: {reply_btns_bkcolor}; color: {reply_btns_text}; }
  A.c_backto:active { text-decoration:underline; background: {reply_btns_bkcolor}; color: {reply_btns_text}; }
  
  a.c_replybar { text-decoration: none; background: {reply_btns_bkcolor}; color: {reply_btns_text}; }
  A.c_replybar:link { text-decoration:none; background: {reply_btns_bkcolor}; color: {reply_btns_text}; }
  A.c_replybar:visted { text-decoration:none; background: {reply_btns_bkcolor}; color: {reply_btns_text}; }
  A.c_replybar:active { text-decoration:none; background: {reply_btns_bkcolor}; color: {reply_btns_text}; }
-->
</STYLE>

<table cellpadding="1" cellspacing="0" width="100%" align="center">
<tr style="spacing-bottom: 1pt;">
	<td colspan="2" bgcolor="{reply_btns_bkcolor}" style="spacing-bottom: 1pt;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr class="email_folder">
			<td width="30%">
				<font size="3" face="{theme_font}" color="{reply_btns_text}">
				<!-- lnk_goback_folder comes here with special class value refering to backto css above -->
				<b>{go_back_to} {lnk_goback_folder}</b>
				</font>
			</td>
			<td align="right" width="50%">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" align="right">
				<tr>
					<td align="center">
						{ilnk_reply}<font size="2" face="{theme_font}">{href_reply}</font>
					</td>
					<td align="center">
						{ilnk_replyall}<font size="2" face="{theme_font}">{href_replyall}</font>
					</td>
					<td align="center">
						{ilnk_forward}<font size="2" face="{theme_font}">{href_forward}</font>
					</td>
					<td align="center">
						{ilnk_delete}<font size="2" face="{theme_font}">{href_delete}</font>
					</td>
					<!-- BEGIN edit_message -->
					<td align="center">
						{ilnk_edit}<font size="2" face="{theme_font}">{href_edit}</font>
					</td>
					<!-- END edit_message -->
				</tr>
				</table>
			</td>
			<td align="right" valign="middle" width="20%">
				<font size="2" face="{theme_font}" color="{reply_btns_text}">
				{ilnk_prev_msg}{href_prev_msg}
				{ilnk_next_msg}{href_next_msg}
 				</font>
			</td>
			<!-- meaningless sep line -->
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td bgcolor="{tofrom_labels_bkcolor}" class="{tofrom_labels_class}" valign="top" width="20%">
		<font size="2" face="{theme_font}">
		<strong>{lang_from}:</strong></font>
	</td>
	<td bgcolor="{tofrom_data_bkcolor}" class="{tofrom_data_class}" width="80%">
		<font size="2" face="{theme_font}">
		{from_data_final}
		</font>
	</td>
</tr>
<tr>
	<td bgcolor="{tofrom_labels_bkcolor}" class="{tofrom_labels_class}" valign="top">
		<font size="2" face="{theme_font}">
		<strong>{lang_to}:</strong></font>
	</td> 
	<td bgcolor="{tofrom_data_bkcolor}" class="{tofrom_data_class}">
		<font size="2" face="{theme_font}">
		{to_data_final}
		</font>
	</td>
</tr>

<!-- BEGIN B_cc_data -->
<tr>
	<td bgcolor="{tofrom_labels_bkcolor}" class="{tofrom_labels_class}" valign="top">
		<font size="2" face="{theme_font}">
		<strong>{lang_cc}:</strong></font>
	</td> 
	<td bgcolor="{tofrom_data_bkcolor}" class="{tofrom_data_class}">
		<font size="2" face="{theme_font}">
		{cc_data_final}
		</font>
	</td>
</tr>
<!-- END B_cc_data -->

<tr>
	<td bgcolor="{tofrom_labels_bkcolor}" class="{tofrom_labels_class}" valign="top">
		<font size="2" face="{theme_font}">
		<strong>{lang_date}:</strong></font>
	</td> 
	<td bgcolor="{tofrom_data_bkcolor}" class="{tofrom_data_class}">
		<font size="2" face="{theme_font}">
		{message_date}
		</font>
	</td>
</tr>

<!-- BEGIN B_attach_list -->
<tr>
	<td bgcolor="{tofrom_labels_bkcolor}" class="{tofrom_labels_class}" valign="top">
		<font size="2" face="{theme_font}">
		<strong>{lang_files}:</strong></font>
	</td> 
	<td bgcolor="{tofrom_data_bkcolor}" class="{tofrom_data_class}">
		<font size="2" face="{theme_font}">
		{list_of_files}
		</font>
	</td>
</tr>
<!-- END B_attach_list -->

<tr>
	<td bgcolor="{tofrom_labels_bkcolor}" class="{tofrom_labels_class}" valign="top">
		<font size="2" face="{theme_font}">
		<strong>{lang_subject}:</strong></font>
	</td> 
	<td bgcolor="{tofrom_data_bkcolor}" class="{tofrom_data_class}">
		<font size="2" face="{theme_font}">
		{message_subject}
		</font>
	</td>
</tr>
</table>

<!-- start message display -->
<br />
<table border="0" cellpadding="1" cellspacing="1" width="100%" align="center">
<!-- BEGIN B_debug_parts -->
<tr>
	<td align="left">
		{msg_body_info}
	</td>
</tr>
<!-- END B_debug_parts -->

<!-- BEGIN B_display_part -->
<tr class="row_on">
	<td bgcolor="{theme_row_on}" width="100%" colspan="3">
		<font size="2" face="{theme_font}">
		<strong>{title_text}</strong> &nbsp; &nbsp; {display_str}</font>
	</td>
</tr>
<tr>
	<td width="1%">&nbsp;</td>
	<td align="left" width="98%">
		<br />{message_body}
	</td>
	<td width="1%">&nbsp;</td>
</tr>
<!-- END B_display_part -->

</table>
{geek_bar}
<!-- lame sep ##### Lame Sep -->
{debugdata}
<!-- end message_main.tpl -->
