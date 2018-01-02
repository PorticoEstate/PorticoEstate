<!-- begin index_main_b0_l1.tpl -->
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
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
<tr>
	<td colspan="7" align="center">
		<font face="{stats_font}" size="{stats_font_size}">{report_this}</font>
	</td>
</tr>
</table>
<!-- END B_action_report -->

<table border="0" cellpadding="1" cellspacing="0" width="100%" align="center">
<tr bgcolor="{arrows_backcolor}" class="{arrows_backcolor_class}" align="center">
	<td>&nbsp;</td>
	{prev_arrows}
	<td>&nbsp;</td>
	{next_arrows}
	<td>&nbsp;</td>
</tr>
</table>

{stats_data_display}

<table border="0" cellpadding="3" cellspacing="0" width="100%" align="center">
<tr bgcolor="{hdr_backcolor}" class="{hdr_backcolor_class}">
	<td width="3%" align="center">
		&nbsp;
	</td>
	<td width="2%">
		&nbsp;
	</td>
	
	<td width="34%">
		<font size="{hdr_font_size}" face="{hdr_font}">
 		<b>{hdr_subject}</b>
		</font>
	</td>
	<td width="23%">
		<font size="{hdr_font_size}" face="{hdr_font}">
		<b>{hdr_from}</b>
		</font>
	</td>
	<td width="12%">
		<font size="{hdr_font_size}" face="{hdr_font}">
		<b>{hdr_date}</b>
		</font>
	</td>
	<td width="4%">
		<font size="{hdr_font_size}" face="{hdr_font}">
		<b>{hdr_size}</b>
		</font>
	</td>
</tr>
<!-- BEGIN B_no_messages -->
<tr bgcolor="{mlist_backcolor}" class="{mlist_backcolor_class}">
	<td colspan="6" align="center">
		<!-- form delmove init here is just a formality, need an opening form tag but form does noting -->
		{V_mlist_form_init}
		<font size="2" face="{mlist_font}">{report_no_msgs}</font>
	</td>
</tr>
<!-- END B_no_messages -->

<!--- &nbsp; LAME BLOCK SEP &nbsp; -->

<!-- BEGIN B_msg_list -->
<tr bgcolor="{mlist_backcolor}" class="{mlist_backcolor_class}">
	<td align="center">
	<!-- INIT FORM ONCE -->{V_mlist_form_init}
		<input type="checkbox" name="delmov_list[]" value="{mlist_embedded_uri}">
	</td>
	<td align="center">
		<div align="right">
			<font size="{mlist_font_size}" face="{mlist_font}">{mlist_new_msg}</font>
			{mlist_attach}
		</div>
		{all_flags_images}
	</td>
	<td>
		{open_strikethru}<font size="{mlist_font_size}" face="{mlist_font}"><a href="{mlist_subject_link}">{mlist_subject}</a></font>{close_strikethru}
	</td>
	<td>
		{open_strikethru}<font size="{mlist_font_size}" face="{mlist_font}"><a href="{mlist_reply_link}">{mlist_from}</a> {mlist_from_extra}</font>{close_strikethru}
	</td>
	<td align="center">
		<font size="{mlist_font_size}" face="{mlist_font}">{mlist_date}</font>
	</td>
	<td align="center">
		<font size="{mlist_font_size_sm}" face="{mlist_font}">{mlist_size}</font>
	</td>
</tr>
<!-- END B_msg_list -->
<tr bgcolor="{ftr_backcolor}" class="{ftr_backcolor_class}">
	<td align="center">
		<a href="javascript:check_all()">
		<img src="{check_image}" border="0"></a>
	</td>
	<td colspan="5">
		<table width="100%" border="0" cellpadding="1" cellspacing="1">
		<tr bgcolor="{ftr_backcolor}" class="{ftr_backcolor_class}">
			<td width="10%" align="left">
				<font face="{stats_font}" size="{stats_font_size}">
					&nbsp;{delmov_button}
				</font>
			</td>
			<td width="10%" align="left">
				<font face="{stats_font}" size="{stats_font_size}">
					&nbsp;&nbsp;{compose_clickme}
				</font>
			</td>
			<td width="30%" align="center">
				<font face="{stats_font}" size="{stats_font_size}">
				<!-- BEGIN B_empty_trash -->
					<a href="{empty_trash_link}" 
					onClick="return window.confirm('{lang_empty_trash_warn}');">{lang_empty_trash}</a>
				<!-- END B_empty_trash -->
				&nbsp;
				</font>
			</td>
			<td width="50%" align="right">
				{delmov_listbox}
			</td>
			</form>
		</tr>
		</table>
	</td>
</tr>
</table>

<br /> 

<table border="0" align="center" width="100%">
<tr>
	<td align="left">
		<font color="{mlist_newmsg_color}">{mlist_newmsg_char}</font>&nbsp;{mlist_newmsg_txt}
	</td>
</tr>
</table>
{debugdata}
<!-- end index_main_b0_l1.tpl -->

