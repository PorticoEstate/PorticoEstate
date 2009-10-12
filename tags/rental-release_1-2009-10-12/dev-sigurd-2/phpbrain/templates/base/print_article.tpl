<html>
{message}
{mail_message}
<div align="center">
	<table>
		<tr>
			<td colspan=3 align=center><h2>{title}</h2></td>
		</tr>
		<tr>
			<td colspan=3 align=center>{lang_article} {art_id}&nbsp;&nbsp;&nbsp;{lang_unpublished}</td>
		</tr>
		<tr>
			<td colspan=3><hr></td>
		</tr>
		<tr>
			<td align=left><h4>{lang_topic}:</h4></td>
			<td>{topic}</td>
			<td width=200 align=right>{createdby}</td>
		</tr>
		<tr>
			<td style="width: 6em" align=left><h4>{lang_category}:</h4></td>
			<td>{links_cats}</td>
			<td align=right>{last_modif}</td>
		</tr>
		<tr>
			<td align=left><h4>{lang_keywords}:</h4></td>
			<td colspan=2>{keywords}</td>
		</tr>
		<tr>
			<td colspan=3><hr></td>
		</tr>
		<tr>
			<td colspan=3>{content}</td>
		</tr>
		<!-- BEGIN file_block -->
		<tr>
			<td colspan=3><hr></td>
		</tr>
		<tr>
			<td colspan=3><h4>{lang_attached_files}:</h4></td>
		</tr>
		<tr>
			<td colspan=3>
			<!-- BEGIN file_item_block -->
			<li>
				{file_name}: {file_comment}
			</li>
			<!-- END file_item_block -->
			</td>
		</tr>
		<!-- END file_block -->

		<!-- BEGIN related_block -->
		<tr>
			<td colspan=3><hr></td>
		</tr>
		<tr>
			<td colspan=3><h4>{lang_related_articles}:</h4></td>
		</tr>
			<td colspan=3>
			<!-- BEGIN related_article_block -->
			<li>
				({related_id}) {title_related}
			</li>
			<!-- END related_article_block -->
			</td>
		</tr>
		<!-- END related_block -->

		<!-- BEGIN show_links_block -->
		<tr>
			<td colspan=3><hr></td>
		</tr>
		<tr>
			<td colspan=3><h4>{lang_links}:</h4></td>
		</tr>
		<tr>
			<td colspan=3>
			<!-- BEGIN links_block -->
			<li>
				{title_link} ({href_link})
			</li>
			<!-- END links_block -->
			</td>
		</tr>
		<!-- END show_links_block -->
	</table>
</div>
</html>
