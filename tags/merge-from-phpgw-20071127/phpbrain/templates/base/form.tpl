    <form action="{form_url}" name="kb_form" method="POST">
  	<!-- BEGIN hidden_var -->
    	<input type="HIDDEN" name="{hidden_name}" value="{hidden_val}"> 
  	<!-- END hidden_var -->
      <table border="0" width="100%" cellspacing="0" class="navbg">
        <tr>
          <td>&nbsp;</td>
          <td>{lang_title}</td>
        </tr>
        <tr>
          <td><b>{lang_input_descr}</b></td>
          <td>
            <textarea name="comment" cols="60" rows="3" class="search"></textarea> 
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input type="submit" value="{lang_submit_val}" class="search"></td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
      </table>
    </form>
