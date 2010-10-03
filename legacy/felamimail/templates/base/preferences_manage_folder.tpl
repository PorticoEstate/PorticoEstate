<!-- BEGIN main -->
<table width="100%" border="0">
	<tr>
		<td width="1%" rowspan="2">
			<form action="{form_action}" method="post" name="folderList">
			<table border="1" width="100%" cellpadding=2 cellspacing=0>
				<caption>{lang_folder_list}</caption>
				<tr>
					<td align="center">
						<select size="30" name="foldername" onchange="document.folderList.submit()">
							{select_rows}
						</select>
					</td>
					<noscript>
					<td align="right">
						<input type="submit" value="{lang_select}" name="selectFolder">
					</td>
					</noscript>
				</tr>
			</table>
			</form>
		</td>
		<td valign="top">
			<table border="1" width="100%" cellpadding=2 cellspacing=0>
				<caption>{lang_folder_settings}</caption>
				<tr>
					<td width="150"align="left">
						{lang_imap_server}
					</td>
					<td align="center">
						<b>{imap_server}</b>
					</td>
				</tr>
				<tr>
					<td width="150"align="left">
						{lang_folder_name}
					</td>
					<td align="center">
						<b>{folderName}</b>
					</td>
				</tr>
				<tr>
					<td width="150"align="left">
						{lang_folder_status}
					</td>
					<td align="center">
						<form action="{form_action}" method="post" name="subscribeList">
						<input type="radio" name="folderStatus" value="subscribe" onchange="document.subscribeList.submit()" id="subscribed" {subscribed_checked}>
						<label for="subscribed">{lang_subscribed}</label> 
						<input type="radio" name="folderStatus" value="unsubscribe" onchange="document.subscribeList.submit()" id="unsubscribed" {unsubscribed_checked}>
						<label for="unsubscribed">{lang_unsubscribed}</label> 
						<noscript><input type="submit" value="{lang_update}" name="un_subscribe"></noscript>&nbsp;
						</form>
					</td>
				</tr>
				<tr>
					<td width="150"align="left">
						{lang_rename_folder}
					</td>
					<td align="center">
						<form action="{form_action}" method="post" name="renameMailbox">
						<input type="text" size="30" name="newMailboxName" value="{mailboxNameShort}" onchange="document.renameMailbox.submit()">
						<noscript><input type="submit" value="{lang_rename}" name="renameMailbox"></noscript>&nbsp;
						</form>
					</td>
				</tr>
				<tr>
					<td width="150"align="left">
						{lang_create_subfolder}
					</td>
					<td align="center">
						<form action="{form_action}" method="post" name="createSubFolder">
						<input type="text" size="30" name="newSubFolder" onchange="document.createSubFolder.submit()">
						<noscript><input type="submit" value="{lang_create}" name="createSubFolder"></noscript>&nbsp;
						</form>
					</td>
				</tr>
				<tr>
					<td width="150"align="left">
						&nbsp;
					</td>
					<td align="center">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td width="150"align="left">
						{lang_delete_folder}
					</td>
					<td align="center">
						<form action="{form_action}" method="post" name="deleteFolder">
						<input type="submit" value="{lang_delete}" name="deleteFolder">
						</form>
					</td>
				</tr>
			</table>
		</td>
	</tr>
<!-- 	<tr>
		<td>
			<table border="1" width="100%">
				<tr>
					<td width="100"align="left">
						{lang_quota_status}
					</td>
					<td align="center">
						<table width="100%" border="1">
							<tr>
								<td colspan="2">
									Storage Limit<br>
								</td>
							</tr>
							<tr>
								<td width="50%">
									STORAGE usage level is: 
								</td>
								<td width="50%">
									{storage_usage}
								</td>
							</tr>
							<tr>
								<td>
									STORAGE limit level is: 
								</td>
								<td>
									{storage_limit}
								</td>
							</tr>
							<tr>
								<td colspan="2">
									Message Limit<br>
								</td>
							</tr>
							<tr>
								<td>
									MESSAGE usage level is: 
								</td>
								<td>
									{message_usage}
								</td>
							</tr>
							<tr>
								<td>
									MESSAGE limit level is: 
								</td>
								<td>
									{message_limit}
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr> -->
</table>

<!-- END main -->

<!-- BEGIN select_row -->
				<option value="{folder_value}" {selected}>({subscribed}) {folder_name}</option>
<!-- END select_row -->

