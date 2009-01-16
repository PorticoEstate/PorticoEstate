<!-- BEGIN list -->
    <table class="basic" align="center">
      <tr>
        {rows}
        <td>
          <div class="center">
            <table>
              <thead>
              	<tr valign="bottom">
                	<td class="left" colspan="2">
                 	 {lang_last_x_logins}
                	</td>
                	<td class="center" colspan="2">{showing}</td>
                	<td class="right">
                  	<table>
                    	<tr>{nextmatchs_left}&nbsp;{nextmatchs_right}</tr>
                  	</table>
                	</td>
              	</tr>
              </thead>
              <tr class="bg_color1">
                <td width="10%">{lang_loginid}</td>
                <td width="15%">{lang_ip}</td>
                <td width="20%">{lang_login}</td>
                <td width="30%">{lang_logout}</td>
                <td>{lang_total}</td>
              </tr>
              {rows_access}
              <tr class="th"><td colspan="5" class="left">{footer_total}</td></tr>
              <tr class="th"><td colspan="5" class="left">{lang_percent}</td></tr>
            </table>
          </div>
        </td>
      </tr>
    </table>
<!-- END list -->

<!-- BEGIN row -->
              <tr class="{tr_class}">
                <td>{row_loginid}</td>
                <td>{row_ip}</td>
                <td>{row_li}</td>
                <td>{row_lo}&nbsp;</td>
                <td>{row_total}&nbsp;</td>
              </tr>
<!-- END row -->

<!-- BEGIN row_empty -->
              <tr><td class="center" colspan="5">{row_message}</td></tr>
<!-- END row_empty -->

