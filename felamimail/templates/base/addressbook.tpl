<!-- $Id: addressbook.tpl 17752 2006-12-22 10:36:52Z sigurdne $ -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML LANG="en">
<head>
<title>{title}</title>
<meta http-equiv="content-type" content="text/html; charset={charset}">
    <!-- BEGIN theme_stylesheet -->
    <link href="{theme_style}" type="text/css" rel="StyleSheet">
    <!-- END theme_stylesheet -->

<script LANGUAGE="JavaScript">
	function ExchangeTo(thisform)
	{
		var address;
	
		address = thisform.elements[1].value+' <'+thisform.elements[0].value+'>';
		//alert(address);
		if (opener.document.doit.to.value =='') 
		{
			opener.document.doit.to.value = address;
		}
		else 
		{
			opener.document.doit.to.value +=", "+address;
		}
	}
	
	function ExchangeCc(thisform)
	{
		var address;
		
		address = thisform.elements[1].value+' <'+thisform.elements[0].value+'>';
		if (opener.document.doit.cc.value=='') 
		{
			opener.document.doit.cc.value=address;
		} 
		else 
		{
			opener.document.doit.cc.value+=", "+address;
		}
	}
	
	function closeWindow()
	{
		window.close();
	}
	
</script>
</head>
<body>
<center>
<p><font <b>{lang_addressbook_action}</b><br>
<hr noshade width="98%" align="center" size="1">

<table border="0" width="100%">
    <tr>
    <td width="33%" align="left">
    <form action="{cats_action}" name="form" method="POST">
    <select name="cat_id" onChange="this.form.submit();"><option value="">{lang_select_cats}</option>{cats_list}</select>
    <noscript>&nbsp;<input type="submit" name="submit" value="{lang_submit}"></noscript></form></td>
    <td width="33%" align="center">{lang_showing}</td>
    <td width="33%" align="right">
    <form method="POST" action="{search_action}">
    <input type="text" name="query">&nbsp;<input type="submit" name="search" value="{lang_search}">
    </form></td>
    </tr>
    <tr>
    <td colspan="4">
    <table border="0" width="100%">
    <tr>
    {left}
    <td>&nbsp;</td>
    {right}
    </tr>
    </table>
    </td>
    </tr>
</table>
<table border="0" width="100%" cellpadding="2" cellspacing="2">
    <tr class="th">
      <td width="14%" class="th" align=center><font face="{font}">{sort_firstname}</font></td>
      <td width="14%" class="th" align=center><font face="{font}">{sort_lastname}</font></td>
      <td width="26%" class="th" align=center><font face="{font}">{lang_email}</font></td>
      <td width="26%" class="th" align=center><font face="{font}">{lang_hemail}</font></td>
    </tr>
  
<!-- BEGIN addressbook_list -->
    <tr class="{tr_class}">
	<td><font face="{font}">{firstname}</font></td>
        <td><font face="{font}">{lastname}</font></td>
        <form>
        <td align="center">
        	<input type="text" size="25" name="email" value="{email}">
        	<input type="hidden" name="realName" value="{realName}">
        	<input type="button" size="25" name="button" value="To" onClick="ExchangeTo(this.form);">
        	<input type="button" size="25" name="button" value="Cc" onClick="ExchangeCc(this.form);">
        </td>
        </form>
        <form>
        <td align="center">
        	<input type="text" size="25" name="hemail" value="{hemail}">
        	<input type="hidden" name="realName" value="{realName}">
        	<input type="button" size="25" name="button" value="To" onClick="ExchangeTo(this.form);">
        	<input type="button" size="25" name="button" value="Cc" onClick="ExchangeCc(this.form);">
        </td>
        </form>
      </tr>
<!-- END addressbook_list -->

</table>
<br>
<table cellpadding="2" cellspacing="2">
	<tr>
		<td>
			<form>
			<font face="{font}">
			<a href="javascript:closeWindow();">{lang_done}</a>
			</font>
			</form>
		</td>
	</tr>
</table>
</center>
</body>
</html>
