<form name="FileList" enctype="multipart/form-data" action="{action_review}" method="post">
<input type="hidden" name="{element_id_field}" value="{element_id_value}">
<table cellpadding="5">
<tr>
<td colspan="2">
<h2>{review_title}</h2>
</td>
</tr>
<tr>
<td>
{comment_label} :
</td>
<td>
<textarea name="{comment_field}" rows="10" cols="50" wrap="off" >{comment_value}</textarea>
</td>
</tr>
<tr>
<td>
{probable_reference_label} :
</td>
<td>
{probable_reference_value}
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
<td colspan="2" align="center" >
<input type="submit" name="review_file" value="{lang_do_transition}" />
</td>
</tr>
</table>
</form>