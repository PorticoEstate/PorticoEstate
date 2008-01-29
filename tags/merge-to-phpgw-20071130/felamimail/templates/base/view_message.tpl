<!-- BEGIN message_main -->
<STYLE type="text/css">
        .subjectBold
        {
        	FONT-SIZE: 12px;
        	font-weight : bold;
        	font-family : Arial;
        }

        .subject
        {
        	FONT-SIZE: 12px;
        	font-family : Arial;
        }

        .body
        {
        	FONT-SIZE: 12px;
        	font-family : Courier;
        }

        A.head_link
        {
        	color: blue;
        }
</STYLE>
<table border="0" width="100%" cellspacing="0" bgcolor="white">
<tr>
	<td>
		{navbar}
	</td>
</tr>
<tr>
	<td>
{header}
	</td>
</tr>
{rawheader}
<tr>
	<td bgcolor="white">
<div class="body">
<!-- Body Begin -->
{body}
<!-- Body End -->
</div>
	<td>
</tr>
<tr>
	<td>
		<br>
		<table border="0" cellspacing="1" width="100%" bgcolor="white">
			{attachment_rows}
		</table>
	</td>
</tr>
</table>
<!-- END message_main -->

<!-- BEGIN message_raw_header -->
<tr>
	<td bgcolor="white">
		<pre><font face="Arial" size="-1">{raw_header_data}</font></pre>
	</td>
</tr>
<!-- END message_raw_header -->

<!-- BEGIN message_navbar -->
<table border="0" cellpadding="1" cellspacing="0" width="100%">
	<tr class="th">
		<td width="50%">
			{lang_back_to_folder}:&nbsp;<a class="head_link" href="{link_message_list}">{folder_name}</a>
			&nbsp;|&nbsp;
			<a class="head_link" href="{link_compose}">{lang_compose}</a>
		</td>
		<td align="right">
			{previous_message}
			{next_message}
		</td>
	</tr>
	<tr class="th">
		<td align="right" colspan="2">
			<a class="head_link" href="{link_reply}">
			<!-- <img src="{app_image_path}/sm_reply.gif" height="26" width="28" alt="{lang_reply}" border="0"> -->
			{lang_reply}
			</a>
			&nbsp;|&nbsp;
			<a class="head_link" href="{link_reply_all}">
			<!-- <img src="{app_image_path}/sm_reply_all.gif" height="26" width="28" alt="{lang_reply_all}" border="0"> -->
			{lang_reply_all}
			</a>
			&nbsp;|&nbsp;
			<a class="head_link" href="{link_forward}">
			<!-- <img src="{app_image_path}/sm_forward.gif" height="26" width="28" alt="{lang_forward}" border="0"> -->
			{lang_forward}
			</a>
			&nbsp;|&nbsp;
			<a class="head_link" href="{link_delete}">
			<!-- <img src="{app_image_path}/sm_delete.gif" height="26" width="28" alt="{lang_delete}" border="0"> -->
			{lang_delete}
			</a>
		</td>
	</tr>
</table>
<!-- END message_navbar -->

<!-- BEGIN message_navbar_print -->
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr class="th">
		<td align="center">
			<a href="javascript:window.print()">{lang_print_this_page}</a>
		</td>
		<td align="center">
			<a href="javascript:window.close()">{lang_close_this_page}</a>
		</td>
	</tr>
</table>
<!-- END message_navbar_print -->

<!-- BEGIN message_attachement_row -->
<tr>
	<td valign="top" bgcolor={bg01}>
		<a href="{link_view}" target="_blank"><font size="2" face="{theme_font}">
		<b>{filename}</b></font></a>
	</td> 
	<td colspan="2" bgcolor={bg01}>
		<font size="2" face="{theme_font}">
		{mimetype}
		</font>
	</td>
	<td colspan="2" bgcolor={bg01}>
		<font size="2" face="{theme_font}">
		{size}
		</font>
	</td>
	<td colspan="2" bgcolor={bg01} width="10%" align="center">
		<font size="2" face="{theme_font}">
		<a href="{link_save}">{lang_save}</a>
		</font>
	</td>
</tr>
<!-- END message_attachement_row -->

<!-- BEGIN message_cc -->
<tr>
	<td class="subject" valign="top" bgcolor={bg01} width="100">
		{lang_cc}:
	</td> 
	<td class="subject" colspan="2" bgcolor={bg01}>
		{cc_data}
	</td>
</tr>
<!-- END message_cc -->

<!-- BEGIN message_organization -->
		[{organization_data}]
<!-- END message_organization -->

<!-- BEGIN message_header -->
<table border="0" cellpadding="1" cellspacing="0" width="100%">
<tr>
	<td class="subject" valign="top" width="100" bgcolor="{bg01}">
		{lang_from}:
	</td>
	<td class="subjectBold" bgcolor="{bg01}">
		{from_data}{organization_data_part}

	</td>
	<td class="subject" nowrap align=right width="120" bgcolor={bg01}>
		<a href="{link_header}">{view_header}</a>
	</td>
</tr>
<tr>
	<td class="subject" class="subject" valign="top" bgcolor="{bg01}">
		{lang_to}:
	</td> 
	<td class="subject" bgcolor="{bg01}">
		{to_data}
	</td>
	<td class="subject" nowrap align=right width="1%" bgcolor={bg01}>
		<a href="{link_printable}" target="_blank">{lang_printable}</a>
	</td>
</tr>


{cc_data_part}

<tr>
	<td class="subject" valign="top" bgcolor="{bg01}">
		{lang_date}:
	</td> 
	<td class="subject" colspan="2" bgcolor="{bg01}">
		{date_data}
	</td>
</tr>

<tr>
	<td class="subject" valign="top" bgcolor="{bg01}">
		{lang_subject}:
	</td> 
	<td class="subjectBold" colspan="2" bgcolor="{bg01}">
		{subject_data}
	</td>
</tr>
</table>
<!-- END message_header -->

<!-- BEGIN previous_message_block -->
<a class="head_link" href="{previous_url}">{lang_previous_message}</a>
<!-- END previous_message_block -->

<!-- BEGIN next_message_block -->
<a class="head_link" href="{next_url}">{lang_next_message}</a>
<!-- END next_message_block -->
