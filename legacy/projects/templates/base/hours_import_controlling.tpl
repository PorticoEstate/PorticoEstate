<!-- $Id: hours_import_controlling.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"></div>
<center>
{l_statement}<br>
<br>
<b>{error}</b>
<br>
<form action="{action}" method="post" enctype="multipart/form-data">
	<input type="file" name="file" size="50" maxlength="100000" accept="csv/*"/><br/>	
	<input type="submit" name="upload" value="{l_upload}"/>
</form>
</center>