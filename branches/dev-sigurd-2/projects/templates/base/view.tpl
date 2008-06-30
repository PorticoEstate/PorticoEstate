<!-- $Id: view.tpl,v 1.3 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"></div>
<center>{message}</center>
<form method="POST" name="app_form" action="{action_url}" enctype="multipart/form-data">
<div align="center">
<table class="tabletab">
  <tr>
    <th id="tab1" class="activetab" valign="top" onclick="javascript:tab.display(1);">
      <table class="basic">
        <tr>
          <td id="starttab"></td>
          <td>
            <a href="#" tabindex="0" accesskey="1" onfocus="tab.display(1);" onclick="tab.display(1); return(false);">Projekt</a>
          </td>
          <td id="tweentab_r"></td>
        </tr>
      </table>
    </th>
    <th id="tab2" class="activetab" onclick="javascript:tab.display(2);">
      <table>
        <tr>
          <td id="tweentab_l"></td>
          <td>
            <a href="#" tabindex="0" accesskey="2" onfocus="tab.display(2);" onclick="tab.display(2); return(false);">Personen</a>
          </td>
          <td id="tweentab_r"></td>
        </tr>
      </table>
    </th>
    <th id="tab3" class="activetab" onclick="javascript:tab.display(3);">
      <table>
        <tr>
          <td id="tweentab_l"></td>
          <td>
            <a href="#" tabindex="0" accesskey="3" onfocus="tab.display(3);" onclick="tab.display(3); return(false);">Zeit und Budget</a>
          </td>
          <td id="tweentab_r"></td>
        </tr>
      </table>
    </th>
    <th id="tab4" class="activetab" onclick="javascript:tab.display(4);">
      <table>
        <tr>
          <td id="tweentab_l"></td>
          <td>
            <a href="#" tabindex="0" accesskey="4" onfocus="tab.display(4);" onclick="tab.display(4); return(false);">Dokumentation</a>
          </td>
          <td id="tweentab_r"></td>
        </tr>
      </table>
    </th>
  </tr>
</table>
</div>
<div id="tabcontent1" class="activetab">
<table class="contenttab">
	<tr bgcolor="{row_on}">
		<td width="20%">{lang_parent}:</td>
		<td width="30%">{parent_select}</td>
		<td width="20%">{lang_previous}</td>
		<td width="30%">{previous}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_number}:</td>
		<td>{number}</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_investment_nr}:</td>
		<td>{investment_nr}</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_title}:</td>
		<td>{title}</td>
		<td>{lang_category}:</td>
		<td>{cat}</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td valign="top">{lang_descr}:</td>
		<td colspan="3">{descr}</td>
	</tr>

	<tr bgcolor="{row_off}">
		<td>{lang_start_date_planned}:</td>
		<td>{psdate}</td>
		<td>{lang_date_due_planned}:</td>
		<td>{pedate}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_start_date}:</td>
		<td>{sdate}</td>
		<td>{lang_date_due}:</td>
		<td>{edate}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_status}:</td>
		<td>{status}</td>
		<td valign="top">{lang_access}:</td>
		<td>{access}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_priority}:</td>
		<td>{priority}</td>
		<td colspan="2"></td>
	</tr>		
	<tr bgcolor="{row_off}">
		<td>{lang_url}:</td>
		<td>{url}</td>
		<td>{lang_reference}:</td>
		<td>{reference}</td>
	</tr>
</table>
</div>

<div id="tabcontent2" class="activetab">
<table class="contenttab">
	<tr bgcolor="{row_on}" style="font-weight: bold; text-align: center">
		<td colspan="5">{lang_customer}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_customer_nr}:</td>
		<td>{customer_nr}</td>
		<td><!-- {lang_orga}: --></td>
		<td colspan="2"></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_person}:</td>
		<td>{customer}</td>
		<td colspan="3"></td>
	</tr>
	<tr bgcolor="{row_on}" style="font-weight: bold; text-align: center">
		<td colspan="5">Projekt-Team</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td colspan="2">{lang_coordinator}:</td>
		<td colspan="3">{coordinator}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td colspan="2">{lang_salesmanager}:</td>
		<td colspan="3">{salesmanager}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td colspan="5" style="text-align: left">{lang_employees}:</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td colspan="2" style="text-decoration: underline">{lang_name}</td>
		<td colspan="2" style="text-decoration: underline">{lang_role}</td>
		<td style="text-decoration: underline">{lang_events}</td>
	<tr>

<!-- BEGIN emplist -->

	<tr bgcolor="{row_off}">
		<td colspan="2" valign="top" nowrap="nowrap">{emp_name}&nbsp;</td>
		<td colspan="2" valign="top" nowrap="nowrap">{role_name}&nbsp;</td>
		<td>{events}</td>
	</tr>

<!-- END emplist -->
	
	
	<tr bgcolor="{row_on}" align="center">
		<td colspan="2"></td>
		<td colspan="3"></td>
	</tr>

<!--begin rolefield1

	<tr bgcolor="{row_off}">
		<td valign="top">{lang_roles}:</td>
		<td colspan="2">
			<table width="100%" border="0" cellspacing="2" cellpadding="2">

end rolefield1
begin rolelist

				<tr>
					<td width="50%">{emp_name}</td>
					<td width="50%">{role_name}</td>
				</tr>

end rolelist

begin rolefield2
			</table>
		</td>
		<td valign="top" align="right"><input type="submit" name="roles" value="{lang_edit_roles}"></td>
	</tr>

end rolefield2-->
</table>
</div>


<div id="tabcontent3" class="activetab">
<table class="contenttab">
	<tr bgcolor="{row_on}">
		<td>{lang_plan_bottom_up}:</td>
		<td colspan="4" align="left">{plan_bottom_up_text}</td>
	</tr>
<!-- BEGIN nonanonym -->

	<tr bgcolor="{row_off}">
		<td>{lang_ptime}:</td>
		<td colspan="4">
			<table width="100%" border="0">
				<tr bgcolor="{row_on}">
					<td colspan="2">{lang_ptime}&nbsp;{lang_hours}&nbsp;[h]</td>
				<tr>
				<tr bgcolor="{row_off}">
					<td width="50%">&nbsp;{lang_project}:</td>
					<td align="right" width="50%">{ptime_item}&nbsp;</td>
				</tr>
				<tr bgcolor="{row_off}">
					<td width="50%">&nbsp;{lang_sub_project}:</td>
					<td align="right" width="50%">{ptime_jobs}&nbsp;</td>
				</tr>
				<tr bgcolor="{row_off}">
					<td width="50%" style="border-top: 1px solid #000000">&nbsp;{lang_sum}:</td>
					<td align="right" width="50%" style="border-top: 1px solid #000000">{ptime_sum}&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_budget}:</td>
		<td colspan="4">
			<table width="100%" border="0">
				<tr bgcolor="{row_on}">
					<td colspan="2">{lang_budget}&nbsp;[{currency}]</td>
				<tr>
				<tr bgcolor="{row_off}">
					<td width="50%">&nbsp;{lang_project}:</td>
					<td align="right" width="50%">{budget_item}&nbsp;</td>
				</tr>
				<tr bgcolor="{row_off}">
					<td width="50%">&nbsp;{lang_sub_project}:</td>
					<td align="right" width="50%">{budget_jobs}&nbsp;</td>
				</tr>
				<tr bgcolor="{row_off}">
					<td width="50%" style="border-top: 1px solid #000000">&nbsp;{lang_sum}:</td>
					<td align="right" width="50%" style="border-top: 1px solid #000000">{budget_sum}&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr bgcolor="{row_off}">
		<td>{lang_extra_budget}:</td>
		<td colspan="4">
			<table width="100%" border="0">
				<tr bgcolor="{row_on}">
					<td colspan="2">{lang_extra_budget}&nbsp;[{currency}]</td>
				<tr>
				<tr bgcolor="{row_off}">
					<td width="50%">&nbsp;{lang_project}:</td>
					<td align="right" width="50%">{ebudget_item}&nbsp;</td>
				</tr>
				<tr bgcolor="{row_off}">
					<td width="50%">&nbsp;{lang_sub_project}:</td>
					<td align="right" width="50%">{ebudget_jobs}&nbsp;</td>
				</tr>
				<tr bgcolor="{row_off}">
					<td width="50%" style="border-top: 1px solid #000000">&nbsp;{lang_sum}:</td>
					<td align="right" width="50%" style="border-top: 1px solid #000000">{ebudget_sum}&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>

{accounting_settings}
{accounting_2settings}

<!-- END nonanonym -->

</table>
</div>

<div id="tabcontent4" class="activetab">
<table class="contenttab">
	<tr bgcolor="{row_on}">
		<td valign="top">{lang_result}:</td>
		<td colspan="3">{result}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td valign="top">{lang_test}:</td>
		<td colspan="3">{test}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td valign="top">{lang_quality}:</td>
		<td colspan="3">{quality}</td>
	</tr>

	<tr bgcolor="{row_off}">
		<td valign="top">{lang_milestones}</td>
		<td colspan="2">
			<table width="100%" border="0" cellspacing="2" cellpadding="2">

<!-- BEGIN mslist -->

				<tr>
					<td width="50%">{s_title}</td>
					<td width="50%">{s_edateout}</td>
				</tr>

<!-- END mslist -->

			</table>
		</td>
		<td align="right" valign="top"><!-- {edit_milestones_button} --></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td valign="top">{lang_files}:</td>
		<td colspan="3">
			<table border="0" cellspacing="0" cellpadding="0" width="80%" style="padding: 5px;">
				<tr bgcolor="{row_off}">
					<td>{lang_filename}</td>
      	    		<td>{lang_period}</td>
      	    		<td>&nbsp;</td>
      	    	</tr>
				{files}
			</table>
		</td>
	</tr>

	<tr height="15">
		<td colspan="4">&nbsp;</td>
	</tr>

	<tr bgcolor="{row_off}">
		<td>{lang_creator}:</td>
		<td>{owner}</td>
		<td>{lang_cdate}:</td>
		<td>{cdate}</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td>{lang_processor}:</td>
		<td>{processor}</td>
		<td>{lang_last_update}:</td>
		<td>{udate}</td>
	</tr>
</table>
</div>



<script language="JavaScript1.1" type="text/javascript">
<!--
  var tab = new Tabs(4,'activetab','inactivetab','tab','tabcontent','','','tabpage');
  tab.init();
  switch_budget_type('{budget_type}');
  set_factortr();
// -->
</script>

</form>

<!-- BEGIN accounting_act -->
	<tr bgcolor="{row_off}">
		<td valign="top">{lang_bookable_activities}:</td>
		<td colspan="4">{book_activities_list}&nbsp;</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td valign="top">{lang_billable_activities}:</td>
		<td colspan="4">{bill_activities_list}&nbsp;</td>
	</tr>
<!-- END accounting_act -->

<!-- BEGIN accounting_own -->
	<tr bgcolor="{row_on}" valign="top">
		<td>{lang_accounting}:</td>
		<td>{accounting_factor}</td>
		<td>{lang_accounting_factor_for_project}:&nbsp;{currency}</td>
		<td colspan="2" valign="top" style="padding: 0px;">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>{project_accounting_factor}&nbsp;{lang_per_hour}</td>
				</tr>
				<tr>
					<td>{project_accounting_factor_d}&nbsp;{lang_per_day}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_billable}:</td>
		<td>{billable}</td>
		<td>{lang_direct_work}:</td>
		<td colspan="2">{direct_work_text}</td>
	</tr>

<!-- END accounting_own -->
	
<!-- BEGIN accounting_both -->

	<tr bgcolor="{row_on}">
		<td>{lang_invoicing_method}:</td>
		<td>{inv_method}</td>
		<td valign="top">{lang_discount}:&nbsp;{discount_type}</td>
		<td valign="top" colspan="2">{discount}</td>
	</tr>

<!-- END accounting_both -->

<!-- BEGIN sub -->

	<tr bgcolor="{th_bg}" valign="top">
		<td>{lang_main}:</td>
		<td><a href="{main_url}">{pro_main}</a></td>
		<td>{lang_parent}:</td>
		<td><a href="{parent_url}">{pro_parent}</a></td>
	</tr>

<!-- END sub -->

<!-- BEGIN attachment_list -->        
      <tr>
          <td>{attachment_link}</td>
          <td style="padding-left: 5px;">{attachment_comment}</td>
          <td>{delete}</td>
      </tr>
<!-- END attachment_list -->