<!-- BEGIN main -->
<script language="JavaScript1.2">
<!--

function doLoad()
{
	// the timeout value should be the same as in the "refresh" meta-tag
	// the timeout is in miliseconds
	// setTimeout( "refresh()", {timeout} );
	{refreshTime}
}

function refresh()
{
    var Ziel = '{refresh_url}'
    window.location.href = Ziel;
}

doLoad();

//-->
</script>
<STYLE type="text/css">
	.header_row_, A.header_row_
	{
		color: blue;
		FONT-SIZE: 11px;
		height : 12px;
		padding: 0;
		font-weight : bold;
	}
	
	.header_row_D, A.header_row_D
	{
		FONT-SIZE: 11px;
		height : 12px;
		padding: 0;
		color: silver;
		text-decoration : line-through;
		font-weight : bold;
	}
	
	.header_row_DS, A.header_row_DS, .header_row_ADS, A.header_row_ADS
	{
		FONT-SIZE: 11px;
		height : 12px;
		padding: 0;
		color: silver;
		text-decoration : line-through;
	}
	
	.header_row_S, A.header_row_S
	{
		color: blue;
		FONT-SIZE: 11px;
		height : 12px;
		padding: 0;
		vertical-align : middle;
	}
	
	.header_row_AS, A.header_row_AS
	{
		ccolor: #000000;
		color: blue;
		FONT-SIZE: 11px;
		height : 12px;
		padding: 0;
		vertical-align : middle;
	}

	.header_row_FAS, A.header_row_FAS, .header_row_FS, A.header_row_FS
	{
		color: red;
		FONT-SIZE: 11px;
		height : 12px;
		padding: 0;
		vertical-align : middle;
	}

	.header_row_F, A.header_row_F
	{
		color: red;
		FONT-SIZE: 11px;
		height : 12px;
		padding: 0;
		font-weight : bold;
		vertical-align : middle;
	}

	.header_row_R, A.header_row_R
	{
		ccolor: #000000;
		color: blue;
		FONT-SIZE: 11px;
		height : 12px;
		padding: 0;
		font-weight : bold;
		vertical-align : middle;
	}

	.quota
	{
		FONT-SIZE: 9px;
		height : 10px;
		vertical-align : middle;
	}
	
	
</STYLE>

<script LANGUAGE="Javascript">
<!--
	var maxMessages = {maxMessages};
	var oldColor, oldFontWeight, checkedCounter=0, aktiv;
	
	function restartCounter()
	{
		if(aktiv)
	{
			// do not reload, while we try to select some messages
			window.clearTimeout(aktiv);
			{refreshTime}
			//window.alert('buh');
		}
	}

	function toggleFolderRadio(_counter)
	{
		restartCounter();
		
		var counter = parseInt(_counter);
		//document.getElementsByTagName("input")[1].checked = "true";
		//tr	= eval(document.getElementsByTagName("tr")[counter+23]);
		//input	= eval(document.getElementsByTagName("input")[counter+10]);
		tr	= document.getElementById("msg_tr_"+_counter);
		input	= document.getElementById("msg_input_"+_counter);
		if(input.checked == true)
		{
			tr.style.backgroundColor        = "silver";
			checkedCounter+=1;
		}
		else
		{
			tr.style.backgroundColor	= "white";
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

	function parentOn(_i)
	{
		restartCounter();

		var i = parseInt(_i);
		//tr	= eval(document.getElementsByTagName("tr")[i+23]);
		tr	= document.getElementById("msg_tr_"+_i);
		
		tr.style.backgroundColor	= "#D3D3D3";
	}
	
	function parentOff(_i)
	{
		var i = parseInt(_i);
		
		tr	= document.getElementById("msg_tr_"+_i);
		input	= document.getElementById("msg_input_"+_i);
		
		if(input.checked == true)
		{
			tr.style.backgroundColor        = 'silver';
		}
		else
		{
			tr.style.backgroundColor	= '#FFFFFF';
		}
	}
	
	function mark()
	{
		restartCounter();

		//alert(maxMessages);
		//var counter	= 10;
		
		master 		= document.getElementsByName("masterSelect")[0];
		slaveInput	= document.getElementsByTagName("input")[20];
		
		for(var i = 0; i < maxMessages; i++)
		{
			input	= document.getElementById("msg_input_"+i);
			tr	= document.getElementById("msg_tr_"+i);
			if(master.checked == true)
			{
				input.checked			= true;
				tr.style.backgroundColor	= 'silver';
			}
			else
			{
				input.checked			= false;
				tr.style.backgroundColor	= '#FFFFFF';
			}
	}
	
		if(master.checked == true)
		{
			checkedCounter	= maxMessages;
			document.getElementsByTagName("input")[3].checked = "true";
		}
		else
	{
			checkedCounter	= 0;
			document.getElementsByTagName("input")[2].checked = "true";
		}
		
		//alert(master.checked);
	}
	
	function mark_read(action)
	{
		document.messageList.mark_read.value = action;
		document.messageList.submit() ;
	}
	function mark_unread(action)
	{
		document.messageList.mark_unread.value = action;
		document.messageList.submit() ;
	}

	function mark_flagged(action)
	{
		document.messageList.mark_flagged.value = action;
		document.messageList.submit() ;
	}

	function mark_unflagged(action)
	{
		document.messageList.mark_unflagged.value = action;
		document.messageList.submit() ;
	}

	function mark_deleted(action)
	{
		document.messageList.mark_deleted.value = action;
		document.messageList.submit() ;
	}

	function change_filter(action)
	{
		document.searchForm.changeFilter.value = action;
		document.searchForm.submit() ;
	}
//-->
</script>
<ddiv class="main_body" style="font-weight:bold; height:18%; width:100%; left:0px; vertical-align:bottom;">

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0" BORDER="0">
	<TR>
		<TD BGCOLOR="{row_off}">
			<TABLE style='background:#f3f3ff;' bBGCOLOR="{row_off}" COLS=2 BORDER="0" cellpadding=0 cellspacing=0 width="100%" height="100%">
				<TR valign="middle">
					<form name=searchForm method=post action="{url_search_settings}">
					<td colspan="1" bgcolor="#ffffcc" align="left" width="70%">
						{lang_quicksearch}:
						<input type="text" size="50" name="quickSearch" value="{quicksearch}" 
						onChange="javascript:document.searchForm.submit()">
					</td>
					<td align="center" bgcolor="#ffffcc">
						{quota_display}
					</td>
					<td bgcolor="#ffffcc" align="right" width="30%" valign="middle">
						<input type=hidden name="changeFilter">
						<select name="filter" onChange="javascript:document.searchForm.submit()">
							{filter_options}
						</SELECT>
					</td>
					</form>
				</TR>

				<TR BGCOLOR="{row_off}">
					<TD width="30%" ALIGN="left" nowrap style='font-size:9.0pt; font-family:Arial;color:#5A538D;border=0px solid #B0A3D9;font-weight:bold;'>
						<a class="body_link" href="{url_compose_empty}">{lang_compose}</a>&nbsp;&nbsp;
						<a class="body_link" href="{url_filter}">{lang_edit_filter}</a>
					</td>
					<TD colspan="2" width="70%" ALIGN="right" nowrap style='font-size:9.0pt; font-family:Arial;color:#5A538D;border=0px solid #000000;font-weight:bold;'>
						<FORM name=messageList method=post action="{url_change_folder}">
						<SMALL><INPUT id="changefolder" TYPE=radio NAME="folderAction" value="changeFolder" {change_folder_checked}>{lang_change_folder}</SMALL>
						<SMALL><INPUT id="movemessage" TYPE=radio NAME="folderAction" value="moveMessage" {move_message_checked}>{lang_move_message}</SMALL>
						<TT><SMALL>
						<SELECT NAME="mailbox" onChange="document.messageList.submit()">
							{options_folder}
						</SELECT></SMALL></TT>
						<noscript>
							<NOBR><SMALL><INPUT TYPE=SUBMIT NAME="moveButton" VALUE="{lang_doit}"></SMALL></NOBR>

						</noscript>
						<INPUT TYPE=hidden NAME="oldMailbox" value="{oldMailbox}">
                                        </td>
				</tr>
			</TABLE>
		</TD>
	</TR>
	{status_row}
</table>

<table border="0" width="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
				<tr>
					<td width="3%" bgcolor="#FFFFCC" align="center">
						<input name="masterSelect" type="checkbox" onClick="javascript:mark()">
					</td>
					<td width="22%" bgcolor="#FFFFCC" align="center">
						<b><a href="{url_sort_from}"><font color="black">{lang_from}</font></a></b>
					</td>
					<td width="9%" bgcolor="#FFFFCC" align="center">
						<b><a href="{url_sort_date}"><font color="black">{lang_date}</font></a></b>
					</td>
					<td width="3%" bgcolor="#FFFFCC" align="center">
						&nbsp;
					</td>
					<td bgcolor="#FFFFCC" align="center">
						<b><a href="{url_sort_subject}"><font color="black">{lang_subject}</font></a></b>
					</td>
					<td width="7%" bgcolor="#FFFFCC" align="center">
						<b>{lang_size}</b>
					</td>
				</tr>
</table>


		<table WIDTH=100% CELLPADING="0" CELLSPACING="0" bgcolor="#FFFFFF">
			{header_rows}
		</table>
</FORM>
<!-- END main -->

<!-- BEGIN status_row_tpl -->
	<tr>
		<TD valign="bottom">
			<table WIDTH=100% HEIGHT="100%" BORDER=0 CELLPADDING=1 CELLSPACING=0>
				<tr BGCOLOR="#FFFFFF">
					<td width="18%">
						{link_previous} | {link_next}
					</td>
					<td width="18%">
						&nbsp;
					</td>
					<TD align="center" width="28%">
						{message}
					</td>
					<td width="18%">
						{trash_link}
					</td>
					<td align="right" width="18%" nowrap style='font-size:9.0pt; font-family:Arial; font-weight:bold;'>
						<!-- {select_all_link} -->
						<input type=hidden name="mark_read">
						<input type=hidden name="mark_unread">
						<input type=hidden name="mark_flagged">
						<input type=hidden name="mark_unflagged">
						<input type=hidden name="mark_deleted">
						<a class="body_link" href="javascript:mark_read('mark_read')">{lang_read}</a>
						&nbsp;
						<a class="body_link" href="javascript:mark_unread('mark_unread')">{lang_unread}</a>
						&nbsp;
						<a class="body_link" href="javascript:mark_flagged('mark_flagged')">{lang_flagged}</a>
						&nbsp;
						<a class="body_link" href="javascript:mark_unflagged('mark_unflagged')">{lang_unflagged}</a>
						&nbsp;
						<a class="body_link" href="javascript:mark_deleted('mark_deleted')">{lang_delete}</a>
						&nbsp;

					</td>
				</tr>
			</table>
		</td>
	</tr>

<!-- END status_row_tpl -->

<!-- BEGIN header_row -->
<tr onmouseover="parentOn('{message_counter}')" onmouseout="parentOff('{message_counter}')" class="{row_css_class}" id="msg_tr_{message_counter}">
	<td class="{row_css_class}" width="3%" align="center">
		<input class="{row_css_class}" type="checkbox" id="msg_input_{message_counter}" name="msg[{message_counter}]" value="{message_uid}" onClick="toggleFolderRadio('{message_counter}')" {row_selected}>
	</td>
	<td class="{row_css_class}" width="24%" nowrap>
		<a class="{row_css_class}" name="link_sender" href="{url_compose}" title="{full_address}">{sender_name}</a>
		<a class="{row_css_class}" name="link_addr_image" href="{url_add_to_addressbook}"><img valign="middle" src="{phpgw_images}/sm_envelope.gif" width="10" height="8" border="0" align="absmiddle" alt="{lang_add_to_addressbook}" title="{lang_add_to_addressbook}"></a>
	</td>
	<td class="{row_css_class}" width="9%" nowrap align="center" style="color: black;">
		{date}
	</td>
	<td class="{row_css_class}" width="8%" align="middle" style="color: black;">
		{state}
<!--		<img class="{row_css_class}" src="{image_path}/{imageName}" width="16" border="0" alt="{lang_read}" title="{lang_read}">
 -->		{row_text}
	</td>
	<td class="{row_css_class}">
		<a class="{row_css_class}" name="link_subject" name="subject_url" href="{url_read_message}">{header_subject}</a>
	</td>
	<td class="{row_css_class}" width="5%">
		{size}
	</td>
</tr>
<!-- END header_row -->

<!-- BEGIN error_message -->
	<tr>
		<td bgcolor="#FFFFCC" align="center" colspan="6">
			<font color="red"><b>{lang_connection_failed}</b></font><br>
			{message}
	</td>
	</tr>
<!-- END error_message -->

<!-- BEGIN quota_block -->
	<table border="1" cellpadding="0" cellspacing="0" width="100%">
		<tr class="quota" valign="middle">
			<td width="{leftWidth}%" bgcolor="{quotaBG}" align="center" valign="middle">
				<small>{quotaUsage_left}</small>
	</td>
			<td align="center" valign="middle">
				<small>{quotaUsage_right}</small>
	</td>
		</tr>
	</table>
<!-- END quota_block -->
