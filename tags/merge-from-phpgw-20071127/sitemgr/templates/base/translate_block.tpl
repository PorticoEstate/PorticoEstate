<!-- BEGIN Blocktranslator -->
<div style="border-width:2px;border-style:solid; margin:5mm;padding:5mm">
<h5>{moduleinfo}</h5>
<div align="center" style="color:red">{validationerror}</div>
<form method="POST">
<table style="border-width:2px;border-style:solid;" align="center" border ="1" rules="all" width="80%" cellpadding="5">
	<tr>
		<td width="20%">{lang_refresh}</td><td width="40%">{showlang}</td><td width="40%">{savelang}</td>
	</tr>
{standardelements}
<!-- BEGIN Version -->
<!-- <div style="border-width:2px;border-style:solid; margin:5mm;padding:5mm"> -->
<tr><td colspan="3" align="center"><b>Version {version_id} {version_state}</b></td></tr>
	{versionelements}
<!-- </div> -->
<!-- END Version -->
</table>
<input type="reset" name="reset" value="{lang_reset}">{savebutton}
<input type="hidden" name="page_id" value="{pageid}">
<input type="hidden" value="{blockid}" name="blockid" />
</form>
</div>
<!-- END Blocktranslator -->

<!-- BEGIN EditorElement -->
	<tr>
		<td>{label}</td>
		<td>{value}</td>
		<td>{form}</td>
	</tr>
<!-- END EditorElement -->