<!-- BEGIN options_select -->
    <option value="{optionvalue}" {optionselected}>{optionname}</option>
<!-- END options_select -->

<!-- BEGIN additional_notes_row -->
  <tr>
    <td colspan="4" class="bg_view" style="color:#000000">
      <hr />
      {lang_date}: &nbsp; {value_date}<br />
      {lang_user}: &nbsp; {value_user}<br /><br />
      {value_note}
      <p></p>
    </td>
  </tr>
<!-- END additional_notes_row -->

<!-- BEGIN additional_notes_row_empty -->
  <tr><td colspan="4"><span style="font-weight:bold;">{lang_no_additional_notes}</span></td></tr>
<!-- END additional_notes_row_empty -->

<!-- BEGIN row_history -->
  <tr>
    <td class="bg_color1">{value_date}</td>
    <td class="bg_color2">{value_user}</td>
    <td class="bg_color1">{value_action}</td>
    <td class="bg_color2">{value_old_value}</td>
    <td class="bg_color1">{value_new_value}</td>
  </tr>
<!-- END row_history -->

<!-- BEGIN row_history_empty -->
  <tr class="bg_color1"><td colspan="4" class="center"><span style="font-weight:bold;">{lang_no_history}</span></td></tr>
<!-- END row_history_empty -->

<!-- BEGIN form -->
<p><span style="font-weight:bold;">{lang_viewjobdetails}</span></p>
<table class="tabletab">
  <tr>
    <th id="tab1" class="activetab" valign="top" onclick="javascript:tab.display(1);">
      <table class="basic">
        <tr>
          <td id="starttab"></td>
          <td>
            <a href="#" tabindex="0" accesskey="1" onfocus="tab.display(1);" onclick="tab.display(1); return(false);">{lang_details}</a>
          </td>
          <td id="tweentab_r"></td>
        </tr>
      </table>
    </th>
    <th id="tab2" class="activetab" onclick="javascript:tab.display(2);">
      <table>
        <tr>
          <td id="tweentab_l"></td>
          <td>
            <a href="#" tabindex="0" accesskey="2" onfocus="tab.display(2);" onclick="tab.display(2); return(false);">{lang_update}</a>
          </td>
          <td id="tweentab_r"></td>
        </tr>
      </table>
    </th>
    <th id="tab3" class="activetab" onclick="javascript:tab.display(3);">
      <table>
        <tr>
          <td id="tweentab_l"></td>
          <td>
            <a href="#" tabindex="0" accesskey="3" onfocus="tab.display(3);" onclick="tab.display(3); return(false);">history</a>
          </td>
          <td id="tweentab_r"></td>
        </tr>
      </table>
    </th>
  </tr>
</table>

<form method="post" action="{viewticketdetails_link}" enctype="multipart/form-data">
  <input type="hidden" name="ticket_id" value="{ticket_id}" />
  <input type="hidden" name="lstAssignedfrom" value="{ticket_user}" />

  <div id="tabcontent1" class="activetab">

    <table class="basic" id="tab1" style="visibility: visible; ">
      {messages}
				<tr><td>
          <table class="padding" style="border:3px solid white">
            <thead class="center">
              <tr><td colspan="4">&nbsp;<span style="font-weight:bold;">[ #{ticket_id} ] - {value_subject}</span></td></tr>
            </thead>
            <tbody>
            <tr class="left">
              <td class="bg_color1">{lang_opendate}:&nbsp;</td>
              <td class="bg_color1"><span style="font-weight:bold;">{value_opendate}</span></td>
              <td class="bg_color1">{lang_billable_hours}:&nbsp;</td>
              <td class="bg_color1"><span style="font-weight:bold;">{currency} {value_billable_hours}</span></td>
            </tr>
            <tr class="left">
              <td class="bg_color2">{lang_assignedfrom}:&nbsp;</td>
              <td class="bg_color2"><span style="font-weight:bold;">{value_owner}</span></td>
              <td class="bg_color2">{lang_billable_hours_rate}:</td>
              <td class="bg_color2"><span style="font-weight:bold;">{currency} {value_billable_hours_rate}</span></td>
            </tr>
            <tr class="left">
              <td class="bg_color1">{lang_assignedto}:&nbsp;</td>
              <td class="bg_color1"><span style="font-weight:bold;">{value_assignedto}</span></td>
              <td class="bg_color1">{lang_billable_hours_total}:</td>
              <td class="bg_color1"><span style="font-weight:bold;">{currency} {value_billable_hours_total}</span></td>
            </tr>
            <tr class="left">
              <td class="bg_color2">{lang_priority}:&nbsp;</td>
              <td class="bg_color2"><span style="font-weight:bold;">{value_priority}</span></td>
              <td class="bg_color2">{lang_effort}:&nbsp;</td>
              <td class="bg_color2">{value_effort}</td>
            </tr>
            <tr class="left">
              <td class="bg_color1">{lang_category}:&nbsp;</td>
              <td class="bg_color1"><span style="font-weight:bold;">{value_category}</span></td>
              <td class="bg_color1">{lang_type}:&nbsp;</td>
              <td class="bg_color1"><span style="font-weight:bold;">{value_type}</span></td>
            </tr>
            <tr class="left">
              <td class="bg_color2">{lang_group}:&nbsp;</td>
              <td class="bg_color2"><span style="font-weight:bold;">{value_group}</span></td>
              <td class="bg_color2">{lang_attachment}:</td>
              <td class="bg_color2">{value_attachment}</td>
            </tr>
            <tr><td class="bg_color1" colspan="4" class="center">&nbsp;</td></tr>
            <tr class="header"><td colspan="4" class="left"><span style="font-weight:bold;">{lang_details}:</span><br>{value_details}</td></tr>
            <tr><td class="bg_color2" class="tts_height" colspan="4"></td></tr>
             <tr class="header"><td colspan="4" class="left"><span style="font-weight:bold;">{lang_additional_notes}:</span></td></tr>
            {rows_notes}
            <tr><td class="bg_color1" colspan="4"><input name="cancel" type="button" value="{lang_cancel}" onClick="self.location.href='{done_url}'" /></td></tr>
            </tbody>
          </table>
        </td>
      </tr>
    </table>
  </div>
  <div id="tabcontent2" class="activetab">
    <table class="basic" id="tab2" style="visibility: visible;">
      <tr>
        <td>
          <table class="basic">
            <tr>
              <td>
                <table cellpadding="2" style="border:3px solid white">
                  <tr class="header"><td colspan="2" class="left">{lang_update}:&nbsp;</td></tr>
                  <tr class="left">
                    <td class="bg_color1">
                    	<span style="font-weight:bold;">{lang_priority}:&nbsp;</span>
                    </td>
                    <td class="bg_color1">
                      <select name="ticket[priority]">
                        {options_priority}
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="bg_color2">
                      <span style="font-weight:bold;">{lang_category}:&nbsp;</span>
                     </td>
                     <td class="bg_color2">
                      <select size="1" name="ticket[category]">
                        {options_category}
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="bg_color1">
                      <span style="font-weight:bold;">{lang_assignedto}:&nbsp;</span>
                     </td>
                     <td class="bg_color1">
                      <select size="1" name="ticket[assignedto]">
                        {options_assignedto}
                      </select>
                    </td>
                   </tr>
                   <tr>
                    <td class="bg_color2">
                      <span style="font-weight:bold;">{lang_status}:&nbsp;</span>
                     </td>
                     <td class="bg_color2">
                      <select name="ticket[status]">
                        {options_status}
                      </select>
                    </td>
                  </tr>
                  <tr class="left">
                    <td class="bg_color1">
                      <span style="font-weight:bold;">{lang_group}:&nbsp;</span>
                     </td>
                    <td class="bg_color1">
                      <select name="ticket[group]">
                        {options_group}
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="bg_color2"><span style="font-weight:bold;">{lang_billable_hours_rate}:&nbsp;</span></td>
                    <td class="bg_color2"><input name="ticket[billable_rate]" value="{value_billable_hours_rate}" /></td>
                  </tr>
                  <tr>
                  	<td class="bg_color1"><span style="font-weight:bold;">{lang_platform}:&nbsp;</span></td>
                  	<td class="bg_color1">{option_platform}</td>
                  </tr>
                  <tr class="left">
										<td class="bg_color2"><span style="font-weight:bold;">{lang_attachment}:&nbsp;</span></td>
										<td class="bg_color2"><input type="file" name="attachment" /></td>
                  </tr>
                  <tr>
										<td class="bg_color1" align="left"><span style="font-weight:bold;">{lang_effort}:&nbsp;</span></td>
										<td class="bg_color1"><input type="text" name="ticket[effort]" value="{value_effort}" /></td>
									</tr>
									<tr>
                    <td class="bg_color2"><span style="font-weight:bold;">{lang_billable_hours}:&nbsp;</span></td>
                    <td class="bg_color2"><input name="ticket[billable_hours]" value="{value_billable_hours}" /></td>
                  </tr>
                  <tr>
                  	<td class="bg_color1" style="vertical-align:top;"><span style="font-weight:bold;">{lang_details}:&nbsp;</span></td>
                  	<td class="bg_color1" colspan="2" class="center">{additonal_details_rows}<textarea rows="12" name="ticket[note]" cols="70" wrap="physical"></textarea></td>
                  </tr>
                  <tr class="bg_color2">
                    <td class="left"><input type="submit" value="{lang_ok}" name="submit" /></td>
                    <td class="left"><input name="cancel" type="button" value="{lang_cancel}" onClick="self.location.href='{done_url}'" /></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </div>
  <div id="tabcontent3" class="activetab">
    <table id="tab3" class="basic" style="visibility: visible;">
      <tr class="left"><td colspan="4">{lang_history}</td></tr>
      <tr class="top">
        <td colspan="4">
        	<table class="basic" style="border:3px solid white">
							<tr class="header">
								<td><span style="font-weight:bold;">{lang_date}</span></td>
								<td><span style="font-weight:bold;">{lang_user}</span></td>
								<td><span style="font-weight:bold;">{lang_action}</span></td>
								<td><span style="font-weight:bold;">{lang_old_value}</span></td>
								<td><span style="font-weight:bold;">{lang_new_value}</span></td>
							</tr>
					{rows_history}
				</table>
        </td>
      </tr>
    </table>
  </div>
</form>

<script language="JavaScript1.1" type="text/javascript"><!--
  var tab = new Tabs(3,'activetab','inactivetab','tab','tabcontent','','','tabpage');
  tab.init();
  // -->
</script>
<!-- END form -->

