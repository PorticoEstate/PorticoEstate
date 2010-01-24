<!-- BEGIN Block -->
<!-- BEGIN Moduleeditor -->
{standalone}
<div style="border-width:2px;border-style:solid; margin:5mm;padding:5mm">
<h4>{moduleinfo}: {description}</h4>
<span style="color:red">{validationerror}</span>
<form method="POST">
<table>
{standardelements}
</table>
<!-- BEGIN Version -->
<div style="border-width:2px;border-style:solid; margin:5mm;padding:5mm">
<b>Version {version_id}</b><select name="inputstate[{version_id}]">{state}</select><input type="submit" value="{deleteversion}" name="btnDeleteVersion[{version_id}]" />
<table>
	{versionelements}
</table>
</div>
<!-- END Version -->
<input type="hidden" value="{blockid}" name="inputblockid" />
<input type="submit" value="{savebutton}" name="btnSaveBlock" /> {savelang}
<input type="submit" value="{deletebutton}" name="btnDeleteBlock" />
<input type="submit" value="{createbutton}" name="btnCreateVersion" />
{donebutton}
</form>
</div>
<!-- END Moduleeditor -->

<!-- BEGIN Moduleview -->
<div style="border-width:2px;border-style:solid; margin:5mm;padding:5mm">
<h4>{moduleinfo}: {description}</h4>
<table>
<!-- BEGIN ViewElement -->
	<tr>
		<td>{label}</td>
		<td>{value}</td>
	</tr>
<!-- END ViewElement -->
</table>
</div>
<!-- END Moduleview -->
<!-- END Block -->

<!-- BEGIN EditorElement -->
	<tr>
		<td>{label}</td>
		<td>{form}</td>
	</tr>
<!-- END EditorElement -->
