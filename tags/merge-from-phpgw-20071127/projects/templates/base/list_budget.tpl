<!-- $Id: list_budget.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"></div>
<!-- BEGIN project_main -->
<!--
<table border="0" width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="{th_bg}">
		<td colspan="7"><b>{lang_main}:&nbsp;<a href="{main_url}">{title_main}</a></b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_number}:</td>
		<td colspan="2">{number_main}</td>
		<td>{lang_url}:</td>
		<td colspan="3"><a href="http://{url_main}" taget="_blank">{url_main}</a></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_coordinator}:</td>
		<td colspan="2">{coordinator_main}</td>
		<td>{lang_customer}:</td>
		<td colspan="3">{customer_main}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_budget}:&nbsp;{currency}</td>
		<td>{lang_planned}:</td>
		<td>{pbudget_main}</td>
		<td>{lang_used_total} {lang_plus_jobs}:</td>
		<td>{ubudget_main}</td>
		<td>{lang_available} {lang_plus_jobs}:</td>
		<td>{abudget_main}</td>
	</tr>
</table>
-->
<!-- END project_main -->

<!-- Some Styles and JS for pretty listings -->	
	<style type="text/css">
		div.value_red {
			color:#cc0000;
			text-align:right;
		}
	</style>
	<style type="text/css">
		div.value_yellow {
			color:orange;
			text-align:right;
		}
	</style>
	<style type="text/css">
		div.value_green {
			color:black;
			text-align:right;
		}
	</style>
	
	<style type="text/css">
		div.leaf_item {
			display:block;
			text-align:right;
		}
	</style>
	<style type="text/css">
		div.leaf_sum {
			display:none;
			text-align:right;
		}
	</style>
	<style type="text/css">
		div.node_item {
			display:block;
			text-align:right;
		}
	</style>
	<style type="text/css">
		div.node_sum {
			display:block;
			text-align:right;
		}
	</style>

	<script type="text/javascript">
		function getStyleSheet(name)
		{
			if(!name || !document.styleSheets) {
				return null;
			}
			var i = document.styleSheets.length;
			while(i--)
			{
				var rules = document.styleSheets[i].rules ? document.styleSheets[i].rules :
				document.styleSheets[i].cssRules;
				var j = rules.length;
				while(j--) { 
					names = rules[j].selectorText.split(",");
					for (var k=0; k<names.length; k++) {
						var p = names[k].indexOf("[class~=");
						var s = (p>=0)? names[k].substring(0,p) : names[k];
						if(s.toLowerCase() == name.toLowerCase()) 
							return rules[j]; 
					}
				}
			}
			return null;
		}

		function setStyle(name, attr, value)
		{
			var rule = getStyleSheet(name);
			if(!rule) {
				alert("could not find stylerule "+name);
				return null;
				}
			if(value) rule.style[attr] = value;
			return rule.style[attr];
		}

		function sum_switch()
		{
			value = setStyle("div.node_sum", "display");
			if (value != 'none') 
				setStyle("div.node_sum", "display", "none");
			else
				setStyle("div.node_sum", "display", "block");
		}
		
	</script>
<!--
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr>
		<td colspan="7">
			<table border="0" width="100%">
				<tr>
				{left}
					<td align="center">{lang_showing}</td>
				{right}
				</tr>
			</table>
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td width="25%">
			<form method="POST" action="{action_url}">
			{action_list}
			</form>
		</td>
		<td width="20%" align="center">
			<form method="POST" name="status" action="{action_url}">
				<select name="status" onChange="this.form.submit();">{status_list}</select>
			</form>
		</td>
		<td width="20%" align="center"><form method="POST" name="filter" action="{action_url}">{filter_list}</form></td>
		<td nowrap="nowrap" width="35%" align="right"><form method="POST" name="query" action="{action_url}">{search_list}</form></td>
	</tr>
</table>
-->
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td colspan="6"><b>{currency}&nbsp;{lang_budget}</b>&nbsp;&nbsp;&nbsp;<a href="javascript:sum_switch()">{lang_in_out_sum}</a></td>
	</tr>
	<tr bgcolor="{th_bg}"  valign="top">
		<td width="10%" valign="top">{sort_number}</td>
		<td width="40%" valign="top">{sort_title}</td>
		<td width="5%" align="right" valign="top">{sort_planned}</td>
		<td width="10%" align="right">{lang_used_total}</td>
		<td width="10%" align="right">{lang_used_billable}</td>
		<td width="10%" align="right">{lang_used_not_billable}</td>
		<td width="10%" align="right">{lang_available}</td>
		<td width="5%">&nbsp;</td>
	</tr>

<!-- BEGIN projects_list -->

	<tr bgcolor="{tr_color}">
		<td>{number}</td>
		<td><a href="{sub_url}">{title}</a></td>
		<td align="right">
			<div class="{list_class_sum}">
				<div class="{value_class_sum}"><strong>{p_budget_jobs}</strong></div>
			</div>
			<div class="{list_class_item}">
				<div class="{value_class_item}">{p_budget}</div>
			</div>
		</td>
		<td align="right">
			<div class="{list_class_sum}">
				<div class="{value_class_sum}" align="right"><strong>{u_budget_jobs}</strong></div>
			</div>
			<div class="{list_class_item}">
				<div class="{value_class_item}" align="right">{u_budget}</div>
			</div>
		</td>
		<td align="right">
			<div class="{list_class_sum}">
				<div class="{value_class_sum}" align="right"><strong>{b_budget_jobs}</strong></div>
			</div>
			<div class="{list_class_item}">
				<div class="{value_class_item}" align="right">{b_budget}</div>
			</div>
		</td>
		<td align="right">
			<div class="{list_class_sum}">
				<div class="{value_class_sum}" align="right"><strong>{n_budget_jobs}</strong></div>
			</div>
			<div class="{list_class_item}">
				<div class="{value_class_item}" align="right">{n_budget}</div>
			</div>
		</td>
		<td align="right">
			<div class="{list_class_sum}">
				<div class="{value_class_sum}" align="right"><strong>{a_budget_jobs}</strong></div>
			</div>
			<div class="{list_class_item}">
				<div class="{value_class_item}" align="right">{a_budget}</div>
			</div>
		</td>
		<td align="right">
			<div class="{list_class_sum}">
				<div class="{value_class_sum}" align="left" style="white-space:nowrap;"><strong>{lang_plus_jobs}</strong></div>
			</div>
			<div class="{list_class_item}">
				<div class="{value_class_item}" align="left">{lang_project}</div>
			</div>
		</td>

	</tr>

<!-- END projects_list -->

	<tr height="15">
		<td>&nbsp;</td>
	<tr>
	<tr bgcolor="{th_bg}">
		<td colspan="2"><b>{lang_sum_budget}:&nbsp;{currency}</b></td>
		<td align="right">
			<div><strong>{sum_budget_jobs}</strong></div>
			<div>{sum_budget}</div>
		</td>
		<td align="right">
			<div><strong>{sum_u_budget_jobs}</strong></div>
			<div>{sum_u_budget}</div>
		</td>
		<td align="right">
			<div><strong>{sum_b_budget_jobs}</strong></div>
			<div>{sum_b_budget}</div>
		</td>
		<td align="right">
			<div><strong>{sum_n_budget_jobs}</strong></div>
			<div>{sum_n_budget}</div>
		</td>
		<td align="right">
			<div><strong>{sum_a_budget_jobs}</strong></div>
			<div>{sum_a_budget}</div>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
</center>
