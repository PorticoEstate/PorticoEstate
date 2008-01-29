<!-- BEGIN setup_demo -->
{errors}
<table cellspacing="0" cellpadding="2" style="{border: 1px solid #000000; width: 100%}">
  <tr>
    <td>{description}</td>
  </tr>
  <tr>
    <td align="left" bgcolor="#486591"><font color="#fefefe">{detailadmin}</td>
  </tr>
  <tr>
    <td>
      <form method="POST" action="{action_url}">
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
            <td>{create_demo_accounts}</td>
            <td><input type="checkbox" name="create_demo" {checked_demo}></td>
          </tr>
          <tr>
            <td><input type="submit" name="submit" value="{lang_submit}"> </td>
            <td><input type="submit" name="cancel" value="{lang_cancel}"> </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
<!-- END setup_demo -->
