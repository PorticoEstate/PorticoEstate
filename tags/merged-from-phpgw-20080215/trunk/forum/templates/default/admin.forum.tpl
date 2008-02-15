
<table border="0" width="90%" align="center">
  <tr> 
    <td> <font size="-1"> </font><br>
      <form method="post" action="{ACTION_LINK}">
        <table border="0" width="80%" bgcolor="#9999FF">
          <tr> 
            <td colspan="2" bgcolor="#D3DCE3"> 
              <center>
                {LANG_ADD_FORUM} 
                <input type="hidden" name="forum[id]" value="{FORID}">
                <input type="hidden" name="forum[orig_cat_id]" value="{CATID}">
              </center>
            </td>
          </tr>
          <tr> 
            <input type="hidden" name="action" value="{ACTION}">
            <td>{BELONG_TO}</td>
            <td> 
              <select name="forum[cat_id]">
 <!-- BEGIN DropDown --> 
             {DROP_DOWN}             
  
<!-- END DropDown --> 
              </select>
            </td>
          <tr> 
            <td>{LANG_FORUM}</td>
            <td> 
              <input type="text" name="forum[name]" size="40" maxlength="49" value="{FORUM_NAME}">
            </td>
          </tr>
          <tr> 
            <td>{LANG_FORUM_DESC}</td>
            <td> 
              <textarea rows="3" cols="40" name="forum[descr]" virtual-wrap maxlength="240">{FOR_DESC}</textarea>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="right">
              <input type="submit" value="{BUTTONLANG}" name="submit">
            </td>
          </tr>
        </table>
      </form>
      <br>
      <center>
      </center>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
