<div style="margin-bottom:1cm;font-weight:bold;text-align:center;text-decoration:underline">{module_edit}</div>
<form method="POST">
<div style="border-width:2px;border-style:solid; margin:5mm;padding:5mm">
<form method="POST">
<table>
<!-- BEGIN EditorElement -->
	<tr>
		<td>{label}</td>
		<td>{form}</td>
	</tr>
<!-- END EditorElement -->
</table>
{savebutton}{deletebutton}
<input type="hidden" value="{module_id}" name="inputmodule_id" />
<input type="hidden" value="{contentarea}" name="inputarea" />
</form>
</div>
<div align="center">{backlink}</div>
