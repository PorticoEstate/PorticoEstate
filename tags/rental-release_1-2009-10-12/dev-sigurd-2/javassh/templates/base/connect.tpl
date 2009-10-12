<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>{pgtitle}</title>
<style type="text/css">
<!-- 
{css} 
-->
</style>
</head>
<body>
<table summary="javassh content holder" align="center">
<tr>
<td>
<applet CODEBASE="{applet_url}"
            ARCHIVE="{applet_file}"
            CODE="de.mud.jta.Applet" 
            WIDTH=590 HEIGHT=360>
		<param name="plugins" value="{plugins}">
		<param name="Applet.disconnect" value="true">
		<param name="Socket.host" value="{host}">
		<param name="Socket.port" value="{port}">
</applet>
</td>
</tr>
<tr>
<td align="center"><a href="javascript:window.close();">{lang_logout}</a></td>
</tr>
</table>
</body>
</html>
