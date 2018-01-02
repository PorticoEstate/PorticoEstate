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

{widget_toolbar}

{V_arrows_form_table}

{stats_data_display}

<table border="0" cellpadding="4" cellspacing="1" width="95%" align="center">
<tr bgcolor="{hdr_backcolor}">
	<td width="3%" align="center">
		&nbsp;
	</td>
	<td width="2%">
		&nbsp;
	</td>
	<td width="20%">
		<font size="{hdr_font_size}" face="{hdr_font}">
		<strong>{hdr_from}</strong>
		</font>
	</td>
	<td width="39%">
		<font size="{hdr_font_size}" face="{hdr_font}">
 		<strong>{hdr_subject}</strong>
		</font>
	</td>
	<td width="10%" align="center">
		<font size="{hdr_font_size_sm}" face="{hdr_font}">
		<strong>{hdr_date}</strong>
		</font>
	</td>
	<td width="4%" align="center">
		<font size="{hdr_font_size_sm}" face="{hdr_font}">
		<strong>{hdr_size}</strong>
		</font>
	</td>
</tr>
<!-- BEGIN B_no_messages -->
<tr bgcolor="{mlist_backcolor}">
	<td colspan="6" align="center">
		<!-- form delmove init here is just a formality, need an opening form tag but form does noting -->
		{V_mlist_form_init}
		<font size="2" face="{mlist_font}">{report_no_msgs}</font>
	</td>
</tr>
<!-- END B_no_messages -->

<!--- &nbsp; LAME BLOCK SEP &nbsp; -->

<!-- BEGIN B_msg_list -->
<tr bgcolor="{mlist_backcolor}">
	<td align="center">
	<!-- INIT FORM ONCE -->{V_mlist_form_init}
		<input type="checkbox" name="{mlist_checkbox_name}" value="{mlist_embedded_uri}">
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
<!-- END B_msg_list -->
<tr bgcolor="{ftr_backcolor}">
	<td>
		<a href="javascript:check_all()"><img src="{app_images}/check.gif" border="0" height="16" width="21"></a>
	</td>
	<td colspan="2" align="left">
		&nbsp;
		<input type="button" value="{delmov_button}" onClick="do_action('delall')">
	</td>
	<td colspan="3" align="right">
		{delmov_listbox}&nbsp;
	</td>
	</form>
</tr>
</table>

<br> 
<!-- end email_index.tpl -->
