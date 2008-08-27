<form name="FileList" enctype="multipart/form-data" action="{action_add}" method="post">
<table cellpadding="5">
<tr>
<td colspan="2">
<h2>Add file</h2><input type="hidden" name="{parent_id_field}" value="{parent_id_value}">
</td>
</tr>
<tr>
<td>
  {lang_name} : 
</td>
<td>  
  <input type="text" default_class="{input_default_class}"  focused_class="{input_active_class}" name="name" value="{new_name}" size="70" maxlength="255"/>
  <input type="hidden" name="{parent_id_field}" value="{parent_id_value}">
</td>
</tr>
<tr>
<td style="vertical-align: top; width: 50px;">{lang_type} :<br>
</td>
<td style="vertical-align: top;">{select_type}
</td>
</tr>
<tr>
<td>
  {lang_reference} : 
</td>
<td>  
  <input type="text" default_class="{input_default_class}"  focused_class="{input_active_class}" name="{referenceq_field}" value="{new_reference}" size="40" maxlength="255"/>
</td>
</tr>
<tr>
<td>
  {lang_version} : 
</td>
<td>  
  <input type="text" default_class="{input_default_class}"  focused_class="{input_active_class}" name="{major_field}" value="{major_reference}" size="2" maxlength="2"/> . <input type="text" default_class="{input_default_class}"  focused_class="{input_active_class}" name="{minor_field}" value="{minor_reference}" size="2" maxlength="2"/>
</td>
</tr>
<tr>
<td>
{lang_file} :  
</td>
<td>
<input name="{file_field}" type="file" value="{file_value}""/>
</td>
</tr>
<tr>
<td>
{description_label} :
</td>
<td>
<textarea name="{description_field}" name="description" rows="10" cols="50" wrap="off" >{description_value}</textarea>
</td>
</tr>
<tr>
<td style="vertical-align: top; width: 50px;">{lang_period} :<br>
</td>
<td style="vertical-align: top;">{select_period}
</td>
</tr>
<tr>
<td colspan="2" align="center" >
<input type="submit" name="add_file" value="{lang_add_file}" />
</td>
</tr>
 </table>
 </form>
 <hr/>
<form name="search" enctype="multipart/form-data" action="{action_search}" method="post">
 <input type="text" name="query"> <input type="hidden" name="sess"  value="193a227a024bf5333a10175a94c1cb86">
 <input type="hidden" name="parent" value="2"> <input type="hidden" name="expand" value="1">
 <input type="hidden" name="order" value="name"> <input type="hidden" name="sortorder" value="sortname">
 <input type="hidden" name="sort"  value="ASC"> <input type="image" src="browse.php_files/btn_search.gif" border="0" alt="Search" title="Search" value="Search">
 </form>
