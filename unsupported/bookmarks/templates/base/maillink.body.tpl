<!-- $Id$ -->
<!-- BEGIN form -->
<form method="post" action="{form_action}">
 <table border="0" bgcolor="#EEEEEE" align="center">
<!--
  <tr>
   <td>{lang_from}:</td>
   <td><strong>{from_name}</strong></td>
  </tr>
-->

  <tr>
   <td>{lang_to} <!-- To E-Mail Addresses --></td>
   <td><input type="text" name="to" size="60" maxlength="255" value="{to}"><br><small>{lang_multiple_addr}</small></td>
  </tr>

  <tr>
   <td>{lang_subject}</td>
   <td><input type="text" name="subject" size="60" maxlength="255" value="{subject}"></td>
  </tr>
 
  <tr>
   <td>{lang_message}</td>
   <td><TEXTAREA NAME="message" WRAP="physical" COLS="60" ROWS="6">{message}</TEXTAREA></td>
  </tr>

  <tr>
   <td colspan="2" align="center">
   <input type="submit" name="send" value="{lang_send}">
  </td>
 </tr>
</table>
</form>
<!-- END form -->

