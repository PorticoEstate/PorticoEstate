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

<table class="basic" align="center">
<tr>
	<td class="bg_color1" width="25%" align="center">
		<a href="{compose_link}">{compose_txt}</a>
	</td>
	<td width="25%" align="center">
		{folders_href}
	</td>
	<td width="25%" align="center">
		{filters_href}
	</td>
	<td width="25%" align="center">
		<a href="{email_prefs_link}">{email_prefs_txt}</a>
	</td>
</tr>
</table>

<table class="basic" align="center">
<tr>
	<td colspan="2" align="center">
		&nbsp;
		<!-- BEGIN B_action_report -->
		{report_this}
		<!-- END B_action_report -->
		
	</td>
</tr>
</table>

{V_arrows_form_table}

<table class="basic" align="center">
<tr>
	<td class="bg_color1" align="center">
			<strong>{stats_folder}</strong>
	</td>
	<td class="bg_color1" align="center">
			&nbsp;&nbsp;{stats_new}&nbsp;&nbsp;{lang_new}
	</td>
	<td class="bg_color1" align="center">
			&nbsp;&nbsp;{stats_saved}&nbsp;&nbsp;{lang_total}
	</td>
	<!-- BEGIN B_show_size -->
	<td class="bg_color1" align="center">
			&nbsp;&nbsp;{stats_size}&nbsp;&nbsp;{lang_size}
	</td>
	<!-- END B_show_size -->
	<!-- &nbsp; Lame Seperator &nbsp; -->
	<!-- BEGIN B_get_size -->
	<form name="{frm_get_size_name}" action="{frm_get_size_action}" method="post">
		<input type="hidden" name="what" value="delete" />
		<input type="hidden" name="folder" value="{current_folder}" />
		<input type="hidden" name="sort" value="{current_sort}" />
		<input type="hidden" name="order" value="{current_order}" />
		<input type="hidden" name="start" value="{current_start}" />
		<input type="hidden" name="{get_size_flag}" value="1" />
			<td class="bg_color1" align="center">
				&nbsp;&nbsp;<input type="submit" value="{lang_get_size}" />
			</td>
	</form>
	<!-- END B_get_size -->
	<td class="bg_color1" align="center">
			&nbsp;&nbsp;{stats_first}&nbsp;{stats_to_txt}&nbsp;{stats_last}
	</td>
</tr>
</table>

<table class="basic" align="center">
<tr>
	<td class="bg_color2" width="3%" align="center">
		&nbsp;
	</td>
	<td class="bg_color2" width="2%">
		&nbsp;
	</td>
	<td class="bg_color2" idth="20%">
		<strong>{hdr_from}</strong>
	</td>
	<td class="bg_color2" width="39%">
 		<strong>{hdr_subject}</strong>
	</td>
	<td class="bg_color2" width="10%" align="center">
		<strong>{hdr_date}</strong>
	</td>
	<td class="bg_color2" width="4%" align="center">
		<strong>{hdr_size}</strong>
	</td>
</tr>
<!-- BEGIN B_no_messages -->
<tr>
	<td class="bg_color2" colspan="6" align="center">
		<!-- form delmove init here is just a formality, need an opening form tag but form does noting -->
		{V_mlist_form_init}
{report_no_msgs}
	</td>
</tr>
<!-- END B_no_messages -->

<!--- &nbsp; LAME BLOCK SEP &nbsp; -->

<!-- BEGIN B_msg_list -->
<tr>
	<td class="bg_view" align="center">
	<!-- INIT FORM ONCE -->{V_mlist_form_init}
		<input type="checkbox" name="msglist[]" value="{mlist_msg_num}">
	</td>
	<td class="bg_view" align="center">
		{mlist_attach}
	</td>
	<td class="bg_view" align="left">
		{open_newbold}
		{mlist_from} {mlist_from_extra}{close_newbold}
	</td>
	<td class="bg_view" align="left">
		{open_newbold}
		<a href="{mlist_subject_link}">{mlist_subject}</a>{close_newbold}
	</td>
	<td class="bg_view" align="center">
		{mlist_date}
	</td>
	<td class="bg_view" align="center">
		{mlist_size}
	</td>
</tr>
<!-- END B_msg_list -->
<tr>
	<td class="bg_color1" colspan="6">
		&nbsp;
	</td>
</tr>
</table>

<br /> 
<!-- end email_index.tpl -->
