		<p>{msg}</p>
		<!-- BEGIN open_block -->
    <table border="0" cellspacing=0 width="600">
    	<tr bgcolor={th_bg}>
    		<td colspan=2><b>{lang_cur_open_qs}</b>&nbsp;&nbsp;&nbsp;{lang_know_contrib}</td>
    	</tr>
		<!-- BEGIN open_list -->
      <tr bgcolor="{row_bg}">
        <td nowrap="nowrap" width="10%">
          <a href="{link_option}">{lang_option}</a> 
        </td>
        <td>{question_text}</td>
      </tr>
		<!-- END open_list -->
    </table>
    <br>
		<!-- END open_block -->
		{question_form}