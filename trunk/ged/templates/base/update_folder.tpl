
<form name="FileList" enctype="multipart/form-data" action="{action_add}" method="post">
<table cellpadding="5">
<tr>
<td colspan="2">
<h2>{lang_update_folder}</h2>
</td>
</tr>
<tr>
<td>
  {lang_name} : 
</td>
<td>  
  <input type="text" default_class="{input_default_class}"  focused_class="{input_active_class}" name="{folder_name_field}" value="{folder_name_value}" size="40" maxlength="40"/>
  <input type="hidden" name="{parent_id_field}" value="{parent_id_value}" />
</td>
</tr>
<tr>
<td>
  {lang_reference} : 
</td>
<td>  
  <input type="text" default_class="{input_default_class}"  focused_class="{input_active_class}" name="{folder_reference_field}" value="{folder_reference_value}" size="40" maxlength="40"/>
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
  <textarea default_class="{textarea_default_class}" focused_class="{textarea_active_class}"  class="{description_class}" name="{folder_description_field}" rows="10" cols="50" wrap="off">{folder_description_value}</textarea>
</td>
</tr>
<tr>
<td>
</td>
<td>
  <input type="submit" name="update_folder" value="{update_folder_action}"/>
  <input type="reset" name="reset_form" value="clear form"/>
 </td>
 </tr>   
 </table>
 </form>
