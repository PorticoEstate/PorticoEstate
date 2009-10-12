<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<h1>{title}</h1>
	<b>{error}</b>
	<!-- END header -->
	<!-- BEGIN body -->
	<h2>{lang_news_admin}</h2>
	<table border="0" align="center">
		<tr bgcolor="{row_on}">
			<td>{lang_newsletter_from_name}:</td>
			<td><input name="newsettings[newsletter_from_name]" value="{value_newsletter_from_name}" size="78"></td>
		</tr>
		<tr bgcolor="{row_off}">
			<td>{lang_newsletter_from_email}:</td>
			<td><input name="newsettings[newsletter_from_email]" value="{value_newsletter_from_email}" size="78"></td>
		</tr>
		<tr bgcolor="{row_on}">
			<td>{lang_newsletter_to}:</td>
			<td><input name="newsettings[newsletter_to]" value="{value_newsletter_to}" size="78"></td>
		</tr>
		<tr bgcolor="{row_off}">
			<td>{lang_newsletter_header_html}:</td>
			<td>
				<textarea name="newsettings[newsletter_header_html]" rows="5" cols="78">{value_newsletter_header_html}</textarea>
			</td>
		</tr>
		<tr bgcolor="{row_on}">
			<td>{lang_newsletter_footer_html}:</td>
			<td>
				<textarea name="newsettings[newsletter_footer_html]" rows="5" cols="78">{value_newsletter_footer_html}</textarea>
			</td>
		</tr>
		<tr bgcolor="{row_off}">
			<td>{lang_more_link_url}:</td>
			<td>
				<input type="text" name="newsettings[more_link_url]" size="78" value="{value_more_link_url}">
			</td>
		</tr>
		<!-- END body -->
		<!-- BEGIN footer -->
		<tr>
			<td colspan="2" align="right">
				<input type="submit" name="cancel" value="{lang_cancel}">
				<input type="submit" name="submit" value="{lang_submit}">
			</td>
		</tr>
	</table>
</form>
<!-- END footer -->
