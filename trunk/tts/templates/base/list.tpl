<!-- START list.tpl -->
		<table id="{table_id}">
			<col id="{col_prefix}_1" />
			<col id="{col_prefix}_2" />
			<col id="{col_prefix}_3" />
			<col id="{col_prefix}_4" />
			<col id="{col_prefix}_5" />
			<col id="{col_prefix}_6" />
			<col id="{col_prefix}_7" />
			<thead>
				<tr>
					<td>&nbsp;</td>
					<td>{lang_id}</td>
					<td>{lang_subject}</td>
					<td>{lang_opened}</td>
					<td>{lang_category}</td>
					<td>{lang_assignedto}</td>
					<td>{lang_openedby}</td>
					<td>{lang_status}</td>
				</tr>
			</thead>
			<tbody class="tts_rows">
			<!-- BEGIN tts_row -->
				<tr class="tts_{status_prefix}_{status_id}" ondoubleclick="window.location='{url_ticket}';">
					<td><a href="{url_close}"><img src="{img_close}" alt="{lang_close_ticket}" title="{lang_close_ticket}" /></a></td>
					<td><a href="{url_ticket}">{ticket_id}</a></td>
					<td>{subject}</td>
					<td>{opened}</td>
					<td>{cat_name}</td>
					<td>{assignedto_name}</td>
					<td>{owner_name}</td>
					<td>{status}</td>
				</tr>
			<!-- END tts_row -->
				<tr class="last">
					<td colspan="8"></td>
				</tr>
			</tbody>
		</table>
<!-- FINISH list.tpl -->
