<!-- begin attach_file_blocks.tpl -->

&nbsp; <!-- === block seperator == --> &nbsp; 


<!-- BEGIN B_alert_msg -->
<tr>
	<td>
		<strong>{alert_msg}</strong>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<!-- END B_alert_msg -->


&nbsp; <!-- === block seperator == --> &nbsp; 


<!-- BEGIN B_attached_list -->
<tr>
	<td>
		<input type="hidden" name="{hidden_delete_name}" value="{hidden_delete_filename}">
		<input type="checkbox" name="{ckbox_delete_name}" value="{ckbox_delete_value}">{ckbox_delete_filename}
	</td>
</tr>
<!-- END B_attached_list -->


&nbsp; <!-- === block seperator == --> &nbsp; 


<!-- BEGIN B_attached_none -->
<tr>
	<td>{text_none}</td>
</tr>
<!-- END B_attached_none -->


&nbsp; <!-- === block seperator == --> &nbsp; 


<!-- BEGIN B_delete_btn -->
<tr>
	<td>
		&nbsp;<input type="submit" name="{btn_delete_name}" value="{btn_delete_value}">
	</td>
</tr>
<!-- END B_delete_btn -->


&nbsp; <!-- === block seperator == --> &nbsp; 

<!-- end attach_file_blocks.tpl -->
