<!-- BEGIN add category -->
<form action="{actionurl}" method="POST">
<table align="center" width="100%" cellpadding="5" cellspacing="0">
	<tr>
		<td align="center" colspan="2">Add New Category</td>
	</tr>
	<tr>
		<td colspan="2">
			<table align="center" border="0" width="80%" 
cellpadding="5" cellspacing="0">
				<tr>
					<td colspan="2">Basic Settings:</td>
				</tr>
				<tr>
					<td>Catagory Name:</td>
					<td><input type="text" name="catname" 
value="{catname}"></td>
				</tr>
				<tr>
					<td>Catagory Description:</td>
					<td><textarea ROWS="3" COLS="50" 
name="catdesc">{catdesc}</textarea></td>
				</tr>
			</table>
		</td>
	</tr>	
	<tr>
		<td colspan="2"><hr width="80%"></td>
	</tr>
	<tr>
		<td colspan="2">
			<table align="center" border="0" width="80%" 
cellpadding="5" cellspacing="0">
				<tr>
					<td colspan="3">Group Access 
Permissions:</td>
				</tr>
				<tr>
					<td align="center">Group Name:</td>
					<td align="center">Read Permission:</td>
					<td align="center">Write 
Permission:</td>
				</tr>
				<tr>
					<td align="center">Group A</td>
					<td align="center"><input 
type="checkbox" name="groupaaccess" value="grouparead"></td>
					<td align="center"><input 
type="checkbox" name="groupaaccess" value="groupawrite"></td>
				</tr>
				<tr>
					<td align="center">Group B</td>
					<td align="center"><input 
type="checkbox" name="groupbaccess" value="groupbread"></td>
					<td align="center"><input 
type="checkbox" name="groupbaccess" value="groupbwrite"></td>
				</tr>
				<tr>
					<td colspan="3"><input type="checkbox" 
name="anonaccess" value="anonread"> Allow Anonymouse Users to Read Catagory</td>
				</tr>
				<tr>
					<td colspan="3"><input type="checkbox" 
name="anonaccess" value="anonwrite"> Allow Anonymouse Users to Contribute to 
Catagory</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2"><hr width="80%"></td>
	</tr>
	<tr>
		<td colspan="2">
			<table align="center" border="0" width="80%" 
cellpadding="5" cellspacing="0">
				<tr>
					<td colspan="2">Individual Access 
Permission:</td>
				</tr>
				<tr>
					<td>User Name:</td>
					<td><input type="text" name="username" 
value="{username}"></td>
					<td><input type="checkbox" 
name="userread[]" value="read"> Read</td>
					<td><input type="checkbox" 
name="userwrite[]" value="write"> Write</td>
					<td><input type="submit" name="add" 
value="add"></td>
				</tr>
				<tr>
					<td><textarea ROWS="3" COLS="25" 
name="indivnames"></textarea></td>
					<td><input type="submit" name="delete" 
value="delete"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2"><hr width="80%"></td>
	</tr>
	<tr>
		<td align="right"><input type="reset" name="reset" 
value="reset"></td>
 		<td align="left"><input type="submit" name="save" 
value="save"></td>
	</tr>
</table>
</form>
<!-- END add category -->
