<!-- $Id: form.tpl,v 1.6 2006/12/05 19:40:45 sigurdne Exp $ -->
<script language="JavaScript">
	var parent_project_members = new Array();
	{parent_project_members}

	function writeOptions(tagname, myarray)
	{
		selectbox = document.getElementById(tagname);
		if(!selectbox)
			return;

		for(i=0; i < myarray.length; i++)
		{
			myoption = new Option(myarray[i][0], myarray[i][1], true);
			exists = false;
			for(j=0; j < selectbox.options.length; j++)
			{
				if (selectbox.options[j].value == myarray[i][1])
				{
					exists = true;
					j = selectbox.options.length;
				}
			}
			if(!exists)
			{
				selectbox.options[selectbox.length] = myoption;
				selectbox.options[(selectbox.length - 1)].selected = true;
			}
		}
	}

	function getParentMember(tagname)
	{
		writeOptions(tagname, parent_project_members);
	}

	function selectAll(tagname)
	{
		selectbox = document.getElementById(tagname);
		if(!selectbox)
			return;

		for(var i=0; i < selectbox.length; i++)
		{
			selectbox.options[i].selected = true;
		}
	}

	function clearFields(fieldlist)
	{
		for(var i=0; i < fieldlist.length; i++)
		{
			document.getElementById(fieldlist[i]).value = '';
		}
	}
	
	function clearOptions(tagname)
	{
		selectbox = document.getElementById(tagname);
		if(!selectbox)
			return;

		for(var i=0; i < selectbox.length; i++)
		{
			if(selectbox.options[i].selected == true)
			{
				selectbox.options[i] = null;
				--i;
			}
		}
	}
	
	function clearCustomer()
	{
		var customer_fields = Array('customernr', 'orgaid', 'organame', 'customerid', 'customer');
		clearFields(customer_fields);
	}

	function factor_calculator(type)
	{
		if(type == 'hour')
		{
			document.getElementById('factor').value = document.getElementById('factor').value / 8;
		}
		else
		{
			document.getElementById('factor').value = document.getElementById('factor').value * 8;
		}
	}

	function switch_budget_type(type)
	{
		if(type == 'h')
		{
			document.getElementById('hbudget').style.display = 'block';
			document.getElementById('mbudget').style.display = 'none';
		}
		else
		{
			document.getElementById('mbudget').style.display = 'block';
			document.getElementById('hbudget').style.display = 'none';
		}
	}

	function set_factortr()
	{
		if(document.getElementById('factortype').value == 'project')
		{
			document.getElementById('td1').style.display = 'block';
			document.getElementById('td2').style.display = 'block';
			document.getElementById('td3').style.display = 'block';
		}
		else
		{
			document.getElementById('td1').style.display = 'none';
			document.getElementById('td2').style.display = 'none';
			document.getElementById('td3').style.display = 'none';
		}
	}
</script>
{app_header}
<div class="projects_content"></div>
<center>{message}</center>
<table width="100%" border="0" cellspacing="2" cellpadding="2">
<form method="POST" name="app_form" action="{action_url}" enctype="multipart/form-data">
<!-- BEGIN main -->
	<tr bgcolor="{th_bg}">
		<td width="100%" colspan="7"><b>{lang_main}</b>:&nbsp;<a href="{main_url}">{pro_main}</a></td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td><b>{lang_pbudget}</b>:&nbsp;{currency}</td>
		<td>{lang_main}:</td>
		<td>{budget_main}</td>
		<td>{lang_sum_jobs}:</td>
		<td>{pbudget_jobs}</td>
		<td>{lang_available}:</td>
		<td>{apbudget}</td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td><b>{lang_ptime}</b>:&nbsp;{lang_hours}</td>
		<td>{lang_main}:</td>
		<td>{ptime_main}</td>
		<td>{lang_sum_jobs}:</td>
		<td>{ptime_jobs}</td>
		<td>{lang_available}:</td>
		<td>{atime}</td>
	</tr>
</table>

<table width="100%" border="0" cellspacing="2" cellpadding="2">
<!-- END main -->
</table>
<div align="center">
<table class="tabletab">
  <tr>
    <th id="tab1" class="activetab" valign="top" onclick="javascript:tab.display(1);">
      <table class="basic">
        <tr>
          <td id="starttab"></td>
          <td>
            <a href="#" tabindex="0" accesskey="1" onfocus="tab.display(1);" onclick="tab.display(1); return(false);">{lang_project}</a>
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
            <a href="#" tabindex="0" accesskey="2" onfocus="tab.display(2);" onclick="tab.display(2); return(false);">{lang_persons}</a>
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
            <a href="#" tabindex="0" accesskey="3" onfocus="tab.display(3);" onclick="tab.display(3); return(false);">{lang_time_and_budget}</a>
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
            <a href="#" tabindex="0" accesskey="4" onfocus="tab.display(4);" onclick="tab.display(4); return(false);">{lang_documentation}</a>
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
		<td>{lang_parent}:</td>
		<td>{parent_select}</td>
		<td>{lang_previous}:</td>
		<td><div style="width:99%; overflow:hidden;"><select style="width:99%; overflow:hidden;" name="values[previous]">
					<option value=""></option>
					{previous_select}
				</select></div>
			</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td class="must">{lang_number}:</td>
		<td nowrap="nowrap"><input type="text" name="values[number]" value="{number}" size="30" />&nbsp;{help_img}</td>
		<td>{lang_choose}</td>
		<td>{choose}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td width="20%">{lang_investment_nr}:</td>
		<td width="30%"><input type="text" name="values[investment_nr]" value="{investment_nr}" size="30"></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td class="must">{lang_title}:</td>
		<td><input type="text" name="values[title]" size="30" value="{title}"></td>
		<td class="must">{lang_category}:</td>
		<td><div style="width:99%; overflow:hidden;">{cat}</div></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td valign="top">{lang_descr}:</td>
		<td colspan="3"><textarea name="values[descr]" rows="1" cols="30" wrap="VIRTUAL">{descr}</textarea></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td class="must">{lang_start_date_planned}:</td>
		<td>{pstart_date_select}</td>
		<td class="must">{lang_date_due_planned}:</td>
		<td>{pend_date_select}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_start_date}:</td>
		<td>{start_date_select}</td>
		<td>{lang_end_date}:</td>
		<td>{end_date_select}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_status}:</td>
		<td><select name="values[status]">{status_list}</select></td>
		<td valign="top">{lang_access}:</td>
		<td>
			<div id="hideme2"><select name="values[access]">{acces_private}{acces_public}<!-- {acces_anonym} --></select></div>
		</td>
		<td>{access}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_priority}:</td>
		<td><select name="values[priority]">{priority_list}</select></td>
		<td>{lang_milestones}</td>
		<td>{edit_mstones_button}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_url}:</td>
		<td>http://<input type="text" name="values[url]" size="25" value="{url}"></td>
		<td>{lang_reference}:</td>
		<td>http://<input type="text" name="values[reference]" size="25" value="{reference}"></td>
	</tr>
</table>
</div>

<div id="tabcontent2" class="activetab">
<table class="contenttab">
	<tr bgcolor="{row_on}" style="font-weight: bold; text-align: center">
		<td colspan="2">{lang_customer}</td>
		<td colspan="2">{lang_project_team}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_customer_nr}:</td>
		<td><input style="width:95%;" type="text" id="customernr" name="values[customer_nr]" size="10" value="{customer_nr}"></td>
		<td class="must">{lang_coordinator}:</td>
		<td>
			<!-- BEGIN clist -->
			<select name="accountid">{coordinator_list}</select>
			<!-- END clist -->

			<!-- BEGIN cfield -->
			<input type="hidden" id="cordinatorid" name="accountid" value="{accountid}">
			<table border="0" cellspacing="0" cellpadding="1">
				<tr>
					<td width="185"><input type="text" id="cordinator" name="accountname" style="width:175px;" value="{accountname}" readonly /></td>
					<td width="105"><input type="button" style="width:100px;" value="{lang_select}" onClick="open_popup('{accounts_link}')" /></td>
					<td>{tooltip_select_coordinator}</td>
				</tr>
			</table>
			<!-- END cfield -->
		</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_organization}:</td>
		<td>
			<input type="hidden" id="orgaid" name="customer_org" value="{customer_org}" />
			<input type="text" id="organame" style="width:95%;" size="35" value="{customer_org_name}" readonly />
		</td>
		<td>{lang_employees}:</td>
		<td>
			<!-- BEGIN elist -->
			<select style="width:150px;" name="employees[]" multiple>{employee_list}</select>
			<!-- END elist -->
	
			<!-- BEGIN efield -->
			<table border="0" cellspacing="0" cellpadding="1">
				<tr>
					<td rowspan="3" width="185" valign="top"><select style="width:175px;" id="staff" name="employees[]" size="4" multiple>{employee_list}</select></td>
					<td width="105"><input style="width:100px;" type="button" value="{lang_adapt}" onClick="getParentMember('staff');"{parent_project_members_button_disable} /></td>
					<td>{tooltip_parent_project_members}</td>
				</tr>
				<tr>
					<td width="105"><input style="width:100px;" type="button" value="{lang_select}" onClick="open_popup('{e_accounts_link}');"></td>
					<td>{tooltip_select_project_members}</td>
				</tr>
				<tr>
					<td width="105"><input style="width:100px;" type="button" value="{lang_remove}" onClick="clearOptions('staff');"></td>
					<td>{tooltip_remove_project_members}</td>
				</tr>
			</table>
			<!-- END efield -->
		</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_person}:</td>
		<td>
			<input type="hidden" id="customerid" name="abid" value="{abid}">
			<input type="text"   id="customer" name="name" style="width:95%;" size="40" value="{name}" readonly></td>
		</td>
		<td>{lang_salesmanager}:</td>
		<td>
			<input type="hidden" id="salesmanagerid" name="salesmanagerid" value="{salesmanagerid}">
			<table border="0" cellspacing="0" cellpadding="1">
				<tr>
					<td rowspan="2" width="185"><input type="text" id="salesmanager" name="salesmanagername" style="width:175px;" value="{salesmanagername}"></td>
					<td width="105"><input type="button" style="width:100px;" value="{lang_select}" onClick="open_popup('{s_accounts_link}')" title="123&#164;{title_select_salesmanager}" readonly/></td>
					<td>{tooltip_select_salesmanager}</td>
				</tr>
				<tr>
					<td width="105"><input style="width:100px;" type="button" value="{lang_remove}" onClick="clearFields(Array('salesmanagerid', 'salesmanager'));"></td>
					<td>{tooltip_remove_salesmanager}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor="{row_on}" align="center">
		<td></td>
		<td>
			<input type="button" value="{lang_select}" style="width:100px;" onClick="open_popup('{addressbook_link}');" />&nbsp;
			<input type="button" value="{lang_remove}" style="width:100px;" onClick="clearCustomer();">
		</td>
		<td colspan="2">{edit_roles_events_button}</td>
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
		<td>
			<input type="{plan_bottom_up_input_type}" name="values[plan_bottom_up]" value="{plan_bottom_up_input_value}"{plan_bottom_up_input_checked}>{plan_bottom_up_text}
		</td>
		<td>
		</td>
	</tr>

	<tr bgcolor="{row_off}">
		<td>{lang_budget}:</td>
		<td>
			<input type="radio" name="budgetradio" value="m" checked onMouseUp="switch_budget_type(this.value);">{lang_monetary}
			<input type="radio" name="budgetradio" value="h"         onMouseUp="switch_budget_type(this.value);">{lang_timed}
		</td>
		<td>
			<div id="mbudget" style="display: block">
				<input style="text-align: right" type="text" name="values[budget]" value="{budget}">&nbsp;[{currency}.c]
			</div>
			<div id="hbudget" style="display: none">
				<input style="text-align: right" type="text" name="values[ptime]" value="{ptime}" />&nbsp;[h]
			</div>
		</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td>{lang_extra_budget}:</td>
		<td>
		</td>
		<td>
			<input style="text-align: right" type="text" name="values[e_budget]" value="{e_budget}" />&nbsp;[{currency}.c]
		</td>
	</tr>

<!-- BEGIN accounting_act -->

	<tr bgcolor="{row_off}">
		<td>{lang_bookable_activities}:</td>
		<td colspan="2"><select name="book_activities[]" multiple>{book_activities_list}</select></td>
	</tr>

	<tr bgcolor="{row_on}">
		<td>{lang_billable_activities}:</td>
		<td colspan="2"><select name="bill_activities[]" multiple>{bill_activities_list}</select></td>
	</tr>

<!-- END accounting_act -->

<!-- BEGIN accounting_own -->

	<tr bgcolor="{row_off}">
		<td valign="top">{lang_accounting}:</td>
		<td valign="top" colspan="2">
			<select id="factortype" name="values[accounting]" onChange="set_factortr()">
				<!-- <option value="">{lang_select_factor}</option> -->
				<option value="project" {acc_project_selected}>{lang_factor_project}</option>
				<option value="employee" {acc_employee_selected}>{lang_factor_employee}</option>
			</select>
		</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td class="must" style="white-space:nowrap; vertical-align:top">
			<div id="td1">
				{lang_accounting_factor_for_project}:
			</div>
		</td>
		<td>
			<div id="td2">
				<input type="radio" name="values[radio_acc_factor]" value="hour" checked onMouseUp="factor_calculator(this.value);">{lang_per_hour}
				<input type="radio" name="values[radio_acc_factor]" value="day"  onMouseUp="factor_calculator(this.value);">{lang_per_day}
			</div>
		</td>
		<td>
			<div id="td3">
				<input style="text-align: right" type="text" size="8" id="factor" name="values[project_accounting_factor]" value="{project_accounting_factor}">&nbsp;[{currency}.c]
			</div>
		</td>
	</tr>
<!-- END accounting_own -->

	<tr bgcolor="{row_on}">
		<td valign="top">{lang_invoicing_method}:</td>
		<td colspan="2">
			<textarea name="values[inv_method]" rows="6" cols="50" wrap="VIRTUAL">{inv_method}</textarea>
		</td>
	</tr>

{option_direct_work_handle}
{option_not_billable_handle}
{option_discount_handle}

</table>
</div>

<div id="tabcontent4" class="activetab">
<table class="contenttab">
	<tr bgcolor="{row_on}">
		<td valign="top">{lang_result}:</td>
		<td colspan="3"><textarea name="values[result]" rows="4" cols="50" wrap="VIRTUAL">{result}</textarea></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td valign="top">{lang_test}:</td>
		<td colspan="3"><textarea name="values[test]" rows="4" cols="50" wrap="VIRTUAL">{test}</textarea></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td valign="top">{lang_quality}:</td>
		<td colspan="3"><textarea name="values[quality]" rows="4" cols="50" wrap="VIRTUAL">{quality}</textarea></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td valign="top">{lang_files}:</td>
		<td valign="top">{attachment}</td>
		<td valign="top">{lang_attach}:</td>
		<td valign="top"><input type="file" name="attachment" /></td>
	</tr>

<!-- begin msfield1

	<tr bgcolor="{row_on}">
		<td valign="top">{lang_milestones}:</td>
		<td colspan="2">
			<table width="100%" border="0" cellspacing="2" cellpadding="2">

-- end msfield1 --
-- begin mslist --

				<tr>
					<td width="50%"><a href="{ms_edit_url}">{s_title}</a></td>
					<td width="50%">{s_edateout}</td>
				</tr>

-- end mslist --

-- begin msfield2 --
				</table>
		</td>
		<td valign="top" align="right"><input type="submit" name="mstone" value="{lang_add_mstone}"></td>
	</tr>
end msfield2 -->
</table>
</div>

<table width="100%" border="0" cellspacing="2" cellpadding="2">
	<tr valign="bottom" height="50" width="100%">
		<!--td width="25%">
			<input type="hidden" name="values[old_status]" value="{old_status}">
			<input type="hidden" name="values[old_parent]" value="{old_parent}">
			<input type="hidden" name="values[old_edate]" value="{old_edate}">
			<input type="hidden" name="values[old_coordinator]" value="{old_coordinator}">
			<input type="submit" name="apply" value="{lang_apply}">
		</td-->
		<td width="35%"><input type="submit" onClick="selectAll('staff'); return true;" name="save" value="{lang_save}" tabindex="1"></td>
		<td width="30%" align="center">{delete_button}</td>
		<td width="35%" align="right"><input type="submit" name="cancel" value="{lang_cancel}"></td>
	</tr>
</form>
</table>

<script language="JavaScript1.1" type="text/javascript">
<!--
  var tab = new Tabs(4,'activetab','inactivetab','tab','tabcontent','','','tabpage');
  tab.init();
  switch_budget_type('{budget_type}');
  set_factortr();
// -->
</script>

<!-- BEGIN option_direct_work -->
	<tr bgcolor="{row_off}">
		<td>{lang_direct_work}:</td>
		<td colspan="2"><input type="{direct_work_input_type}" name="values[direct_work]" value="{direct_work_input_value}"{direct_work_input_checked}>{direct_work_text}</td>
	</tr>
<!-- END option_direct_work -->

<!-- BEGIN option_not_billable -->
	<tr bgcolor="{row_on}">
		<td>{lang_non_billable}:</td>
		<td colspan="2"><input type="checkbox" name="values[billable]" value="True" {acc_billable_checked}></td>
	</tr>
<!-- END option_not_billable -->

<!-- BEGIN option_discount -->
	<tr bgcolor="{row_on}">
		<td valign="top">{lang_discount}:</td>	
		<td valign="top">
			<select name="values[discount_type]">
				<option value="no" {dt_no}>{lang_nodiscount}</option>
				<option value="percent" {dt_percent}>{lang_percent}&nbsp;[%]</option>
				<option value="amount" {dt_amount}>{lang_amount}&nbsp;[{currency}.c]</option>
			</select>
		</td>
		<td>
			<input type="text" name="values[discount]" value="{discount}"></td>
		</td>
	</tr>
<!-- END option_discount -->
