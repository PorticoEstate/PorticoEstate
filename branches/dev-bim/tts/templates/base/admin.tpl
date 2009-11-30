<!-- $Id$ -->
<!-- BEGIN tts_select_options -->
    <option value="{tts_optionvalue}" {tts_optionselected}>{tts_optionname}</option>
<!-- END tts_select_options -->

<!-- BEGIN admin.tpl -->
<p><b>{lang_admin}:</b><hr><p>
   <form method="POST" action="{action_url}">
   <table border="0" align="center" cellspacing="1" cellpadding="1">
    <tr bgcolor="#EEEEEE">
     <td>{lang_mailnotification}</td>
     <td><input type="checkbox" name="usemailnotification"{mailnotification}></td>
    </tr>
    <tr bgcolor="#EEEEEE">
     <td>{lang_ownernotification}</td>
     <td>
       <select size="1" name="ownernotification">
           {tts_owneroptions}
       </select>
     </td>
    </tr>
    <tr bgcolor="#EEEEEE">
     <td>{lang_groupnotification}</td>
     <td>
       <select size="1" name="groupnotification">
           {tts_groupoptions}
       </select>
     </td>
    </tr>
    <tr bgcolor="#EEEEEE">
     <td>{lang_assignednotification}</td>
     <td>
       <select size="1" name="assignednotification">
           {tts_assignedoptions}
       </select>
     </td>
    </tr>
    <tr>
     <td colspan="3" align="center">
      <input type="submit" name="submit" value="{lang_submit}">
     </td>
    </tr>
   </table>
   </form>
