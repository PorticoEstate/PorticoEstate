<!-- $Id$ -->
{app_header}
<div class="projects_content"></div>
<center>{msg}</center>
<!-- BEGIN project_main -->
<!--
<table border="0" width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_main}:&nbsp;<a href="{main_url}">{title_main}</a></b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_number}:</td>
		<td>{number_main}</td>
		<td>{lang_url}:</td>
		<td><a href="http://{url_main}" taget="_blank">{url_main}</a></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_coordinator}:</td>
		<td>{coordinator_main}</td>
		<td>{lang_customer}:</td>
		<td>{customer_main}</td>
	</tr>
</table>
-->
<!-- END project_main -->
<!--
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr>
		<td width="100%" colspan="8">
			<table border="0" width="100%">
				<tr>
				{left}
					<td align="center" width="100%">{lang_showing}</td>
				{right}
				</tr>
			</table>
		</td>
	</tr>
	<form method="POST" action="{action_url}">
	<tr style="vertical-align:top">
		<td width="20%" nowrap="nowrap" align="left">{action_list}</td>
		<td width="10%" nowrap="nowrap" align="center"><select name="status" onChange="this.form.submit();">{status_list}</select></td>
		{filter_list}
		<td width="10%" nowrap="nowrap" align="right">{search_list}</td>
	</tr>
</table>
<br/>
-->
<script type="text/javascript">
	function change_view(id)
	{
		value = document.getElementById(id).style.display
		if (value != 'none') 
			document.getElementById(id).style.display = "none";
		else
			document.getElementById(id).style.display = "block";
	}
</script>

<style type="text/css">
	table.show_emps {
		border:2px solid #808080;
		}
</style>

<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}">
		<td style="padding-left:5px; paddgin-right:5px">{sort_number}</td>
		<td style="padding-left:5px; paddgin-right:5px">{sort_title}</td>
		<td style="padding-left:5px; paddgin-right:5px">{sort_coordinator}</td>
		<td style="padding-left:5px; paddgin-right:5px; text-align:center">{sort_sdate}</td>
		<td style="padding-left:5px; paddgin-right:5px; text-align:center">{sort_edate}</td>
		<td style="padding-left:5px; paddgin-right:5px" width="16">&nbsp;</td>
	</tr>

<!-- BEGIN projects_list -->

	<tr bgcolor="{tr_color}">
		<td style="padding-left:5px; paddgin-right:5px;">{number}</td>
		<td style="padding-left:5px; paddgin-right:5px;"><a href="{projects_url}">{title}</a></td>
		<td style="padding-left:5px; paddgin-right:5px;">{coordinator}</td>
		<td style="padding-left:5px; paddgin-right:5px; text-align:center">{sdate}</td>
		<td style="padding-left:5px; paddgin-right:5px; text-align:center">{edate}</td>
		<td style="padding-left:5px; paddgin-right:5px; text-align:center"><a href="javascript:change_view({node_nr})"><img src="{view_img}" border="0" title="{lang_view_employees}"></a></td>
	</tr>

	{employee_list}

<!-- END projects_list -->
                                                                                                                                     
	<tr height="50" valign="bottom">
		<td colspan="6">&nbsp;</td>
	</tr>
</form>
</table>

<!-- BEGIN user_cols -->
	<tr bgcolor="{tr_color}">
		<td colspan="2">&nbsp;</td>
		<td colspan="3">
			<div id="{node_nr}" style="display: {node_style};">
				<table width="100%" align="center" border="0" cellpadding="0" cellspacing="2" class="show_emps">
					<tr>
						<td width="50%" bgcolor="{th_bg}">{lang_project_employees}</td>
						<td width="50%" bgcolor="{th_bg}">{lang_role}</td>
					</tr>

<!-- BEGIN user_list -->

					<tr>
						<td width="50%" bgcolor="{tr_color}">{emp_name}</td>
						<td width="50%" bgcolor="{tr_color}">{emp_role}</td>
					</tr>

<!-- END user_list -->
				</table>
			</div>
		</td>
		<td>&nbsp;</td>
	</tr>
<!-- END user_cols -->
