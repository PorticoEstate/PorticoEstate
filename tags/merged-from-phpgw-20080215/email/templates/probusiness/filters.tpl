<!-- BEGIN Sieve Mail Filters -->
<form action="{form_edit_filter_action}" method="post">
  <input type="hidden" name="filter_num" value="{filter_num}" />
    <table class="basic" align="center">
    	<tr><td colspan="4" class="header" align="center">{lang_email_filters}</td></tr>
      <tr class="bg_color2">
        <td colspan="4" class="left">
            {lang_filter_number}:&nbsp;<strong>[{filter_num}]</strong>
            &nbsp;&nbsp;
            {lang_filter_name}:&nbsp;<input size="30" name="{filter_name_box_name}" value="{filter_name_box_value}" />
        </td>
      </tr>
      <tr><td colspan="4">&nbsp;</td></tr>
      <tr class="bg_color2"><td colspan="4"><strong>{lang_if_messages_match}</strong></td></tr>
<!-- BEGIN B_matches_row -->
      <tr class="bg_view">
        <td class="center">{V_match_left_td}</td>
        <td class="center">
            <select name="{examine_selectbox_name}">
              <option value="from" {from_selected}>{lang_from}</option>
              <option value="to" {to_selected}>{lang_to}</option>
              <option value="cc" {cc_selected}>{lang_cc}</option>
              <!-- <option value="bcc" {bcc_selected}>{lang_bcc}</option>  -->
              <option value="recipient" {recipient_selected}>{lang_recipient}</option>
              <option value="sender" {sender_selected}>{lang_sender}</option>
              <option value="subject" {subject_selected}>{lang_subject}</option>
              <option value="received" {received_selected}>{lang_received_headers}</option>
              <!-- <option value="header">{lang_header}</option> -->
              <!-- <option value="size_larger">{lang_size_larger}</option> -->
              <!-- <option value="size_smaller">{lang_size_smaller}</option> -->
              <!-- <option value="allmessages">{lang_allmessages}</option> -->
              <!-- <option value="body">{lang_body}</option> -->
            </select>
        </td>
        <td class="center">
					<select name="{comparator_selectbox_name}">
						<option value="contains" {contains_selected}>{lang_contains}</option>
						<option value="notcontains" {notcontains_selected}>{lang_notcontains}</option>
					</select>
				</td>
				<td class="center"><input size="20" name="{matchthis_textbox_name}" value="{match_textbox_txt}" /></td>
      </tr>
<!-- END B_matches_row -->
    </table>
		<br />
    <table class="basic" align="center">
			<tr class="bg_color2"><td colspan="5"><strong>{lang_take_actions}</strong></td></tr>
<!-- BEGIN B_actions_row -->
      <tr class="bg_view">
        <td class="center">{V_action_widget}</td>
        <td class="center">{folder_listbox}</td>
        <td class="center">
            {lang_or_enter_text}&nbsp;<br />
				<input size="20" name="{action_textbox_name}" value="{action_textbox_txt}" />
        </td>
        <td class="center">
					<input type="checkbox" name="{stop_filtering_checkbox_name}" value="True" {stop_filtering_checkbox_checked} />
            &nbsp;{lang_stop_if_matched}
        </td>
      </tr>
<!-- END B_actions_row -->
    </table>
  <br />
    <table class="padding" align="center">
      <tr>
        <td class="center"><input type="submit" name="submit" value="{lang_submit}" /></td>
        <td class="center"><input type="reset" name="reset" value="{lang_clear}" /></td>
</form>
        <td class="center"><form action="{form_cancel_action}" method="post"><br /><input type="submit" name="cancel" value="{lang_cancel}" /></form></td>
      </tr>
    </table>
    <br />
    <hr />
    <br />
      <table class="padding" align="center">
        <tr class="header">
          <td colspan="2" class="center">
            <!-- <em>Under Development</em> -->
            <!-- this text reminds the user to submit the filter first so AM knows about it -->
            Submit the filter data to the database by clicking submit,<br />
            then you may test or apply the filter.
          </td>
        </tr>
        <tr class="center">
          <td class="bg_color1">{test_this_filter_href}</td>
          <td class="bg_color2">{apply_this_filter_href}</td>
        </tr>
      </table>
    <p>&nbsp;</p>
    {debugdata}
<!-- END Sieve Mail Filters -->

