<!-- $Id: view_product.tpl 9701 2002-03-11 11:04:57Z milosch $ -->

<!-- BEGIN view -->

<center>
<br><br>
<table width="100%" border="0" cellpadding="2" cellspacing="2">
  <tr colspan="2">
    <td align="right">
      <table border="0" cellpadding="2" cellspacing="2">
        <form method="POST" action="{action_url}">
        <tr>
          <td height="35"><div align="right"><b>{lang_id}:</b></div></td>
          <td height="35">{id}</td>
        </tr>
        <tr>
          <td height="35"><div align="right"><b>{lang_short_name}:</b></div></td>
          <td height="35">{name}</td>
        </tr>
        <tr>
          <td height="35"><div align="right"><b>{lang_description}:</b></div></td>
          <td height="35">{descr}</td>
        </tr>
        <tr>
          <td height="35"><div align="right"><b>{lang_category}:</b></div></td>
          <td height="35">{cat_name}</td>
        </tr>
        <tr>
          <td height="2">&nbsp;</td>     
        </tr>
        <tr>
          <td height="35"><div align="right"><b>{lang_price}:</b>&nbsp;{currency}</div></td>
          <td height="35">{retail}</td>
        </tr>
      </table>
    </td>
    <td align="center" valign="bottom"><img src="{image}"</td>
  </tr>
</table>

<!-- BEGIN done -->

<table width="50%" border="0" cellspacing="2" cellpadding="2">
  <tr valign="midle">
    <td height="50" align="right">{lang_piece}:&nbsp;&nbsp;<input type="text" name="piece[]" value="" size="5"></td>
    <td height="50">
      {hidden_vars}
      <input type="submit" name="Add" value="{lang_add}">
      </form>
    </td>
  </tr>
  <tr>
    <td height="50">
      <FORM method="POST" action="{done_action}">
      {hidden_vars}
      <input type="submit" name="done" value="{lang_done}">
      </form>
    </td>
  </tr>
</table>
</center>

<!-- END done -->

<!-- END view -->
