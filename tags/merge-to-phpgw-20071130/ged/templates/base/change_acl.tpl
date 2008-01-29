<h2>Change Acl - {element_name}</h2>
<form method="POST">
<table>
<tr>
<td colspan="5" align="center"><h3>Update Access Control</h3></td>
</tr>
<tr>
<td class="th">user/group</td>
<td class="th" nowrap ><b>Can read</b></td>
<td class="th" nowrap ><b>Can Write</b></td>
<td class="th" nowrap ><b>Can Delete</b></td>
<td class="th" nowrap ><b>Can Change Acl</b></td>
<!-- BEGIN statuses_list -->
<td class="th" bgcolor="lightgrey">{status_label}</td>
<!-- END statuses_list -->
<td class="th" nowrap ><b>Alter children's ACLs</b></td>
</tr>
<!-- BEGIN acl_list -->
<tr>
<td>{account}<input type="hidden" name="acl[{acl_id}][account]" value="{account}"></td></td>
<td align="center"><input type="checkbox" name="acl[{acl_id}][read]" {readflag}></td>
<td align="center"><input type="checkbox" name="acl[{acl_id}][write]" {writeflag}></td>
<td align="center"><input type="checkbox" name="acl[{acl_id}][delete]" {deleteflag}></td>
<td align="center"><input type="checkbox" name="acl[{acl_id}][changeacl]" {changeaclflag}></td>
<!-- BEGIN acl_list_statuses_list -->
<td align="center" bgcolor="lightgrey"><input type="checkbox" name="acl[{acl_id}][statuses][{status}]" {statusflag}></td>
<!-- END acl_list_statuses_list -->
<td align="center"><input type="checkbox" name="acl[{acl_id}][recursive]"></td>
</tr>
<!-- END acl_list -->
<tr>
<td colspan="5" align="center"><h3>Add Access Control</h3></td>
</tr>
<tr>
<td class="th" nowrap >user/group</td>
<td class="th" nowrap ><b>Can read</b></td>
<td class="th" nowrap ><b>Can Write</b></td>
<td class="th" nowrap ><b>Can Delete</b></td>
<td class="th" nowrap ><b>Can Change Acl</b></td>
<!-- BEGIN statuses_list -->
<td class="th" bgcolor="lightgrey">{status_label}</td>
<!-- END statuses_list -->
<td class="th" nowrap ><b>Alter children's ACLs</b></td>
</tr>
<tr>
<td><select name="newacl[account_id]"><!-- BEGIN accounts_list -->
<option value="{account_id}">{account}</option>
<!-- END accounts_list -->
</select></td>
<td align="center"><input type="checkbox" name="newacl[read]"></td>
<td align="center"><input type="checkbox" name="newacl[write]"></td>
<td align="center"><input type="checkbox" name="newacl[delete]"></td>
<td align="center"><input type="checkbox" name="newacl[changeacl]"></td>
<!-- BEGIN new_statuses_list -->
<td align="center" bgcolor="lightgrey"><input type="checkbox" name="newacl[statuses][{status}]"></td>
<!-- END new_statuses_list -->
<td align="center"><input type="checkbox" name="newacl[recursive]"></td>
</tr>
<tr>
<td></td>
<td align="center"><input type="submit" name="update_acl" value="{lang_update_acl}" /></td>
<td align="center"><input type="reset" name="reset_acl" value="{lang_reset_acl}" /></td>
<td align="center"><input type="button" name="go_back" value="{lang_go_back}" onclick="{js_action_go_back}"/></td>
</tr>
</form>