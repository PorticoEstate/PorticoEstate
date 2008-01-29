
<form name="search_frm" action="{form_action}" method="POST">
<table cellpadding="4" cellspacing="4" border="0"
 style="width: 75%; text-align: left; margin-left: auto; margin-right: auto;">
  <tbody>
    <tr>
      <td style="width: 70%; vertical-align: top;">
      <div align="center"> </div>
      <table cellpadding="2" cellspacing="2" border="0"
 style="width: 100%;">
        <tbody>
          <tr>
            <td style="vertical-align: top;">
            <table border="1" bordercolor="#000000" cellspacing="0"
 cellpadding="0" width="100%">
              <tbody>
                <tr>
                  <td>
                  <table style="width: 100%;" border="0" cellspacing="2"
 cellpadding="2" width="100%">
                    <tbody>
                      <tr>
                        <td colspan="2" bgcolor="#d3dce3">
                        <div align="center"><span
 style="font-weight: bold;">{lang_search_string}</span></div>
                        <table cellpadding="2" cellspacing="2"
 border="0" style="width: 100%;">
                          <tbody>
                            <tr>
                              <td style="vertical-align: top;"
 bgcolor="#eeeeee" width="60%" align="right" valign="middle">{lang_subject}</td>
                              <td style="vertical-align: top;"
 bgcolor="#bbbbbb"><input size="30" name="search_subject" maxlength="50"> </td>
                            </tr>
                            <tr>
                              <td style="vertical-align: top;"
 bgcolor="#eeeeee" width="60%" align="right" valign="middle">{lang_from}<br />
                              </td>
                              <td style="vertical-align: top;"
 bgcolor="#bbbbbb" width="25%"><input size="30" name="search_from" maxlength="50"></td>
                            </tr>
                            <tr>
                              <td style="vertical-align: top;"
 bgcolor="#eeeeee" width="60%" align="right" valign="middle">Body<br />
                              </td>
                              <td style="vertical-align: top;"
 bgcolor="#bbbbbb"> <input size="30" name="search_body" maxlength="50"></td>
                            </tr>
                            <tr>
                              <td style="vertical-align: top;"
 bgcolor="#eeeeee" width="60%" align="right" valign="middle">{lang_to}<br />
                              </td>
                              <td style="vertical-align: top;"
 bgcolor="#bbbbbb"> <input size="30" name="search_to" maxlength="50"></td>
                            </tr>
                            <tr>
                              <td style="vertical-align: top;"
 bgcolor="#eeeeee" width="60%" align="right" valign="middle">{lang_cc}<br />
                              </td>
                              <td style="vertical-align: top;"
 bgcolor="#bbbbbb"> <input size="30" name="search_cc" maxlength="50"></td>
                            </tr>
                            <tr>
                              <td style="vertical-align: top;"
 bgcolor="#eeeeee" width="60%" align="right" valign="middle">{lang_bcc}<br />
                              </td>
                              <td style="vertical-align: top;"
 bgcolor="#bbbbbb"> <input size="30" name="search_bcc" maxlength="50"></td>
                            </tr>
                            <tr>
                              <td style="vertical-align: top;"
 bgcolor="#eeeeee" width="60%" align="right" valign="middle">{lang_keyword}<br />
                              </td>
                              <td style="vertical-align: top;"
 bgcolor="#bbbbbb"> <input size="30" name="search_keyword" maxlength="50"></td>
                            </tr>
                          </tbody>
                        </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  </td>
                </tr>
              </tbody>
            </table>
            </td>
          </tr>
          <tr>
            <td>
            <table border="1" bordercolor="#000000" cellspacing="0"
 cellpadding="0" width="100%">
              <tbody>
                <tr>
				  <td valign="top">
					<table border="1" cellpadding="2" cellspacing="2" width="100%">
					<tr>
					<td bgcolor="#d3dce3">
						<b>{lang_search}</b>
					</td>
					</tr>
					<!-- BEGIN folder -->
					<tr>
					<td bgcolor="#eeeeee">
						<input type="checkbox" name="folder_list[]" value="{fld_value}"
 {fld_checked}>
 &nbsp;{fld_value}
					</td>
					</tr>
					<!-- END folder -->
					</table>
				  </td>
                </tr>
              </tbody>
            </table>
            </td>
          </tr>
          <tr>
            <td style="vertical-align: top;">
            <table border="1" bordercolor="#000000" cellspacing="0"
 cellpadding="0" width="100%">
              <tbody>
                <tr>
                  <td>
                  <table cellpadding="2" cellspacing="2" border="0"
 width="100%">
                    <tbody>
                      <tr>
                        <td valign="top" colspan="2" align="center"
 bgcolor="#d3dce3"><b>{lang_return_mails_during}<b><br />
                        </td>
                      </tr>
                      <tr>
                        <td valign="top" bgcolor="#cccccc"><input
 type="checkbox" name="date_on">&nbsp;&nbsp;&nbsp;{lang_on}</td>
                        <td valign="top" bgcolor="#cccccc">

					<select name="date_on_month">
		                <!-- BEGIN month_on -->
        	            <option value="{month_value}" {selected}>{month_option}</option>
             		    <!-- END month_on -->
	                </select>
    	            <select name="date_on_day">
        	            <!-- BEGIN day_on -->
            	        <option value="{day_option}" {selected}>{day_option}</option>
                	    <!-- END day_on -->
	                </select>
    	            <select name="date_on_year">
        	            <!-- BEGIN year_on -->
            	        <option value="{year_option}" {selected}>{year_option}</option>
                	    <!-- END year_on -->
	                </select>
                        </td>
                      </tr>
                      <tr>
                        <td valign="top" bgcolor="#cccccc"><input
 type="checkbox" name="date_before"> &nbsp; {lang_before}</td>
                        <td valign="top" bgcolor="#cccccc">

					<select name="date_before_month">
                    	<!-- BEGIN month_before -->
	                   <option value="{month_value}" {selected}>{month_option}</option>
    	               <!-- END month_before -->
	               </select>
    	           <select name="date_before_day">
        	           <!-- BEGIN day_before -->
            	       <option value="{day_option}" {selected}>{day_option}</option>
                	   <!-- END day_before -->
	               </select>
    	           <select name="date_before_year">
        	           <!-- BEGIN year_before -->
            	       <option value="{year_option}" {selected}>{year_option}</option>
                	   <!-- END year_before -->
	               </select>

                        </td>
                      </tr>
                      <tr>
                        <td valign="top" bgcolor="#cccccc"><input
 type="checkbox" name="date_after">&nbsp;&nbsp; {lang_after} </td>
                        <td valign="top" bgcolor="#cccccc">

				<select name="date_after_month">
                    <!-- BEGIN month_after -->
                    <option value="{month_value}" {selected}>{month_option}</option>
                    <!-- END month_after -->
                </select>
                <select name="date_after_day">
                    <!-- BEGIN day_after -->
                    <option value="{day_option}" {selected}>{day_option}</option>
                    <!-- END day_after -->
                </select>
                <select name="date_after_year">
                    <!-- BEGIN year_after -->
                    <option value="{year_option}" {selected}>{year_option}</option>
                    <!-- END year_after -->
                </select>


                        </td>
                      </tr>
                    </tbody>
                  </table>
                  </td>
                </tr>
              </tbody>
            </table>
            </td>
          </tr>
        </tbody>
      </table>
      </td>
      <td width="30%" valign="top">
      <table cellpadding="0" cellspacing="0" border="1"
 bordercolor="#000000" width="100%">
        <tbody>
          <tr>
            <td>
            <table cellpadding="2" cellspacing="2" border="0"
 style="width: 100%;">
              <tbody>
                <tr>
                  <td bgcolor="#d3dce3">
                  <div align="center"><b>{lang_check_flags}
                  </b></div>
                  </td>
                </tr>
				<!-- BEGIN flag -->
                <tr>
                  <td style="vertical-align: top;" bgcolor="#dddddd"><input
 type="checkbox" name="{flg_name}">&nbsp;{flg_value}</td>
                </tr>
				<!-- END flag -->
              </tbody>
            </table>
            </td>
          </tr>
        </tbody>
      </table>
      <br />
      </td>
    </tr>
    <tr>
      <td valign="top" colspan="2" align="center"> <input type="submit"
 value="{lang_search_button}">&nbsp;&nbsp;<input type="reset" value="{lang_clear_form_button}"> </td>
    </tr>
  </tbody>
</table>
<br />
</form>

