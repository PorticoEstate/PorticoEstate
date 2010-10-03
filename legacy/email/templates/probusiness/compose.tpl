<!-- begin compose.tpl -->
<script type="text/javascript">
<!--
	self.name="first_Window";
	function addybook(extraparm)
	{
		Window1=window.open('{js_addylink}'+extraparm,"Search","width={jsaddybook_width},height={jsaddybook_height},toolbar=no,scrollbars=yes,resizable=yes");
	}
	function attach_window(url)
	{
		document.{form1_name}.attached_filenames.value="";
		awin = window.open(url,"attach","width=500,height=400,toolbar=no,resizable=yes");
	}
	function spellcheck()
	{
		document.doit.btn_spellcheck.value = "'Spell Check*'";
		document.doit.submit() ;
	}
	function send()
	{
		if (document.doit.to.value == "") {
			alert('Please enter a email address in the To box');
		} else {
			document.doit.submit();
		}
	}
	function jsaddybook_closing(contact_ids)
        {
                //alert('OPENER GOT -> '+contact_ids);
        }
-->
</script>

{widget_toolbar}

<table class="basic" align="center">
<!--  <form enctype="multipart/form-data" name="{ form1_name }" action="{form1_action}" method="{ form1_method }"> -->
<form enctype="application/x-www-form-urlencoded" name="{form1_name}" action="{form1_action}" method="{form1_method}">
<tr>
	<td colspan="2">
		<table width="100%" class="noCollapse" align="center">
			<tr class="header">
				<td align="left">
					{addressbook_button}
				</td>
				<td align="center">
					{spellcheck_button}
				</td>
				<td align="right">
					&nbsp;
				</td>
				<td align="right">
					{send_button}
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr class="bg_color1">
	<td width="20%" align="left">
		<strong>&nbsp;{to_box_desc}</strong>
	</td>
	<td width="80%" align="left">
		<input type="text" name="{to_box_name}" size="80" value="{to_box_value}" />
	</td>
</tr>
<tr class="bg_color2">
	<td>
		<strong>&nbsp;{cc_box_desc}</strong>
	</td>
	<td>
		<input type="text" name="{cc_box_name}" size="80" value="{cc_box_value}" />
	</td>
</tr>
<tr class="bg_color1">
	<td>
		<strong>&nbsp;{bcc_box_desc}</strong>
	</td>
	<td>
		<input type="text" name="{bcc_box_name}" size="80" value="{bcc_box_value}" />
	</td>
</tr>
<tr class="bg_color2">
	<td>
		<strong>&nbsp;{subj_box_desc}</strong>
	</td>
	<td>
		<input type="text" name="{subj_box_name}" size="80" value="{subj_box_value}" />
	</td>
</tr>

<!-- BEGIN B_checkbox_sig -->
<tr class="bg_color1">
	<td colspan="1">
		<strong>&nbsp;{checkbox_sig_desc}</strong>
	</td>
	<td colspan="1">
		<input type="checkbox" name="{checkbox_sig_name}" value="{checkbox_sig_value}" {ischecked_checkbox_sig} />
	</td>
</tr>
<!-- END B_checkbox_sig -->
<tr class="bg_color2">
	<td colspan="1">
		<strong>&nbsp;{checkbox_req_notify_desc}</strong>
	</td>
	<td colspan="1">
	<input type="checkbox" name="{checkbox_req_notify_name}" value="{checkbox_req_notify_value}" {ischecked_checkbox_req_notify}>
	</td>
</tr>
<tr class="bg_color1">
	 <td>
		 {attachfile_js_button}
	 </td>
	 <td>
	 	<input type="text" size="80" name="attached_filenames" onClick="javascript:{attachfile_js_onclick}" />
	<td>
 </tr>
 </table>
<!-- this textarea should be 78 chars each line to conform with RFC822 old line length standard 78+CR+LF= 80 char line -->
<!-- when used with enctype multipart/form-data and wrap=hard this will add the hard wrap CRLF to the end of each line -->
<!-- NEW we no longer wrap=hard here, wordwrap line lengths taken care of in code now -->
<table align="center" class="basic_noCollapse">
<tr class="bg_color2" align="center">
	<td>
		<!-- <textarea name="_body_box_name_" cols="84" rows="15" wrap="hard">_body_box_value_</textarea> -->
		<textarea name="{body_box_name}" cols="88" rows="15">{body_box_value}</textarea> 
		<!-- <textarea name="_body_box_name_" cols="78" rows="20" wrap="hard">_body_box_value_</textarea> -->
	</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr><td>{send_button}</td></tr>
</table>
</form>

<script type="text/javascript">
  document.doit.body.focus();
  if(document.doit.subject.value == "") document.doit.subject.focus();
  if(document.doit.to.value == "") document.doit.to.focus();
</script>
<!-- end compose.tpl -->
