<!-- $Id$ -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML LANG="en">
	<head>
		<title>{title}</title>
		<meta http-equiv="content-type" content="text/html; charset={charset}">

       <!-- BEGIN theme_stylesheet -->
       <link href="{theme_style}" type="text/css" rel="StyleSheet">
       <!-- END theme_stylesheet -->

		<script type="text/javascript">
			function ExchangeTo(thisform)
			{
				if (opener.document.doit.to.value =='')
				{
					opener.document.doit.to.value = thisform.elements[0].value;
				}
				else
				{
					opener.document.doit.to.value +=","+thisform.elements[0].value;
				}
			}
			function ExchangeCc(thisform)
			{
				if (opener.document.doit.cc.value=='')
				{
					opener.document.doit.cc.value=thisform.elements[0].value;
				}
				else
				{
					opener.document.doit.cc.value+=","+thisform.elements[0].value;
				}
			}
			function ExchangeBcc(thisform)
			{
				if (opener.document.doit.bcc.value=='')
				{
					opener.document.doit.bcc.value=thisform.elements[0].value;
				}
				else
				{
					opener.document.doit.bcc.value+=","+thisform.elements[0].value;
				}
			}	
		</script>
	</head>
	<body bgcolor="{bg_color}">
		<p>
		<font face="{font}"><b>{lang_addressbook_action}</b></font>
		<br />
		<hr noshade width="98%" align="center" size="1">
		<table border="0" width="100%">
			<tr>
				<td width="33%" align="left">
					<form action="{cats_action}" name="form" method="POST">
						<select name="cat_id" onChange="this.form.submit();">
							<option value="">{lang_select_cats}</option>
							{cats_list}
						</select>
						<noscript>&nbsp;<input type="submit" name="submit" value="{lang_submit}"></noscript>
					</form>
				</td>
				<td width="33%" align="center">
					{lang_showing}
				</td>
				<td width="33%" align="right">
					<form method="POST" action="{search_action}">
						<table>
							<tr>
								<td>
									<input type="text" name="query">&nbsp;
								</td>
								<td>
									<select name="filter" onChange="this.form.submit();">
										{filter_list}
										<option value="" >Select Filter</option>
										<option value="private">Private</option>
										<option value="none">All</option>
										<option value="user_only">Only yours </option>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<center><input type="submit" name="search" value="{lang_search}"></center>
								</td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<table border="0" width="100%">
						<tr>
							{left}
							<td>
								&nbsp;
							</td>
							{right}
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table border="0" width="100%" cellpadding="2" cellspacing="2">
			<tr class="th">
				<td width="25%" align="center">
					{sort_firstname}
				</td>
				<td width="15%" align="center">
					{sort_lastname}
				</td>
				<td width="15%" align="center">
					{sort_company}
				</td>
				<td width="25%" align="center">
					{lang_email}
				</td>
				<td width="25%" align="center">
					{lang_hemail}
				</td>
			</tr>
  
			<!-- BEGIN addressbook_list -->
			<tr class="{tr_class}">
				<td>
					{firstname}
				</td>
				<td>
					{lastname}
				</td>
				<td>
					{company}
				</td>
				
				<td width="20%" align="center">
					<form>
						<input type="text" size="20" name="email" value="{email}">
						<br />
						<input type="button" size="25" name="button" value="To" onClick="ExchangeTo(this.form);">
						<input type="button" size="25" name="button" value="Cc" onClick="ExchangeCc(this.form);">
						<input type="button" size="25" name="button" value="Bcc" onClick="ExchangeBcc(this.form);">
					</form>
				</td>
				<td align="center">
					<form>
						<input type="text" size="25" name="hemail" value="{hemail}">
						<br />
						<input type="button" size="25" name="button" value="To" onClick="ExchangeTo(this.form);">
						<input type="button" size="25" name="button" value="Cc" onClick="ExchangeCc(this.form);">
						<input type="button" size="25" name="button" value="Bcc" onClick="ExchangeBcc(this.form);">
					</form>
				</td>
			</tr>
			<!-- END addressbook_list -->
		</table>
		<table cellpadding="2" cellspacing="2">
			<tr>
				<td>
					<form>
						<input type="button" name="done" value="{lang_done}" onClick="window.close()">
					</form>
				</td>
			</tr>
		</table>
	</body>
</html>
