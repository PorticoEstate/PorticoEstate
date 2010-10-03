<!-- begin email_index.tpl -->
<script type="text/javascript">
function do_action(act)
{
	flag = 0;
	for (i=0; i<document.delmov.elements.length; i++) {
		//alert(document.delmov.elements[i].type);
		if (document.delmov.elements[i].type == "checkbox") {
			if (document.delmov.elements[i].checked) {
				flag = 1;
			}
		}
	}
	if (flag != 0) {
		document.delmov.what.value = act;
		document.delmov.submit();
	} else {
		alert("{select_msg}");
		document.delmov.tofolder.selectedIndex = 0;
	}
}

function check_all()
{
	for (i=0; i<document.delmov.elements.length; i++) {
		if (document.delmov.elements[i].type == "checkbox") {
			if (document.delmov.elements[i].checked) {
				document.delmov.elements[i].checked = false;
			} else {
				document.delmov.elements[i].checked = true;
			}
		} 
	}
}
</script>

<!-- BEGIN B_action_report -->
<table border="0" cellpadding="0" cellspacing="0" width="95%" align="center">
<tr>
	<td colspan="7" align="center">
		<font face="{stats_font}" size="{stats_font_size}">{report_this}</font>
	</td>
</tr>
</table>
<!-- END B_action_report -->

<table border="0" cellpadding="1" cellspacing="1" width="95%" align="center">
<tr bgcolor="{arrows_backcolor}" align="center">
	<td>&nbsp;</td>
	{prev_arrows}
	<td>&nbsp;</td>
	{next_arrows}
	<td>&nbsp;</td>
</tr>
</table>


<table border="0" cellpadding="1" cellspacing="1" width="95%" align="center">
<tr>
	<td colspan="6" bgcolor="{stats_backcolor}">
		<table border="0" cellpadding="0" cellspacing="1" width="100%">
		<tr>
			<td>
				<font face="{stats_font}" size="{stats_foldername_size}" color="{stats_color}">
					&nbsp;<strong>{stats_folder}</strong>
				</font>
				<br>
				<font face="{stats_font}" size="{stats_font_size}" color="{stats_color}">
					&nbsp;&nbsp;&nbsp;{stats_new}&nbsp;&nbsp;:&nbsp;&nbsp;{lang_new2}<br>
					&nbsp;&nbsp;&nbsp;{stats_saved}&nbsp;&nbsp;:&nbsp;&nbsp;{lang_total2}
					<!-- BEGIN B_show_size -->
					<br>&nbsp;&nbsp;&nbsp;{stats_size_or_button}&nbsp;&nbsp;:&nbsp;&nbsp;{lang_size2}
					<!-- END B_show_size -->
				</font>
			</td>
			<td align="right">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
				<form name="{switchbox_frm_name}" action="{switchbox_action}" method="post">
					<td>
						<font face="{stats_font}" size="{stats_font_size}">
						{switchbox_listbox}
						</font>
					</td>
					<td>
						<font face="{stats_font}" size="{stats_font_size}">
						&nbsp;&nbsp;{folders_btn}
						</font>
					</td>
				</form>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>

<table border="0" cellpadding="3" cellspacing="1" width="95%" align="center">
<tr>
	<td bgcolor="{hdr_backcolor}" width="3%" align="center">
		&nbsp;
	</td>
	<td bgcolor="{hdr_backcolor}" width="2%">
		&nbsp;
	</td>
	
	<td bgcolor="{hdr_backcolor}" width="34%">
		<font size="{hdr_font_size}" face="{hdr_font}">
 		<b>{hdr_subject}</b>
		</font>
	</td>
	<td bgcolor="{hdr_backcolor}" width="23%">
		<font size="{hdr_font_size}" face="{hdr_font}">
		<b>{hdr_from}</b>
		</font>
	</td>
	<td bgcolor="{hdr_backcolor}" width="12%">
		<font size="{hdr_font_size}" face="{hdr_font}">
		<b>{hdr_date}</b>
		</font>
	</td>
	<td bgcolor="{hdr_backcolor}" width="4%">
		<font size="{hdr_font_size}" face="{hdr_font}">
		<b>{hdr_size}</b>
		</font>
	</td>
</tr>
<!-- BEGIN B_no_messages -->
<tr>
	<td bgcolor="{mlist_backcolor}" colspan="6" align="center">
		<!-- form delmove init here is just a formality, need an opening form tag but form does noting -->
		{V_mlist_form_init}
		<font size="2" face="{mlist_font}">{report_no_msgs}</font>
	</td>
</tr>
<!-- END B_no_messages -->

<!--- &nbsp; LAME BLOCK SEP &nbsp; -->

<!-- BEGIN B_msg_list -->
<tr>
	<td bgcolor="{mlist_backcolor}" align="center">
	<!-- INIT FORM ONCE -->{V_mlist_form_init}
		<input type="checkbox" name="delmov_list[]" value="{mlist_embedded_uri}">
	</td>
	<td bgcolor="{mlist_backcolor}" align="center">
		<font size="{mlist_font_size}" face="{mlist_font}">{mlist_new_msg}{mlist_attach}</font>
	</td>
	<td bgcolor="{mlist_backcolor}">
		<font size="{mlist_font_size}" face="{mlist_font}"><a href="{mlist_subject_link}">{mlist_subject}</a></font>
	</td>
	<td bgcolor="{mlist_backcolor}">
		<font size="{mlist_font_size}" face="{mlist_font}"><a href="{mlist_reply_link}">{mlist_from}</a> {mlist_from_extra}</font>
	</td>
	<td bgcolor="{mlist_backcolor}" align="center">
		<font size="{mlist_font_size}" face="{mlist_font}">{mlist_date}</font>
	</td>
	<td bgcolor="{mlist_backcolor}" align="center">
		<font size="{mlist_font_size_sm}" face="{mlist_font}">{mlist_size}</font>
	</td>
</tr>
<!-- END B_msg_list -->
<tr>
	<td bgcolor="{ftr_backcolor}" align="center">
		<a href="javascript:check_all()">
		<img src="{app_images}/check.gif" border="0" height="16" width="21"></a>
	</td>
	<td bgcolor="{ftr_backcolor}" colspan="5">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<input type="button" value="{delmov_button}" onClick="do_action('delall')">
				<font face="{stats_font}" size="{stats_font_size}">
					&nbsp;&nbsp;<a href="{compose_link}">{compose_txt}</a>
					<!-- BEGIN B_get_size -->
					&nbsp;&nbsp;&nbsp;<a href="{get_size_link}">{lang_get_size}</a>
					<!-- END B_get_size -->
				</font>
			</td>
			<td align="right">
				{delmov_listbox}
			</td>
			</form>
		</tr>
		</table>
	</td>
</tr>
</table>

<br> 

<table border="0" align="center" width="95%">
<tr>
	<td align="left">
		<font color="{mlist_newmsg_color}">{mlist_newmsg_char}</font>&nbsp;{mlist_newmsg_txt}
	</td>
</tr>
</table>
<!-- end email_index.tpl -->

