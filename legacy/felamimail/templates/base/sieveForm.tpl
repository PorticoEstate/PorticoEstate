<!-- BEGIN header -->
<center>
<i>Scripts available for this account.</i><br>
<br>
<table border="0" width="95%">
<!--	<tr bgcolor=#dddddd>
		<td>
			Script (1): 
		</td>
		<td> 
			sample1 
		</td>
		<td>
			<a href=test.php?action=get&script=sample1>View/Edit Script</a>
		</td>
		<td>
			<a href=test.php?action=del&script=sample1>Delete Script</a>
		</td>
		<td>
			<a href=test.php?action=act&script=sample1>Activate Script</a>
		</td>
	</tr> -->
	{scriptrows}
</table><br>
<hr width="95%">
<form method=post action="{formAction}">
<table border=0>
	<tr>
		<td><i>Editing script "{editScriptName}"</i></td>
		<td align="right"><i><a href="{link_newScript}">Create new script</a></i></td>
	</tr>
	<tr>
		<td bgcolor=#d0d0d0>Script name</td>
		<td><input type=text name=scriptName value="{editScriptName}"></td>
	</tr>
	<tr>
		<td colspan=2 bgcolor=#d0d0d0>Script</td>
	</tr>
	<tr>
		<td colspan=2>
			<textarea name=scriptContent cols=90 rows=20>{scriptContent}</textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type=submit name="Send Script" value="saveScript"></td>
	</tr>
</table>
</form>
</center>
<!-- END header -->

<!-- BEGIN scriptrow -->
<tr>
	<td class="body">
		Script {scriptnumber}
	</td>
	<td class="body" align="right">
		{scriptname}
	</td>
	<td class="body" align="right">
		<a href={link_deleteScript}>{lang_delete}</a>
	</td>
	<td class="body" align="right">
		<a href={link_editScript}>{lang_edit}</a>
	</td>
	<td class="body" align="right">
		<a href={link_activateScript}>{lang_activate}</a>{active}
	</td>
</tr>
<!-- END scriptrow -->