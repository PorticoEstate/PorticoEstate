<div style="margin-bottom:1cm;font-weight:bold;text-align:center;text-decoration:underline">{translation_manager}</div>
<table style="border-width:2px;border-style:solid;" border='1' align='center' rules="all" width='70%' cellpadding='0'>
	<tr>
		<td colspan={colspan} align='center'><a href="{translate_site_content}">{lang_site_content}</a></td>
	</tr>
	<tr>
		<td><u>{lang_catname}</u></td>
		<!-- BEGIN sitelanguages -->
		<td>{sitelanguage}</td>
		<!-- END sitelanguages -->
	</tr>
	<!-- BEGIN CategoryBlock -->
	<tr bgcolor='dddddd'>
		<td align='left' style='font-weight:bold' bgcolor='dddddd'>
		{category}
		</td>
		<!-- BEGIN langexistcat -->
		<td align="center">{catexistsinlang}</td>
		<!-- END langexistcat -->
		<td align='center' bgcolor='dddddd' valign="center" width='5%'>{translatecat}</td>
	</tr>
	<!-- BEGIN PageBlock -->
	<tr>
		<td align='left' style="padding-left:1cm">
		{page}
		</td>
		<!-- BEGIN langexistpage -->
		<td align="center">{pageexistsinlang}</td>
		<!-- END langexistpage -->
		<td align='center' valign="center" width='5%'>{translatepage}</td>
	</tr>
	<!-- END PageBlock -->
	<!-- END CategoryBlock -->
</table>
