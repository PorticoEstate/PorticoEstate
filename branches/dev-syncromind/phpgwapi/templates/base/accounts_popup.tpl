<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" 
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<!-- $Id$ -->
<html lang="en">
	<head>
		<title>{title}</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">

		<script type="text/javascript">
			var userSelectBox = opener.document.forms["app_form"].elements['{select_name};

			function ExchangeAccountSelect(thisform)
			{
				NewEntry = new Option(thisform.elements[1].value,thisform.elements[0].value,false,true);
				userSelectBox.options[userSelectBox.length] = NewEntry;
			}

			function ExchangeAccountText(thisform)
			{
				opener.document.app_form.accountid.value = thisform.elements[0].value;
				opener.document.app_form.accountname.value = thisform.elements[1].value;
			}
		</script>
		<link rel="stylesheet" type="text/css" href="{css_file}">
	</head>
	<body>
		<table border="0" width="100%">
			<tr>
				<td colspan="4">
					<table border="0" width="100%">
						<tr>
						{left}
							<td align="center">{lang_showing}</td>
						{right}
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width="100%" colspan="4" align="right">
					<form method="POST" action="{search_action}">
					<input type="text" name="query">&nbsp;<input type="submit" name="search" value="{lang_search}">
					</form>
				</td>
			</tr>
		</table>

		<div style="float: left; width: 200px;">
			<h2>{lang_groups}</h2>
			<!-- BEGIN withperm_intro -->
			<h3>{lang_perm}</h3>
			<!-- END withperm_intro -->
			<ul>
				<!-- BEGIN group_select -->
				<li class="tr_class">
					<a href="{link_user_group}">{name_user_group}</a>
					<form>
						<input type="hidden" name="hidden" value="{accountid}">
						<input type="hidden" name="hidden" value="{account_display}">
						<input type="image" src="{img}" onClick="{js_function}(this.form); return false;" name="{lang_select_group}" title="{lang_select_group}">
					</form>
				</li>
				<!-- END group_select -->

				<!-- BEGIN group_other -->
				<li class="{tr_class}"><a href="{link_user_group}">{name_user_group}</a></li>
				<!-- END group_other -->
			</ul>

			<!-- BEGIN withoutperm_intro -->
			<h3>{lang_nonperm}</h3>
			<!-- END withoutperm_intro -->
			<ul>
				<!-- BEGIN group_all -->
				<li class="{tr_class}"><a href="{link_all_group}">{name_all_group}</a><li>
				<!-- END group_all -->
			</ul>
		</div>

		<div>
			<table width="600">
				<thead>
					<tr>
						<th colspan="3">{lang_accounts}</td>
					</tr>
						<th width="250">{sort_firstname}</th>
						<th width="250">{sort_lastname}</th>
						<th width="20">&nbsp;</td>
					</tr>
				</thead>
				<tbody>
					<!-- BEGIN accounts_list -->
					<tr class="{tr_class}">
						<td width="250">{firstname}</td>
						<td width="250">{lastname}</td>
						<td width="20">
							<form style="padding: 0px; margin: 0px; width: 20px;">
								<input type="hidden" name="hidden" value="{accountid}">
								<input type="hidden" name="hidden" value="{account_display}">
								<input type="image" src="{img}" onClick="{js_function}(this.form); return false;" name="{lang_select_user}" title="{lang_select_user}">
							</form>
						</td>
					</tr>
					<!-- END accounts_list -->
				</tbody>
			</table>
		</div>

		<div class="button_group" style="clear: both;">
			<form>  
				<input type="hidden" name="start" value="{start}">
				<input type="hidden" name="sort" value="{sort}">
				<input type="hidden" name="order" value="{order}">
				<input type="hidden" name="query" value="{query}">
				<input type="hidden" name="group_id" value="{group_id}">
				<input type="button" name="Done" value="{lang_done}" onClick="window.close()">
			</form>
		</div>
	</body>
</html>
