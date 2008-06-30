<form method="POST" action="{action_url}">
<table border="0" cellpadding="0" cellspacing="0" width="50%" align="center">
	<tr>
		<td>

<table width="100%">
	<tr bgcolor="{title_color}">
		<td colspan="2">
			{title}
		</td>
	</tr>
	<tr bgcolor="{row_1_color}" align="center">
		<td colspan="2">
			&nbsp;{message}
		</td>
	</tr>
	<tr bgcolor="{censor_level_color}">
		<td>
			{censor_level_label}:
		</td>
		<td>
			<select name="censor_level" STYLE="width: 100%">
			{censor_level_options}
			</select>
		</td>
	</tr>
	<tr bgcolor="{override_enabled_color}">
		<td>
			{override_enabled_label}:
		</td>
		<td>
			<input type="checkbox" {override_enabled} name="override_enabled" value="1">
		</td>
	</tr>
	<tr bgcolor="{image_source_color}">
		<td>
			{image_source_label}:
		</td>
		<td>
			<select name="image_source" STYLE="width: 100%">
			{image_source_options}
			</select>
		</td>
	</tr>
	<tr bgcolor="{remote_enabled_color}">
		<td>
			{remote_enabled_label}:
		</td>
		<td>
			<input type="checkbox" {remote_enabled} name="remote_enabled" value="1">
		</td>
	</tr>
	<tr bgcolor="{filesize_color}">
		<td>
			{filesize_label}:
		</td>
		<td>
			<input type="text" name="filesize" value="{filesize}" size=7 maxlength=7 STYLE="width: 100%">
		</td>
	</tr>
	<tr bgcolor="{row_2_color}">
		<td colspan="2">
			&nbsp;
		</td>
	</tr>
	<tr bgcolor="{title_color}">
		<td colspan="2">
			&nbsp;
		</td>
	</tr>
	<tr colspan="2">
		<td>
			<input type="submit" name="submit" value="{submit}">
			<input type="reset" name="reset" value="{reset}">
			<input type="submit" name="submit" value="{done}">
		</td>
	</tr>
</table>

		</td>
	</tr>
</table>
</form>
