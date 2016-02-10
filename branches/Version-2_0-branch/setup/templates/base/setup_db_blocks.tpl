<!-- begin setup_db_blocks.tpl -->

&nbsp; <!-- ================================== --> &nbsp; 

<!-- BEGIN B_db_stage_1 -->
<tr>
	<td align="center">
		<img src="{img_incomplete}" alt="{notcomplete}" border="0">
	</td>
	<td>
    {dbnotexist}<br>
		{makesure}.<br>
		<br>
		{instr}<p>
		<form action="index.php" method="post">
		{createdb}<br>
		    DB root username: <input type="text" name="db_root" value="{db_root}">
		    DB root password: <input type="password" name="db_pass">
            <input type="hidden" name="action" value="Create Database">
		    <input type="submit" name="label" value="{create_database}">
		</form>
		<form method="POST" action="index.php"> <br>
		<input type="submit" value="Re-Check my database">
		</form>
	</td>
</tr>
<!-- END B_db_stage_1 -->

&nbsp; <!-- ================================== --> &nbsp; 

<!-- BEGIN B_db_stage_2 -->

<tr>
	<td align="center">
		<img src="{img_incomplete}" alt="{notcomplete}" border="0">
	</td>
	<td>
	{prebeta}
	</td>
</tr>
<!-- END B_db_stage_2 -->

&nbsp; <!-- ================================== --> &nbsp; 

<!-- BEGIN B_db_stage_3 -->
<tr>
	<td align="center">
		<img src="{img_incomplete}" alt="{Complete}" border="0">
	</td>
	<td>
		<form action="index.php" method="post">
		<input type="hidden" name="oldversion" value="new">

		{dbexists}<br>
        <input type="hidden" name="action" value="Install">
		<input type="submit" name="label" value="{install}"> {coreapps}
		</form>
	</td>
</tr>
<!-- END B_db_stage_3 -->

&nbsp; <!-- ================================== --> &nbsp; 

<!-- BEGIN B_db_stage_4 -->
<tr>
	<td align="center">
		<img src="{img_incomplete}" alt="not complete" border="0">
	</td>
	<td>
		{oldver}.<br>
		{automatic}
		{backupwarn}<br>
		<form method="POST" action="index.php">
		<input type="hidden" name="oldversion" value="{oldver}">
		<input type="hidden" name="useglobalconfigsettings">
		<input type="hidden" name="action" value="Upgrade">
		<input type="submit" name="label" value="{upgrade}"><br>
		</form>

		<form method="POST" action="index.php">
		<input type="hidden" name="oldversion" value="{oldver}">
		<input type="hidden" name="useglobalconfigsettings">
		<input type="hidden" name="action" value="Uninstall all applications">
		<input type="submit" name="label" value="{uninstall_all_applications}"><br>({dropwarn})
		</form>
		<hr>
{dont_touch_my_data}.&nbsp;&nbsp;{goto}:
		<form method="POST" action="config.php">
        <input type="hidden" name="action" value="Dont touch my data">
		<input type="submit" name="label" value="{configuration}">
        </form>
		<form method="POST" action="lang.php">
        <input type="hidden" name="action" value="Dont touch my data">
		<input type="submit" name="label" value="{language_management}">
        </form>
		<form method="POST" action="applications.php">
        <input type="hidden" name="action" value="Dont touch my data">
		<input type="submit" name="label" value="{applications}">
		</form>
	</td>
</tr>
<!-- END B_db_stage_4 -->

<!-- BEGIN B_db_stage_5 -->
<tr>
	<td>&nbsp;</td><td align="left">{are_you_sure}</td>
</tr>
<tr>
	<td align="center">
		<img src="{img_incomplete}" alt="{Complete}" border="0">
	</td>
	<td>
		<form action="index.php" method="post">
		<input type="hidden" name="oldversion" value="new">
        <input type="hidden" name="action" value="REALLY Uninstall all applications">
		<input type="submit" name="label" value="{really_uninstall_all_applications}"> {dropwarn}
		</form>
		<form action="index.php" method="post">
		<input type="submit" name="cancel" value="{cancel}">
		</form>
	</td>
</tr>
<!-- END B_db_stage_5 -->

&nbsp; <!-- ================================== --> &nbsp; 

<!-- BEGIN B_db_stage_6_pre -->
<tr>
	<td align="center">
		<img src="{img_incomplete}" alt="{notcomplete}" border="0">
	</td>
	<td>
		<table cellspacing="0" style="{border-width: 0px; padding: 0px; width: 100%;}">
		<tr class="th">
			<td>
				&nbsp;<b>{subtitle}</b>
			</td>
		</tr>
		<tr class="row_on">
			<td>
				{submsg}
			</td>
		</tr>
		<tr class="th">
			<td>
				&nbsp;<b>{tblchange}</b>
			</td>
		</tr>
<!-- END B_db_stage_6_pre -->

&nbsp; <!-- ================================== --> &nbsp; 

<!-- BEGIN B_db_stage_6_post -->
		<tr class="th">
			<td>
				&nbsp;<b>{status}</b>
			</td>
		</tr>
		<tr bgcolor="#e6e6e6">
			<td>{tableshave} {subaction}</td>
		</tr>
		</table>

		<form method="POST" action="index.php"> <br>
		<input type="submit" value="{re-check_my_installation}">
		</form>
	</td>
</tr>
<!-- END B_db_stage_6_post -->

&nbsp; <!-- ================================== --> &nbsp; 

<!-- BEGIN B_db_stage_10 -->
<tr>
	<td align="center">
		<img src="{img_completed}" alt="{completed}" border="0">
	</td>
	<td>
		{tablescurrent}
		<form method="POST" action="index.php">
		<input type="hidden" name="oldversion" value="new"> <br>
		{insanity}: 
        <input type="hidden" name="action" value="Uninstall all applications">
		<input type="submit" name="label" value="{uninstall_all_applications}"><br>({dropwarn})
		</form>
	</td>
</tr>
<!-- END B_db_stage_10 -->

&nbsp; <!-- ================================== --> &nbsp; 

<!-- BEGIN B_db_stage_default -->
<tr>
	<td align="center">
		<img src="{img_incomplete}" alt="not complete" border="0">
	</td>
	<td>
		<form action="index.php" method="post">
		{dbnotexist}.<br>
		<input type="submit" value="{create_one_now}">
		</form>
	</td>
</tr>
<!-- END B_db_stage_default -->

&nbsp; <!-- ================================== --> &nbsp; 


<!-- end setup_db_blocks.tpl -->
