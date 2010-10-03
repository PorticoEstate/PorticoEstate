<!-- $Id: delete.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"</div>
<center>
<table border="0" with="65%">
<form method="POST" action="{action_url}">
	<tr colspan="2">
		<td align="center">{deleteheader}</td>
	</tr>
	<tr>
		<td align="center">{lang_subs}</td>
		<td align="center">{subs}</td>
	</tr>
	<tr>
		<td><input type="submit" name="yes" value="{lang_yes}"></td>
		<td><input type="submit" name="no" value="{lang_no}"></td>
	</tr>
</form>
</table>
</center>
