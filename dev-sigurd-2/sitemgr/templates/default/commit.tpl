<div style="margin-bottom:1cm;font-weight:bold;text-align:center;text-decoration:underline">{commit_manager} </div>
<form method="POST">
<h2 style="text-align:center">{lang_categories}</h2>
<table align="center" width="60%">
<!-- BEGIN Category -->
	<tr>
		<td width="10%"><input type="checkbox" name="cat[{catid}]" /></td>
		<td width="70%"><a href="{edit}">{category}</a></td>
		<td>{addedorremoved}</td>
	</tr>
<!-- END Category -->
</table>
<h2 style="text-align:center">{lang_pages}</h2>
<table align="center" width="60%">
<!-- BEGIN Page -->
	<tr>
		<td width="10%"><input type="checkbox" name="page[{pageid}]" /></td>
		<td width="70%"><a href="{edit}">{page}</a></td>
		<td>{addedorremoved}</td>
	</tr>
<!-- END Page -->
</table>
<h2 style="text-align:center">{lang_blocks}</h2>
<table align="center" width="60%">
<!-- BEGIN Block -->
	<tr>
		<td width="10%"><input type="checkbox" name="block[{blockid}]" /></td>
		<td width="35%"><a target="editwindow" href="{edit}">{block}</a></td>
		<td width="35%">{scope}</td>
		<td>{addedorremovedorreplaced}</td>
	</tr>
<!-- END Block -->
</table>
<p align="center">
	<input type="button" name="select_all" value="{lang_select_all}" onClick="selectAll(this.form)" />
	<input type="submit" name="btnCommit" value="{lang_commit}" />
</p>
</form>
<script>
<!--
	function selectAll(frmObj)
	{
		for (i = 0; i < frmObj.elements.length; i++)
		{
			if (frmObj.elements[i].type == 'checkbox')
			{
				frmObj.elements[i].checked = true;
			}
		}
	}
-->
</script>
