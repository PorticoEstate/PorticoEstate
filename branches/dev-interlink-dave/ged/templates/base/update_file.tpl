<table border="0" cellpadding="5" width="100%">
<tbody>
<tr>
<td colspan="2">
<table>
<tr>
<td>
<h2>File info</h2>
<form enctype="multipart/form-data" action="{action_update}" method="post">
<input type="hidden" name="{element_id_field}" value="{element_id_value}">
</td>
</tr>			
<tr>
<td style="vertical-align: top; width: 50px;">{lang_name} :<br>
</td>
<td style="vertical-align: top;"><input type="text" default_class="{input_default_class}"  focused_class="{input_active_class}" name="{file_name_field}" value="{file_name_value}" size="40" />
</td>
</tr>
<tr>
<!-- BEGIN power_block -->
<tr>
<td style="vertical-align: top; width: 50px;">{lang_type} :<br>
</td>
<td style="vertical-align: top;">{select_type} (CARE : if you change this field with a chrono type, reference will be overriden)
</td>
</tr>
<td>{lang_reference} :</td>
<td>  
  <input type="text" default_class="{input_default_class}"  focused_class="{input_active_class}" name="{referenceq_field}" value="{new_reference}" size="40" maxlength="40"/>
</td>
</tr>
<!-- END power_block -->
<tr>
<td style="vertical-align: top; width: 50px;">{lang_description}<br>
</td>
<td style="vertical-align: top;"><textarea name="{file_description_field}" name="description" rows="10" cols="50" wrap="off" >{file_description_value}</textarea>
</td>
</tr>
<tr>
<td style="vertical-align: top; width: 50px;">{lang_period}<br>
</td>
<td style="vertical-align: top;">{select_period}
</td>
</tr>
<tr>
<td style="vertical-align: top; text-align: center;" colspan="2">
<input type="submit" name="{update_file_field}" value="{update_file_action}">
<input type="reset" name="{reset_file_field}" value="{reset_file_action}">
<input type="submit" name="{go_back_field}" value="{go_back_action}">
</form>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td colspan="2">
<hr/>
<table>
<tr>
<td>
<h2 id="versions">Version</h2>
<form enctype="multipart/form-data" action="{action_update}#versions" method="post">
<input type="hidden" name="{version_id_field}" value="{version_id_value}">
<input type="hidden" name="{element_id_field}" value="{element_id_value}">
</td>
</tr>
<tr>
<td style="vertical-align: top; width: 50px;">File<br>
</td>
<td style="vertical-align: top;"><input name="{version_file_field}" type="file" value="{version_file_value}""/>
</td>
</tr>
<tr>
<td>
  {lang_version} :
</td>
<td>  
  <input type="text" default_class="{input_default_class}"  focused_class="{input_active_class}" name="{major_field}" value="{major_value}" size="2" maxlength="2"/> . <input type="text" default_class="{input_default_class}"  focused_class="{input_active_class}" name="{minor_field}" value="{minor_value}" size="2" maxlength="2"/>
</td>
</tr>
<tr>
<td style="vertical-align: top; width: 50px;">Description<br>
</td>
<td style="vertical-align: top;"><textarea name="{version_description_field}" name="description" rows="10" cols="50" wrap="off" >{version_description_value}</textarea>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td style="border: 1px black solid;">

<table width="100%" height="100%" >
<tr>
<td valign="top" height="100%" width="50%">
 <h3>Search</h3>
 <table width="70%">
 <tr>
 <td colspan="2">
 <input type="text" name="query" value="{search_query}"> <input type="submit" name="search"value="search" >
 </td>
 </tr>
<!-- BEGIN search_list_block -->
 <tr>
 <td width="20" valign="bottom">
 <img src="{status_image}">
 </td>
 <td valign="top"><a href="{search_link}">{name} [{reference}] {version}</a></td>
 <td width="20" valign="bottom">
 <input type="image" src="{add-image}" name="do_add_relation" value="{version_id}">
 </td>
 </tr>
<!-- END search_list_block -->
 </table>
</td>
<td valign="top">
  <h3>Relations</h3>
  <table width="100%">
<!-- BEGIN relations_list_block -->
  <tr>
  <td><img src="{relations_element_status_image}" />
  </td>
  <td>{relations_element_name} [{relations_element_reference}] v{relations_element_major}.{relations_element_minor}
  <input name="{relations_id_field}" type="hidden" value="{relations_id_value}"/>
  </td>
  <td>
  {relations_type}
  </td>
  <td>
  <input type="image" src="{remove-image}" name="do_remove_relation" value="{relations_id_value}">
  </td>
  </tr>
<!-- END relations_list_block -->
  </table>
 </td>
</tr>
</table>
</td>
</tr>	
<tr>
<td style="vertical-align: top; text-align: center;" colspan="2">
<input type="submit" name="{update_version_field}" value="{update_version_action}">
<input type="reset" name="{reset_version_field}" value="{reset_version_action}">
<input type="submit" name="{go_back_field}" value="{go_back_action}">
</form>
</td>
</tr>
</tbody>
</table>

 <hr/>
<form name="search" enctype="multipart/form-data" action="{action_search}" method="post">
 <input type="text" name="query"> <input type="hidden" name="sess"  value="193a227a024bf5333a10175a94c1cb86">
 <input type="hidden" name="parent" value="2"> <input type="hidden" name="expand" value="1">
 <input type="hidden" name="order" value="name"> <input type="hidden" name="sortorder" value="sortname">
 <input type="hidden" name="sort"  value="ASC"> <input type="image" src="browse.php_files/btn_search.gif" border="0" alt="Search" title="Search" value="Search">
 </form>