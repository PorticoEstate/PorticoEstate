<!-- BEGIN nntp_header -->
<script language="JavaScript1.1" type="text/javascript">
function check_all()
 {
  for (i = 0; i < document.allow.elements.length; ++i)
   {
    if (document.allow.elements[i].type == "checkbox")
     {
      if (document.allow.elements[i].checked)
       {
        document.allow.elements[i].checked = false;
       }
      else
       {
        document.allow.elements[i].checked = true;
       }
     }
  }
}
</script>
<p></p>
<div align="center">
  {title}
  <br />
  <table class="padding">
    <tr>
      <td width="40%">
        <div class="center">
          <form method="post" action="{action_url}">
            {common_hidden_vars}
            <input type="text" name="query" value="{search_value}" />
            <input type="submit" name="search" value="{search}" />
            <input type="submit" name="next" value="{next}" />
          </form>
        </div>
      </td>
    </tr>
    <tr>{nml}</tr>
    <tr>{nmr}</tr>
  </table>
  <form name="allow" method="post" action="{action_url}">
    {common_hidden_vars}
    <table class="padding">
      <tr class="header">
        <td class="center">{sort_con}</td>
        <td>{sort_group}</td>
        <td class="center">{sort_active}</td>
      </tr>
<!-- END nntp_header -->

{output}

<!-- BEGIN nntp_list -->
      <tr class="bg_view">
        <td class="center">{con}</td>
        <td>{group}</td>
        <td class="center">{active}</td>
      </tr>
<!-- END nntp_list -->

<!-- BEGIN nntp_footer -->
      <tr>
        <td>&nbsp;</td>
        <td class="center"><input type="submit" name="submit" value="{lang_update}" /></td>
        <td class="center"><a href="javascript:check_all()"><img src="{checkmark}" border="0" height="16" width="21"></a></td>
      </tr>
    </table>
  </form>
</div>
<!-- END nntp_footer -->

