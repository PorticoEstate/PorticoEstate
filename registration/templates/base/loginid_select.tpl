<!-- BEGIN form -->
<center>{errors}</center>

<!-- BEGIN input -->
<form action="{form_action}" method="POST">
 <table border="0" width="40%" align="center">
	<tr >
		<td>{lang_domain}</td>
		<td>
			<select name="logindomain" id="logindomain" class="inputbox">
				{domain_options}
			</select>
		</td>
	</tr>
  <tr>
   <td>{lang_username}</td>
   <td><input name="r_reg[loginid]" value="{value_username}"></td>
  </tr>
 
  <tr>
   <td colspan="2"><input type="submit" name="submit" value="{lang_submit}"></td>
  </tr>
 </table>
</form>
<!-- END input -->
<!-- END form -->
