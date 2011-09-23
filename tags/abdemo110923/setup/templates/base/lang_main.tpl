<!-- begin lang_main.tpl -->
<table cellspacing="0" style="padding: 0px; border: 1px solid #000000; width: 100%;}">
<tr class="th">
	<td colspan="{td_colspan}">
		{stage_title}
	</td>
</tr>
<tr class="row_on">
	<td colspan="{td_colspan}">
		{stage_desc}
	</td>
</tr>
<tr class="row_on">
	<td {td_align}>
		{select_box_desc}
		<form method="POST" action="lang.php">
		{hidden_var1}
		<p style="height: 150px; overflow: auto; border: 5px solid #eee; background: #eee; color: #000; margin-bottom: 1.5em;">
		{checkbox_langs}
		</p>
	</td>
	<!-- BEGIN B_choose_method -->
	<td valign="top">
		{meth_desc}
		<br><br>
		<input type="radio" name="upgrademethod" value="dumpold" checked>
		&nbsp;{blurb_dumpold}
		<br>
		<input type="radio" name="upgrademethod" value="addonlynew">
		&nbsp;{blurb_addonlynew}
		<br>
		<input type="radio" name="upgrademethod" value="addmissing">
		&nbsp;{blurb_addmissing}
	</td>
	<!-- END B_choose_method -->
</tr>
<tr class="row_on">
	<td align="center" colspan="2">
	<input type="submit" name="submit" value="{lang_install}">
	<input type="submit" name="cancel" value="{lang_cancel}">
</tr>
</table>
<!-- end lang_main.tpl -->
