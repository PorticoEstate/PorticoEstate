<!-- BEGIN main -->
<style type="text/css">
	.header_row_, A.header_row_
	{
		padding: 0;
		font-weight : bold;
	}
	
	.header_row_D, A.header_row_D
	{
		padding: 0;
		color: silver;
		text-decoration : line-through;
		font-weight : bold;
	}
	
	.header_row_DS, A.header_row_DS, .header_row_ADS, A.header_row_ADS
	{
		padding: 0;
		color: silver;
		text-decoration : line-through;
	}
	
	.header_row_S, A.header_row_S
	{
		padding: 0;
		vertical-align : middle;
	}
	
	.header_row_AS, A.header_row_AS
	{
		padding: 0;
		vertical-align : middle;
	}

	.header_row_FAS, A.header_row_FAS, .header_row_FS, A.header_row_FS
	{
		color: red;
		padding: 0;
		vertical-align : middle;
	}

	.header_row_F, A.header_row_F
	{
		color: red;
		padding: 0;
		font-weight : bold;
		vertical-align : middle;
	}

	.header_row_R, A.header_row_R
	{
		padding: 0;
		font-weight : bold;
		vertical-align : middle;
	}
	
</style>

<script type="text/javascript">
<!--
	var checkedCounter={checkedCounter};
	var active;
	var maxMessages = {maxMessages};
	
	function ttoggleFolderRadio()
	{
		//alert(document.getElementsByTagName("input")[0].checked);
		document.getElementsByTagName("input")[1].checked = "true";
	}

	function toggleFolderRadio(_counter)
	{
		if(active)
		{
			// do not reload, while we try to select some messages
			window.clearTimeout(active);
			{refreshTime}
		}

		var counter = parseInt(_counter);
		//alert(document.getElementById("msg_input_"+_counter).checked);
		//document.getElementsByTagName("input")[1].checked = "true";
		//tr	= eval(document.getElementsByTagName("tr")[counter+23]);
		//input	= eval(document.getElementsByTagName("input")[counter+10]);
		tr	= document.getElementById("msg_tr_"+_counter);
		input	= document.getElementById("msg_input_"+_counter);
		if(input.checked == true)
		{
			checkedCounter+=1;
		}
		else
		{
			checkedCounter-=1;
		}
		if (checkedCounter > 0)
		{
			document.getElementsByTagName("input")[3].checked = "true";
		}
		else
		{
			document.getElementsByTagName("input")[2].checked = "true";
		}
	}

	var sURL = unescape(window.location.pathname);

	function doLoad()
	{
		// the timeout value should be the same as in the "refresh" meta-tag
		{refreshTime}
	}

	function refresh()
	{
		window.location.href = '{refresh_url}';
	}

	doLoad();

//-->
</script>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr class="row_off">
		<td ALIGN="left" WIDTH="70%">
			<a href="{url_compose_empty}">{lang_compose}</a>&nbsp;&nbsp;
			<a href="{url_filter}">{lang_edit_filter}</a>&nbsp;&nbsp;
		</td>
		<td align='right' width="30%">
			{quota_display}
		</td>
	</tr>
	<tr valign="middle">
		<form name=searchForm method=post action="{url_search_settings}">
		<td colspan="1" bgcolor="#ffffcc" align="left" width="70%">
			{lang_quicksearch}:
			<input type="text" size="50" name="quickSearch" value="{quicksearch}"
			onChange="javascript:document.searchForm.submit()">
		</td>
		<td bgcolor="#ffffcc" align="right" width="30%" valign="middle">
			<input type=hidden name="changeFilter">
			<select name="filter" onChange="javascript:document.searchForm.submit()">
				{filter_options}
			</select>
		</td>
		</form>
	</tr>
</table>

<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
	<TR>
		<TD class="row_off">
			<TABLE class="row_off" COLS=2 BORDER='0' cellpadding=0 cellspacing=0 width="100%">
				<TR valign="middle">
					<FORM name=messageList method=post action="{url_change_folder}">
					<td nowrap width="40%" align="LEFT" valign="center" bgcolor="#ffffcc">
						<TT><SMALL>
						<SELECT NAME="mailbox" onChange="document.messageList.submit()">
							{options_folder}
						</SELECT></SMALL></TT>
						<SMALL><INPUT TYPE=radio NAME="folderAction" value="changeFolder" {change_folder_checked}>{lang_change_folder}</SMALL>
						<SMALL><INPUT TYPE=radio NAME="folderAction" value="moveMessage" {move_message_checked}>{lang_move_message}</SMALL>
						<noscript>
							<NOBR><SMALL><INPUT TYPE=SUBMIT NAME="moveButton" VALUE="{lang_doit}"></SMALL></NOBR>
						</noscript>
						<INPUT TYPE=hidden NAME="oldMailbox" value="{oldMailbox}">
					</TD>
                                        <td width="40%">
                                                &nbsp;
                                        </td>
					<td width="2%" align="LEFT" valign="center">
						<input type="image" src="{read_small}" name="mark_read" alt="{desc_read}" title="{desc_read}" width="16">
                                        </td>
                                        <TD WIDTH="2%" ALIGN="MIDDLE" valign="center">
                                                &nbsp;|&nbsp;
                                        </td>
                                        <td width="2%" align="RIGHT" valign="center">
						<input type="image" src="{unread_small}" name="mark_unread" title="{desc_unread}" width="16">
                                        </td>
                                        <TD WIDTH="2%" ALIGN="MIDDLE" valign="center">
                                                &nbsp;
                                        </td>
                                        <td width="2%" align="LEFT" valign="center">
						<input type="image" src="{unread_flagged_small}" name="mark_flagged" title="{desc_important}" width="16">
                                        </td>
                                        <TD WIDTH="2%" ALIGN="MIDDLE" valign="center">
                                                &nbsp;|&nbsp;
                                        </td>
                                        <td width="2%" align="RIGHT" valign="center">
						<input type="image" src="{unread_small}" name="mark_unflagged" title="{desc_unimportant}">
                                        </td>
                                        <TD WIDTH="2%" ALIGN="MIDDLE" valign="center">
                                                &nbsp;&nbsp;
                                        </td>
                                        <td width="2%" align="RIGHT" valign="center">
						<input type="image" src="{unread_deleted_small}" name="mark_deleted" title="{desc_deleted}">
					</TD>
				</TR>
			</TABLE>
			<br>
		</TD>
	</TR>
	{status_row}
	<TR>
		<TD>
			<table WIDTH=100% BORDER=0 CELLPADDING=1 CELLSPACING=1>
				<colgroup>
					<col width="1%">
					<col width="10%">
					<col width="10%">
					<col width="1%">
					<col width="70%">
					<col width="8%">
				</colgroup>
				<tr>
					<td width="1%" bgcolor="#FFFFCC" align="center">
						&nbsp;
					</td>
					<td width="20%" bgcolor="#FFFFCC" align="center">
						<b><a href="{url_sort_from}"><font color="black">{lang_from}</font></a></b>
					</td>
					<td bgcolor="#FFFFCC" align="center">
						<b><a href="{url_sort_date}"><font color="black">{lang_date}</font></a></b>
					</td>
					<td bgcolor="#FFFFCC" align="center">
						&nbsp;
					</td>
					<td bgcolor="#FFFFCC" align="center">
						<b><a href="{url_sort_subject}"><font color="black">{lang_subject}</font></a>
					</td>
					<td bgcolor="#FFFFCC" align="center">
						<b>{lang_size}</b>
					</td>
				</tr>
				{header_rows}
			</table>
		</TD>
	</TR>
	{status_row}
</table>
<!-- END main -->

<!-- BEGIN status_row_tpl -->
	<tr>
		<TD>
			<table WIDTH="100%" BORDER="0" CELLPADDING="1" CELLSPACING="0">
				<tr BGCOLOR="#FFFFFF">
					<td width="18%">
						{link_previous} | {link_next}
					</td>
					<td width="10%">
						&nbsp;
					</td>
					<TD align="center" width="36%">
						{message}
					</td>
					<td width="18%">
						{trash_link}
					</td>
					<td align="right" width="18%">
						{select_all_link}
					</td>
				</tr>
			</table>
		</td>
	</tr>

<!-- END status_row_tpl -->

<!-- BEGIN header_row -->
<tr class="{row_css_class}">
	<td width="1%" bgcolor="#FFFFFF" align="center">
		<input type="checkbox" id="msg_input_{message_counter}" name="msg[{message_counter}]" value="{message_uid}" onClick="toggleFolderRadio('{message_counter}')" {row_selected}>
	</td>
	<td width="10%" bgcolor="#FFFFFF" nowrap>
		<a href="{url_compose}" title="{full_address}">{sender_name}</a>
		<a href="{url_add_to_addressbook}"><img src="{sm_envelope}" width="10" height="8" border="0" align="absmiddle" alt="{lang_add_to_addressbook}" title="{lang_add_to_addressbook}"></a>
	</td>
	<td bgcolor="#FFFFFF" nowrap align="center">
		{date}
	</td>
	<td bgcolor="#FFFFFF" valign="middle" align="center">
		{state}
<!--		<img src="{image_path}/read_small.png" width="16" border="0" alt="{lang_read}" title="{lang_read}">
-->		{row_text}
	</td>
	<td bgcolor="#FFFFFF">
		<a name="subject_url" href="{url_read_message}">{header_subject}</a>
	</td>
	<td bgcolor="#FFFFFF">
		{size}
	</td>
</tr>
<!-- END header_row -->

<!-- BEGIN error_message -->
	<tr>
		<td bgcolor="#FFFFCC" align="center" colspan="6">
			<span class="error">{lang_connection_failed}</span><br>
			{message}
		</td>
	</tr>
<!-- END error_message -->

<!-- BEGIN quota_block -->
	<table border="1" cellpadding="0" cellspacing="0" width="200">
		<tr valign="middle">
			<td width="{leftWidth}%" bgcolor="{quotaBG}" align="center" valign="middle">
				<small>{quotaUsage_left}</small>
			</td>
			<td align="center" valign="middle">
				<small>{quotaUsage_right}</small>
			</td>
		</tr>
	</table>
<!-- END quota_block -->
