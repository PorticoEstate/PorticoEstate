<!-- BEGIN list -->
<h1>{lang_header}</h1>
<hr />
<div class="center">
  <table class="padding">
    <tr>
      <td class="left">{left_next_matchs}</td>
      <td class="center">&nbsp;</td>
      <td class="right">{right_next_matchs}</td>
    </tr>
  </table>
  
    <table class="padding">
      <tr>
        <td>{lang_loginid}</td>
        <td>{lang_lastname}</td>
        <td>{lang_firstname}</td>
        <td>{lang_access}</td>
      </tr>
      {rows}
    </table>
  
</div>
<form method="post" action="{actionurl}">
  <div class="center">
    <table class="padding">
      <tr>
        <td class="right">
          <form method="post" action="{accounts_url}">
            <input name="query" value="{lang_search}" />
          </form>
        </td>
      </tr>
    </table>
  </div>
<!-- END list -->

<!-- BEGIN row -->
    <tr class="padding">
      <td>{row_loginid}</td>
      <td>{row_lastname}</td>
      <td>{row_firstname}</td>
      <td>{row_access}</td>
    </tr>
<!-- END row -->

<!-- BEGIN row_empty -->
    <tr><td colspan="5" class="center">{message}</td></tr>
<!-- END row_empty -->

