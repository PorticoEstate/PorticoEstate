<!-- BEGIN read_body -->
<p>&nbsp;</p>
<table width="90%" border="0" cellspacing="2" cellpadding="1" align="center">
  <tr bgcolor="{BGROUND}"> 
    <td colspan="2"><a href="{MAIN_LINK}">{LANG_MAIN}</a> : <a href="{FORUM_LINK}">{LANG_CATEGORY}</a>: 
      {LANG_FORUM}</td>
  </tr>
  <tr bgcolor="{BGROUND}"> 
    <td colspan="2">[ <a href="{POST_LINK}">{LANG_NEWTOPIC}</a> | <a href="{THREADS_LINK}">{LANG_THREADS}</a> 
      | {LANG_SEARCH}]</td>
  </tr>
</table>
<table width="90%" border="0" cellspacing="2" cellpadding="1" align="center">
  {MESSAGE_TEMPLATE}
{UL_POST}
  <tr> 
    <td align="left" width="100%" valign="top"><br>
      <form method="POST" action="{READ_ACTION}" name="">
        <center>
          <input type="hidden" name="msg" value="{MSG}">
          <input type="hidden" name="action" value="{ACTION}">
          <input type="hidden" name="cat_id" value="{CAT_ID}">
          <input type="hidden" name="forum_id" value="{FORUM_ID}">
          <input type="hidden" name="thread" value="{THREAD}">
          <input type="hidden" name="depth" value="{DEPTH}">
          <input type="hidden" name="pos" value="{POST}">
          <p>&nbsp;</p>
          <table border="0" width="80%" bgcolor="#D8E0E8" cellspacing="1" cellpadding="2">
            <tr> 
              <th colspan="3" bgcolor="#D3DCE3">{LANG_REPLYTOPIC}</th>
            </tr>
            <tr bgcolor="#FFFFFF"> 
              <td width="31%">{LANG_SUBJECT}</td>
              <td colspan="2"> 
                <input type="text" size="32" maxlength="49" value="{RE_SUBJECT}" name="subject">
              </td>
            </tr>
            <tr bgcolor="#FFFFFF"> 
              <td colspan="3">{LANG_MESSAGE} 
            <tr bgcolor="#FFFFFF"> 
              <td colspan="3"> 
                <center>
                  <textarea rows="20" cols="50" name="message" value="{MESSAGE}"></textarea>
                </center>
            <tr> 
              <td colspan="2">&nbsp; </td>

              <td align="right" width="55%"> 
                <input type="submit" value="{LANG_SUBMIT}" name="submit">
              </td>
            </tr>
          </table>
        </center>
      </form>
    </td>
</table>

<!-- END read_body -->

<!-- BEGIN msg_template -->
  <tr>
    <td align="left" width="100%" valign="top">
{UL_PRE}
      <table border="0" width="100%" align="center">
       <tr bgcolor="{row_on}">
        <td>{LANG_AUTHOR}</td>
        <td>{AUTHOR}</td>
       </tr>
       <tr bgcolor="{row_on}">
        <td>{LANG_DATE}</td>
        <td>{POSTDATE}</td>
       </tr>
       <tr bgcolor="{row_on}">
        <td>{LANG_SUBJECT}</td>
        <td><a href="{SUBJECT_LINK}">{SUBJECT}</a></td>
       </tr>
       <tr bgcolor="{row_off}">
        <td colspan="2" align="left">{MESSAGE}</td> 
       </tr>
      </table>
    </td>
  </tr>
<!-- END msg_template -->
