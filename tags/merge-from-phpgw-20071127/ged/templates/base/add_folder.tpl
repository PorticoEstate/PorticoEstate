
<form name="FileList" enctype="multipart/form-data" action="{action_add}" method="post">
<table cellpadding="5">
<tr>
<td colspan="2">
<h2>{lang_add_folder}</h2>
</td>
</tr>
<tr>
<td>
  {lang_name} : 
</td>
<td>  
  <input type="text" default_class="{input_default_class}"  focused_class="{input_active_class}" name="name" value="{new_name}" size="40" maxlength="40"/>
  <input type="hidden" name="{parent_id_field}" value="{parent_id_value}" />
</td>
</tr>
<tr>
<td>
  {lang_reference} : 
</td>
<td>  
  <input type="text" default_class="{input_default_class}"  focused_class="{input_active_class}" name="referenceq" value="{new_reference}" size="40" maxlength="40"/>
</td>
</tr>
<!-- BEGIN project_block -->
<tr>
<td>
  {lang_project} : 
</td>
<td>  
  <input type="text" default_class="{input_default_class}"  focused_class="{input_active_class}" name="{project_name_field}" value="{project_name_value}" size="40" maxlength="40"/>
</td>
</tr>
<!-- END project_block -->
<tr>
<td>
  {lang_description} :
 </td>
 <td>
  <textarea default_class="{textarea_default_class}" focused_class="{textarea_active_class}"  class="{description_class}" name="description" rows="10" cols="50" wrap="off">{new_description}</textarea>
</td>
</tr>
<tr>
<td>
</td>
<td>
  <input type="submit" name="add_folder" value="{lang_add_folder}"/>
  <input type="reset" name="reset_form" value="clear form"/>
 </td>
 </tr>   
 </table>
 </form>
<form name="search" enctype="multipart/form-data" action="{action_search}" method="post">
   <input type="text" name="query"> <input type="hidden" name="sess"  value="193a227a024bf5333a10175a94c1cb86">
  <input type="hidden" name="parent" value="2"> <input type="hidden" name="expand" value="1">
  <input type="hidden" name="order" value="name"> <input type="hidden" name="sortorder" value="sortname">
  <input type="hidden" name="sort"  value="ASC"> <input type="submit" src="browse.php_files/btn_search.gif" border="0" alt="Search" title="Search" value="Search">
  </center>
</form>
