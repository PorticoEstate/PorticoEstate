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
<table class="basic" align="center">
	<tr>
		<td colspan="7" class="center">
			{report_this}
		</td>
	</tr>
</table>
<!-- END B_action_report -->

<table class="basic" align="center">
	<tr class="bg_color1" align="center">
		<td>&nbsp;</td>
		 {prev_arrows}
		<td>&nbsp;</td>
		 {next_arrows}
		<td>&nbsp;</td>
	</tr>
</table>

{stats_data_display}

<table class="basic" align="center">
	<tr class="header">
		<td width="3%" align="center">
		 &nbsp;
		</td>
		<td width="2%">
		 &nbsp;
		</td>
	
		<td width="34%">
			
 				<b>{hdr_subject}</b>
			
		</td>
		<td width="23%">
			
				<b>{hdr_from}</b>
			
		</td>
		<td width="12%">
			
				<b>{hdr_date}</b>
			
		</td>
		<td width="4%">
			
				<b>{hdr_size}</b>
			
		</td>
	</tr>
<!-- BEGIN B_no_messages -->
	<tr class="header">
		<td colspan="6" align="center">
<!-- form delmove init here is just a formality, need an opening form tag but form does noting -->
 		{V_mlist_form_init}
			{report_no_msgs}
		</td>
	</tr>
<!-- END B_no_messages -->

<!--- &nbsp; LAME BLOCK SEP &nbsp; -->

<!-- BEGIN B_msg_list -->
	<tr>
		<td class="center">
<!-- INIT FORM ONCE -->{V_mlist_form_init}
			<input type="checkbox" name="delmov_list[]" value="{mlist_embedded_uri}" />
		</td>
		<td align="center">
			<div align="right">
				{mlist_new_msg}
				 {mlist_attach}
			</div>
			 {all_flags_images}
		</td>
		<td>
		 {open_strikethru}
			
				<a href="{mlist_subject_link}">{mlist_subject}</a>
			
		 {close_strikethru}
		</td>
		<td>
 		 {open_strikethru}
 			<a href="{mlist_reply_link}">{mlist_from}</a> {mlist_from_extra}
 		 {close_strikethru}
		</td>
		<td align="center">
			{mlist_date}
		</td>
		<td align="center">
			{mlist_size}
		</td>
	</tr>
<!-- END B_msg_list -->
	<tr class="header">
		<td class="center">
			<a href="javascript:check_all()"><img src="{check_image}" border="0"></a>
		</td>
		<td colspan="5">
			<table width="100%" border="0" cellpadding="1" cellspacing="1">
				<tr class="header">
					<td width="10%" align="left">
						&nbsp;{delmov_button}
					</td>
					<td width="10%" align="left">
						&nbsp;&nbsp;{compose_clickme}
					</td>
					<td width="80%" align="right">
					 {delmov_listbox}
					</td>
					</form>
				</tr>
			</table>
		</td>
	</tr>
</table>

<br /> 

<table class="basic" align="center">
	<tr>
		<td class="left">
			{mlist_newmsg_char}&nbsp;{mlist_newmsg_txt}
		</td>
	</tr>
</table>
	{debugdata}
<!-- end index_main_b0_l1.tpl -->

