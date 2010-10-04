<!-- BEGIN setup_demo -->
{errors}
<h1>{title}</h1>
<p>{description}</p>
<h2>{detailadmin}</h2>
<div id="setup_accounts">
<form method="post" action="{action_url}">
	<table border="0">
	<tr>
		<td>{adminusername}</td>
		<td><input type="text" name="username" value="{val_username}"></td>
	</tr>
	<tr>
		<td>{adminfirstname}</td>
		<td><input type="text" name="fname" value="{val_fname}"></td>
	</tr>
	<tr>
		<td>{adminlastname}</td>
		<td><input type="text" name="lname" value="{val_lname}"></td>
	</tr>
	<tr>
		<td>{adminpassword}</td>
		<td><input type="password" name="passwd"></td>
	</tr>
	<tr>
		<td>{adminpassword2}</td>
		<td><input type="password" name="passwd2"></td>
	</tr>
	<tr>
		<td><input type="submit" name="submit" value="{lang_submit}"> </td>
		<td><input type="submit" name="cancel" value="{lang_cancel}"> </td>
	</tr>
	</table>
</form>
</div>
<!-- END setup_demo -->
