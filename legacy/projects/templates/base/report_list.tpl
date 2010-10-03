<!-- BEGIN report.tpl -->
<!-- $Id: report_list.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"></div>
<center>
<form method="POST" action="{actionurl}">
<table style="border: 2px solid #FFFFFF; width:800px; min-width:800px" align="center">
	<tr bgcolor="{th_bg}">
		<td colspan="2" align="center"><b>{lang_activity_reports}</b></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td colspan="2" align="center"><b>{error}</b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_reports_for}:&nbsp;{employee}</td>
		<td align="right"><input type="submit" name="yes" value="{lang_new}" /></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td colspan="2" align="center">
			<table border="0" cellspacing="0" cellpadding="0" width="80%" style="padding: 5px;">
				{report_rows}
			</table>
		</td>
	</tr>
</table>
</form>
</center>
<!-- END report.tpl -->

<!-- BEGIN project_row -->
      <tr bgcolor="{row_off}">
        <td colspan="2">{pr_name}</td>
      </tr>
      <tr>
        <td>
      	  <table style="border:2px solid rgb(128, 128, 128);" width="100%">
      	    <tr bgcolor="{row_off}">
      	    	<td>{lang_filename}</td>
      	    	<td>{lang_period}</td>
      	    	<td>&nbsp;</td>
      	    </tr>
      		  {files}
      	  </table>
      	</td>
      </tr>
<!-- END project_row -->
      
<!-- BEGIN attachment_list -->        
      <tr>
          <td>{attachment_link}</td>
          <td style="padding-left: 5px;">{attachment_comment}</td>
          <td>{delete}</td>
      </tr>
<!-- END attachment_list -->