<!-- BEGIN form -->
<style type="text/css">
	#country { width: 300px; }
	#gender { width: 300px; }
	#loc1 { width: 300px; }
	#loc2 { width: 300px; }
	#loc3 { width: 300px; }
	#loc4 { width: 300px; }
	#loc5 { width: 300px; }
	#loc6 { width: 300px; }
</style>

<script language="JavaScript" type="text/javascript">
	var tos;

	function opentoswindow()
	{
		if (tos)
		{
			if (tos.closed)
			{
				tosWindow.stop();
				tosWindow.close();
			}
		}
		tosWindow = window.open("{tos_link}", "tos", "width=500,height=600,location=no,menubar=no,directories=no,toolbar=no,scrollbars=yes,resizable=yes,status=no");
		if (tosWindow.opener == null)
		{
			tosWindow.opener = window;
		}
	}
</script>

{css}

<script type="text/javascript">
<!--
	var strBaseURL = '{str_base_url}';
	{win_on_events}
//-->
</script>
{javascript}


<center>{message}</center>
<center>{errors}</center>
<form action="{form_action}" method="POST">
	<table border="0" width="75%" align="center">

		<!-- BEGIN username -->
		<tr>
			<td width="1%"></td>
			{domain_select}
		</tr>
		<tr>
			<td width="1%">{missing_loginid}</td>
			<td>{lang_username}</td>
			<td>{domain_from_host}<input name="r_reg[loginid]" value="{value_username}"></td>
		</tr>
		<!-- END username -->


		<!-- BEGIN password -->
		<tr>
			<td width="1%">{missing_passwd}</td>
			<td><b>{lang_password}</b></td>
			<td><input type="password" name="r_reg[passwd]" value="{value_passwd}"></td>
		</tr>

		<tr>
			<td width="1%">{missing_passwd_confirm}</td>
			<td><b>{lang_reenter_password}</b></td>
			<td><input type="password" name="r_reg[passwd_confirm]" value="{value_passwd_confirm}"></td>
		</tr>
		<!-- END password -->

		<!-- BEGIN other_fields_proto -->
		<tr>
			<td width="1%">{missing_indicator}</td>
			<td>{bold_start}{lang_displayed_text}{bold_end}</td>
			<td>{input_field}</td>
		</tr>
		<!-- END other_fields_proto -->

		<!-- BEGIN tos -->
		<tr>
			<td width="1%">{missing_tos_agree}</td>
			<td colspan="2"><b><font size="2"><a href="javascript:opentoswindow()">{lang_tos_agree}</a></font></b><input type="checkbox" name="r_reg[tos_agree]" {value_tos_agree}></td>
		</tr>
		<!-- END tos -->

		<tr>
			<td colspan="3"><input type="submit" name="submit" value="{lang_submit}"></td>
		</tr>
	</table>
</form>
<!-- END form -->

