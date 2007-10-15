<!-- BEGIN sendfax -->


<!-- BEGIN header -->
<br>
<center><h2>{fax}</h2></center>
<script language=javascript>

 function fc()
  {
    document.fax_filter.action = '{fc_url}';
    document.fax_filter.submit();
  }
  
 function preview()
 {
   document.fax_filter.action = '{preview_url}';
   document.fax_filter.submit();
 }

 tst = 0 ;
 function check_all()
    { 
      if (tst == 1)
      {for (i = 0; i <document.fax_filter.elements.length; i++) 
         { document.fax_filter.elements[i].checked = false; } 
       tst = 0;   
      }
      else
      {for (i = 0; i<document.fax_filter.elements.length; i++) 
         { document.fax_filter.elements[i].checked = true;} 
       tst = 1;
      }
     }
	 
 function submit_if(vv)
    {
     box = document.fax_filter.selected_file;
	 // alert(box.selectedIndex);
	 // lgth = box.options.length ; 
     if ((box.selectedIndex != -1) &&
	     (box.selectedIndex != 0) &&
	     (box.options[box.selectedIndex].selected == true))
       {
	   
	   //	if (vv == 'file_show')
		 // { document.fax_filter.action = '{show_url}' ; }
        document.fax_filter.attach_options.value=vv;
        document.fax_filter.submit();
       }
     }
</script>
<!-- END header -->


<!-- BEGIN categories_header -->
<form enctype='multipart/form-data' name='fax_filter' action='{url_view}' method='post'>
<input type='hidden' name='user_login' value='{user_login}'>
<table border='0' cellspacing=1>
<tr>
 <th width='200' align=right>{l_cat}</th>
 <th align=left>
  <select name='cat_id' onChange='this.form.submit()'> 
   <option value='none' {none}>  </option>
   <option value="all" {all}>{l_all_cat}</option>  
<!-- END categories_header -->


<!-- WARNING!: categories bodies are writen from fax/class.manager.inc.php -->


<!-- BEGIN categories_footer -->
  </select></th>
 <th  align=right>{l_filter}</th>
 <th  align=left>
  <select name='filter' onChange='this.form.submit()'>
   <option value='all' {show_all}>{l_all_add}</option>
   <option value="private" {private}>{l_private}</option>
   <option value="personal" {personal}>{l_mine}</option>
 </select></th>
</tr>
<input type='hidden' name='old_query' value='{old_query}'>
<input type='hidden' name='old_cat' value='{old_cat}'>
<input type='hidden' name='old_filter' value='{old_filter}'>
<!-- END categories_footer -->


<!-- BEGIN contacts_header -->
<br>
<tr bgcolor={td_color}>
 <td>{l_contact}</td><td>{l_company}</td><td>{l_city}</td><td>{l_faxnumber}</td>
 <td><input type='checkbox' onClick='javascript:check_all();' name='ca'> {type}</td>
<tr>
<!-- END contacts_header -->


<!-- BEGIN contacts_body -->
<tr bgcolor={bgcolor}>
 <td>{fn}</td><td>{org_name}</td>
 <td>{adr_one_locality}</td><td>{tel_fax}</td><td>
  <input type='checkbox' name='item_{id}' {it_sel}>
</tr>
<input type='hidden' name='operand' value='{operand}'>
<!-- END contacts_body -->


<!-- BEGIN contacts_footer -->
<tr bgcolor={td_color}>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
</tr>
<input type='hidden' value='{contacts}' name='contacts'>
<!-- END contacts_footer -->


<!-- BEGIN faxdata_search -->
<tr>
 <th  align=right>{l_faxnumber}:</th>
 <th  align=left> <input type='text' value='{faxnumber}' name='faxnumber'></th>
 <th  align=right><input type='submit' name='search' value='{l_search}'></th>
 <th  align=left><input type='text' name='query' value=''></th> 
</tr>
<!-- END faxdata_search -->


<!-- BEGIN faxdata_searchonly -->
<tr>
<th> &nbsp; </th>
 <th> &nbsp; </th>
 <th  align=right><input type='submit' name='search' value='{l_search}'></th>
 <th  align=left><input type='text' name='query' value=''></th>
</tr>
<!-- END faxdata_searchonly -->


<!-- BEGIN faxdata_add -->
<tr>
 <th  align=right>{l_recipient}:</th>
 <th  align=left> <input type='text' value='{recipient}' name='recipient'></th>
</tr>
<tr>
 <th  align=right>{l_company}:</th>
 <th  align=left> <input type='text' value='{company}' name='company'></th>
</tr>
<tr>
 <th  align=right>{l_location}:</th>
 <th  align=left> <input type='text' value='{location}' name='location'></th>
</tr>
<!-- END faxdata_add -->


<!-- BEGIN faxdata_body --> 
<tr>
 <th  align=right>{l_regarding}:</th>
 <th  align=left> <input type='text' value='{regarding}' name='regarding'></th>
</tr>
<tr>
 <th  align=right>{l_comment}:</th>
 <th  align=left> <input type='text' value='{comments}' name='comments'></th>
</tr>
<tr>
 <th  align=right>{l_mnotify}:</th>
 <th  align=left>
  <select name='notify'>
   <option value='N' {mnot_check_no}>{l_no}
   <option value='Y' {mnot_check_yes}>{l_yes}
  </select>
 </th>
</tr>
<!-- END faxdata_body -->


<!-- BEGIN add_cover -->
<tr>
 <th  align=right>{l_ucover}:</th>
 <th  align=left>
  <select name='addcover' onChange='this.form.submit()'>  
   <option value='N' {cover_sel_no}>{l_no}
   <option value='Y' {cover_sel_yes}>{l_yes}
  </select></th>
</tr>
<!-- END add_cover -->


<!-- BEGIN faxdata_cover_header -->
<tr>
 <th  align=right>{l_cover}:</th>
 <th  align=left>
  <select name='cover' > 
<!-- END faxdata_cover_header -->


<!-- BEGIN faxdata_cover_row -->
  <option value='{cover_path}' {sel}>{cover_name}
<!-- END faxdata_cover_row -->


<!-- BEGIN faxdata_cover_footer -->
  </select> 
 <input type=submit value={l_preview} onclick='javascript:preview()'> 
 </th>
</tr>
<!-- END faxdata_cover_footer -->


<!-- BEGIN attachment_header -->
</table> 
<br><center><b>{l_upld_err}</b></center>
<br>
<table border=0 cellspacing=0 cellpadding=2>
<tr>
<td bgcolor={bg01}> &nbsp;</td>
<td align=center colspan=2 bgcolor={bg02}> {l_att_descr}</td>
</tr>
<tr>
 <!-- ToDo: upload if and only if there is something in the text field? -->
 <td bgcolor={bg01} align=right> <input type='file' name='userfile' > <input type='submit' name='add_file' value='{l_addfile}' ></td> 
 <td bgcolor={bg02} rowspan=3 align=center>  <select name='selected_file' size=10> <option value='FAKE'>{l_fake}
<!-- END attachment_header -->


<!-- BEGIN attachment_row -->
   <option value='{fn_fp}'>{fn_n}
  </option>
<!-- END attachment_row -->


<!-- BEGIN attachment_footer -->
  </select></td>
 <td bgcolor={bg02}> <input width='100' type='button' value='{l_u}' onclick='javascript:submit_if("file_up")'> &nbsp; </td>
</tr>
<tr>
<td bgcolor={bg01} align=right > <input type='submit' name='bottone' value='{l_storage}' onclick='javascript:fc()'></td> 
 <td bgcolor={bg02}> <input type='button' value='{l_d}' onclick='javascript:submit_if("file_down")'> &nbsp;</td>
</tr>
<tr>
 <td bgcolor={bg03} align=center><textarea name='faxtext' rows='6' cols='60'>{l_msg}</textarea></td>
<td bgcolor={bg02}> &nbsp; </td>
<tr>
<tr>
 <td bgcolor={bg03} align=right> <input type='submit' name='add_text' value='{l_addtext}'></td>
 <td bgcolor={bg02} align=center> <input type='button' value='{l_show}' onclick='javascript:submit_if("file_show")'><input type='button' value='{l_delete}' onclick='javascript:submit_if("file_del")'></td></td>
 <td bgcolor={bg02} > &nbsp; </td> 
</tr>
<input type='hidden' name="MAX_FILE_SIZE" value='5000000'>
<input type='hidden' name='filename_data' value='{filename_data}'>
<input type='hidden' name='filename' value='{filename}'>
<input type='hidden' name='filename_real' value='{filename_real}'>
<input type='hidden' name='attach_options' value=''>
<!-- END attachment_footer -->


<!-- BEGIN submit_button -->
<tr>
 <th  colspan=3>&nbsp;</th>
</tr>
<tr>
 <th  align=right><input type='submit' name='action1' value='{l_send}'></th>
 <th  colspan=2> &nbsp;</th>
</tr>
</table>
</form>
<!-- END submit_button -->


<!-- BEGIN show_preview -->

<script language=javascript>
   function openwindowlink() 
    {
     newwin = window.open('{show_link}','pippo','width=600,height=500')
    }
    openwindowlink();
</script>

<!-- 
<a href='{show_link}'> <img src='{dl_pic}' border=0></a>
<br>
<a href="javascript:history.go(-1)" onMouseOver="self.status=document.referrer;return true">{l_goback}</a>
 -->

<!-- END show_preview -->


<!-- BEGIN no_data -->
<br><center><b>{l_sorry}</b></center><br>
<!-- END no_data -->


<!-- BEGIN message -->
 <p>
  <center>{msg}</center>
 </p>
<!-- END message -->

<!-- BEGIN hidden_header -->
<form name='error_page' action='{back_url}' method=POST>
<!-- END hidden_header -->

<!-- BEGIN err_message -->
<center>
 <br>{errmessage}
</center>
<!-- END err_message -->

<!-- BEGIN hidden_data -->
<input type=hidden name='{hidden_name}' value='{hidden_value}'>
<!-- END hidden_data -->


<!-- BEGIN hidden_footer -->
<br>
<center><input type=submit value='{l_goback}'></center>
</form>
<!-- END hidden_footer -->


<!-- BEGIN fax_sent -->
<center> {msg_ok} </center>
<br>
<center>
 <form name='ok' action='{submit_url}' method='post'><input type='submit' value = 'OK'></form>
</center>
<!-- END fax_sent -->


<!-- END sendfax -->
