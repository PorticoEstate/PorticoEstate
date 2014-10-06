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

<table border="0" cellpadding="2" cellspacing="1" width="95%" align="center">
<tr>
	<td bgcolor="{ctrl_bar_back2}" width="25%" align="center">
		<font face="{ctrl_bar_font}" size="{ctrl_bar_font_size}"><a href="{compose_link}">{compose_txt}</a></font>
	</td>
	<td bgcolor="{ctrl_bar_back2}" width="25%" align="center">
		<font face="{ctrl_bar_font}" size="{ctrl_bar_font_size}">{folders_href}</font>
	</td>
	<td bgcolor="{ctrl_bar_back2}" width="25%" align="center">
		<font face="{ctrl_bar_font}" size="{ctrl_bar_font_size}">{filters_href}</font>
	</td>
	<td bgcolor="{ctrl_bar_back2}" width="25%" align="center">
		<font face="{ctrl_bar_font}" size="{ctrl_bar_font_size}"><a href="{email_prefs_link}">{email_prefs_txt}</a></font>
	</td>
</tr>
</table>

<table border="0" cellpadding="3" cellspacing="1" width="95%" align="center">
<tr>
	<td colspan="2" align="center">
		<font face="{stats_font}" size="{stats_font_size}">&nbsp;
		<!-- BEGIN B_action_report -->
		{report_this}
		<!-- END B_action_report -->
		</font>
	</td>
</tr>
</table>

{V_arrows_form_table}

<table border="0" cellpadding="5" cellspacing="1" width="95%" align="center">
<tr>
	<td bgcolor="{stats_backcolor}" align="center">
		<font face="{stats_font}" size="{stats_font_size}" color="{stats_color}">
			<strong>{stats_folder}</strong>
		</font>
	</td>
	<td bgcolor="{stats_backcolor}" align="center">
		<font face="{stats_font}" size="{stats_font_size}" color="{stats_color}">
			&nbsp;&nbsp;{stats_new}&nbsp;&nbsp;{lang_new}
		</font>
	</td>
	<td bgcolor="{stats_backcolor}" align="center">
		<font face="{stats_font}" size="{stats_font_size}" color="{stats_color}">
			&nbsp;&nbsp;{stats_saved}&nbsp;&nbsp;{lang_total}
		</font>
	</td>
	<!-- BEGIN B_show_size -->
	<td bgcolor="{stats_backcolor}" align="center">
		<font face="{stats_font}" size="{stats_font_size}" color="{stats_color}">
			&nbsp;&nbsp;{stats_size}&nbsp;&nbsp;{lang_size}
		</font>
	</td>
	<!-- END B_show_size -->
	<!-- &nbsp; Lame Seperator &nbsp; -->
	<!-- BEGIN B_get_size -->
	<form name="{frm_get_size_name}" action="{frm_get_size_action}" method="post">
	<input type="hidden" name="what" value="delete">
	<input type="hidden" name="folder" value="{current_folder}">
	<input type="hidden" name="sort" value="{current_sort}">
	<input type="hidden" name="order" value="{current_order}">
	<input type="hidden" name="start" value="{current_start}">
	<input type="hidden" name="{get_size_flag}" value="1">
	<td bgcolor="{stats_backcolor}" align="center">
		<font face="{stats_font}" size="{stats_font_size}" color="{stats_color}">
			&nbsp;&nbsp;<input type="submit" value="{lang_get_size}">
		</font>
	</td>
	</form>
	<!-- END B_get_size -->
	<td bgcolor="{stats_backcolor}" align="center">
		<font face="{stats_font}" size="{stats_font_size}" color="{stats_color}">
			&nbsp;&nbsp;{stats_first}&nbsp;{stats_to_txt}&nbsp;{stats_last}
		</font>
	</td>
</tr>
</table>

<table border="0" cellpadding="4" cellspacing="1" width="95%" align="center">
<tr>
	<td bgcolor="{hdr_backcolor}" width="3%" align="center">
		&nbsp;
	</td>
	<td bgcolor="{hdr_backcolor}" width="2%">
		&nbsp;
	</td>
	<td bgcolor="{hdr_backcolor}" width="20%">
		<font size="{hdr_font_size}" face="{hdr_font}">
		<strong>{hdr_from}</strong>
		</font>
	</td>
	<td bgcolor="{hdr_backcolor}" width="39%">
		<font size="{hdr_font_size}" face="{hdr_font}">
 		<strong>{hdr_subject}</strong>
		</font>
	</td>
	<td bgcolor="{hdr_backcolor}" width="10%" align="center">
		<font size="{hdr_font_size_sm}" face="{hdr_font}">
		<strong>{hdr_date}</strong>
		</font>
	</td>
	<td bgcolor="{hdr_backcolor}" width="4%" align="center">
		<font size="{hdr_font_size_sm}" face="{hdr_font}">
		<strong>{hdr_size}</strong>
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
		<input type="checkbox" name="msglist[]" value="{mlist_msg_num}">
	</td>
	<td bgcolor="{mlist_backcolor}" align="center">
		{mlist_attach}
	</td>
	<td bgcolor="{mlist_backcolor}" align="left">
		{open_newbold}<font size="{mlist_font_size}" face="{mlist_font}">{mlist_from} {mlist_from_extra}</font>{close_newbold}
	</td>
	<td bgcolor="{mlist_backcolor}" align="left">
		{open_newbold}<font size="{mlist_font_size}" face="{mlist_font}"><a href="{mlist_subject_link}">{mlist_subject}</a></font>{close_newbold}
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
	<td bgcolor="{ftr_backcolor}" colspan="6">
		&nbsp;
	</td>
</tr>
</table>

<br /> 
<!-- end email_index.tpl -->
