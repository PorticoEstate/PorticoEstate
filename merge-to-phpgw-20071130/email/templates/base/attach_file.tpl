<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="Content-Type" content="text/html"; charset="{charset}">
  <meta name="author" content="AngleMail for phpGroupWare http://www.anglemail.org">
  <meta name="description" content="AngleMail for phpGroupWare http://www.phpgroupware.org">
  <meta name="keywords" content="AngleMail for phpGroupWare">
  <style type="text/css">
   <!--
   A:link {text-decoration:none;}
   A:visted {text-decoration:none;}
   A:active {text-decoration:none;}
   body { font-family: "{font_family}"; }
   center { text-decoration:none; }
   -->
  </style>
<script type="text/javascript">
	function copyback()
	{
	
		for(i=0;i<document.attach_form.elements.length;i++)
		{
		
			if(document.attach_form.elements[i].type == "hidden" )
			{
				if(window.opener.document.{form1_name}.attached_filenames.value)
				{
					comma=",";
				}
				else
				{
					comma="";
				}
				window.opener.document.{form1_name}.attached_filenames.value=window.opener.document.{form1_name}.attached_filenames.value+comma+document.attach_form.elements[i].value;
			}
		}
		
		window.close();
		 
	}
</script>

  <title>{page_title}</title>
</head>
<body {body_tags}>

<form  enctype="multipart/form-data"  name="attach_form"  method="{form_method}" action="{form_action}">
<table border="0">
{V_alert_msg}
<tr>
	<td>
		<strong>{text_attachfile}:</strong>
	</td>
</tr>
<tr>
	<td>
		{text_currattached}:
	</td>
</tr>

{V_attached_list}
{V_delete_btn}
<!-- either the 2 above vars or this one below -->
{V_attached_none}

<tr>
	<td>
		{txtbox_upload_desc}: <input type="file" name="{txtbox_upload_name}">
		&nbsp;<input type="submit" name="{btn_attach_name}" value="{btn_attach_value}">
	</td>
</tr>
<tr>
	<td align="center">
		<input type="button" name="{btn_done_name}" value="{btn_done_value}" onClick="{btn_done_js}">
	</td>
</tr>
</table>
</form>
</body>
</html>
