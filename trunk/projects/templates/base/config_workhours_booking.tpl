<br /><br />
<center>
<table border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td colspan="2" align="center" valign="top">{message}</td>
	<tr>
<form method="POST" action="{action_url}">
	<tr>
		<td valign="top"><font color="{th_text}"><b>{workhours_booking_desc}:</b></font></td>
		<td>
			<select name="book_type">
				<option value="0"{selected_0}>{opt_workday_0_desc}</option>
				<option value="1"{selected_1}>{opt_workday_1_desc}</option>
				<option value="2"{selected_2}>{opt_workday_2_desc}</option>
				<option value="3"{selected_3}>{opt_workday_3_desc}</option>
				<option value="4"{selected_4}>{opt_workday_4_desc}</option>
				<option value="5"{selected_5}>{opt_workday_5_desc}</option>
			</select>
		</td>
	</tr>
    <tr height="50" valign="bottom">
		<td><input type="submit" name="save" value="{lang_save}"></td>
		<td align="right"><input type="submit" name="done" value="{lang_done}"></td>
	</tr>
</form>
</table>
</center>