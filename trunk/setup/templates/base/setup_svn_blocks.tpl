<!-- begin setup_svn_blocks.tpl -->

&nbsp; <!-- ================================== --> &nbsp; 

<!-- BEGIN B_svn_stage_1 -->
<tr>
	<td align="center">
		<img src="{img_completed}" alt="{completed}" border="0">
	</td>
	<td>
		{check_for_svn_update}
		<form method="POST" action="index.php">
		{dry_run}:
        <input type="hidden" name="action_svn" value="check_for_svn_update">
		<input type="submit" name="label" value="{check_for_svn_update}"><br>({svnwarn})
		</form>
		{svn_message}
	</td>
</tr>
<!-- END B_svn_stage_1 -->

&nbsp; <!-- ================================== --> &nbsp; 

<!-- BEGIN B_svn_stage_2 -->
<tr>
	<td align="center">
		<img src="{img_completed}" alt="{completed}" border="0">
	</td>
	<td>
		{perform_svn_update}
		<form method="POST" action="index.php">
		{execute}:
        <input type="hidden" name="action_svn" value="perform_svn_update">
		<input type="submit" name="label" value="{perform_svn_update}"><br>({svnwarn})
		</form>
		{svn_message}
	</td>
</tr>
<!-- END B_svn_stage_2 -->

<!-- end setup_svn_blocks.tpl -->
