<p class=action>{lang_cal_action}<br />
<div class="center">
<hr noshade class="basic" size="1">
</div>

		<FORM {enctype} action="{action_url}" method="post">
      <TABLE class="basic" align="center">

<!-- BEGIN filename -->
	  		<TR>
	    		<TD>{lang_csvfile}</td>
				<td><INPUT NAME="csvfile" SIZE=30 TYPE="file" VALUE="{csvfile}" /></td>
			</tr>
			<tr>
				<td>{lang_fieldsep}</td>
				<td><input name="fieldsep" size=1 value="{fieldsep}" /></td>
			</tr>
			<tr><td>&nbsp;</td>
				<td><input NAME="convert" TYPE="submit" VALUE="{submit}" /></TD>
			</TR>
<!-- END filename -->

<!-- BEGIN fheader -->
			<tr>
				<td><b>{lang_csv_fieldname}</b></td>
				<td><b>{lang_cal_fieldname}</b></td>
				<td><b>{lang_translation}</b></td>
			</tr>
<!-- END fheader -->

<!-- BEGIN fields -->
			<tr>
				<td>{csv_field}</td>
				<td><SELECT name="cal_fields[{csv_idx}]">{cal_fields}</select></td>
				<td><input name="trans[{csv_idx}]" size=60 value="{trans}" /></td>
			</tr>
<!-- END fields -->

<!-- BEGIN ffooter -->
			<tr>
				<td rowspan=2 class="middle">
				  <br /><INPUT NAME="convert" TYPE="submit" VALUE="{submit}" />
				</TD>
				<td colspan=2><br />
					{lang_start}<INPUT name="start" type="text" size="5" value="{start}" /> &nbsp; &nbsp;
					{lang_max}<INPUT name="max" type="text" size="3" value="{max}" /><td>
			</tr>
			<tr>
				<td><INPUT name="debug" type="checkbox" value="1" checked /> {lang_debug}</td>
			</TR>
			<tr><td colspan=3>&nbsp;<p>
				{help_on_trans}
			</td></tr>			
<!-- END ffooter -->

<!-- BEGIN imported -->
			<tr>
				<td colspan=2 class="center">
					{log}<p>					
					{anz_imported}					
				</td>
			</TR>
<!-- END imported -->

		</TABLE>
		{hiddenvars}</form>
