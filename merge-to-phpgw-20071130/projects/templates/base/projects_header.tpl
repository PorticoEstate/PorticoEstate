<!-- $Id: projects_header.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
<script language="JavaScript" type="text/javascript">
document.getElementsByTagName("body")[0].style.backgroundColor = "white";
</script>
<!-- BEGIN projects_header -->
<style>
body
{
	background-color: #ffffff;
}

input, select, textarea
{
	border: 1px solid #808080;
	background-color: #ffffff;
}

fieldset
{
	border: 1px solid #808080;
	padding: 1px 6px 6px 6px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 9pt;
	color: #000000;
}

fieldset.menu_toolbar
{
	padding: 1px 6px 6px 6px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 1pt;
	white-space: nowrap;
	height:47px; 
	min-height:47px; 
	max-height:47px; 
    x-height: 47px;
    float: left;
}


legend
{
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #000000;
}

legend.headline
{
	font-size: 9pt;
}

legend.menu_toolbar
{
	font-size: 7pt;
}

div.menu_button, div.menu_button_active, div.menu_button_inactive
{
	float: left;
	margin: 2px;
	padding: 4px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 8pt;
	text-align: center;
	vertical-align: middle;
	color: #000000;
	text-align: center;
	-moz-border-radius: 8px;
	white-space: nowrap;
}

div.menu_button
{
	border: 2px solid #909090;
}

img.menu_button, img.menu_button_active, img.menu_button_inactive
{
	text-align: center;
	vertical-align: middle;
	width: 20px;
	height: 20px;
}

img.menu_button_inactive
{
	filter: alpha(opacity=40);
	-moz-opacity:40%;
}

div.menu_button_active
{
	border: 3px solid #000090;
}

div.menu_button_inactive
{
	border: 2px solid #e0e0e0;
	filter: alpha(opacity=40);
	-moz-opacity:40%;
}

a.menu_button, a.menu_button_active, a.menu_button_inactive
{
	text-decoration: none;
	white-space: nowrap;
}

div.menu_icon, div.menu_icon_active, div.menu_icon_inactive
{
	float: left;
	margin: 3px;
	padding: 3px;
	width: 15px;
	height: 15px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 8pt;
	text-align: center;
	vertical-align: middle;
	color: #000000;
	text-align: center;
	-moz-border-radius: 8px;
}

div.appgroup
{
    width:100%;
    float:left;
}

img.menu_icon_inactive
{
	filter: alpha(opacity=40);
	-moz-opacity:40%;
}

</style>
<div class="appgroup">{projects_menu}</div>
<br style="float:clear" />
<form method="POST" name="select_pro" action="{select_pro_action}">
  <div class="appgroup">
    <div style="margin-left:5px; margin-top:8px; white-space:nowrap; float:left; width:100%; overflow:hidden">{up_button}&nbsp;<select style="width:85%; overflow:hidden" name="project_id" onChange="this.form.submit();">{select_pro_options}</select></div>
  </div>
</form>
<div class="appgroup">
<fieldset>
<legend class="headline">[&nbsp;{headline}&nbsp;]</legend>
<!-- END projects_header -->

<!-- BEGIN projects_menu_toolbar -->
<fieldset class="menu_toolbar" style="margin-right:5px">
  <legend class="menu_toolbar">[&nbsp;{toolbar_name}&nbsp;]</legend>
    <div style="float:left; white-space: nowrap;">{toolbar_icons}</div>
</fieldset>
<!-- END projects_menu_toolbar -->
