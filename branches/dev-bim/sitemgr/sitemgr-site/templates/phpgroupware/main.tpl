<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>{sitename}: {title}</title>
		<LINK REL="StyleSheet" HREF="templates/phpgroupware/style/style.css" TYPE="text/css">

		<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
		<META HTTP-EQUIV="EXPIRES" CONTENT="0">
		<META NAME="RESOURCE-TYPE" CONTENT="DOCUMENT">
		<META NAME="DISTRIBUTION" CONTENT="GLOBAL">
		<META NAME="AUTHOR" CONTENT="{sitename}">
		<META NAME="COPYRIGHT" CONTENT="Copyright (c) 2002 by {sitename}">
		<META NAME="DESCRIPTION" CONTENT="{slogan}">
		<META NAME="ROBOTS" CONTENT="INDEX, FOLLOW">
		<META NAME="REVISIT-AFTER" CONTENT="1 DAYS">
		<META NAME="RATING" CONTENT="GENERAL">
		<META NAME="GENERATOR" CONTENT="phpGroupWare Web Site Manager">
	</head>
<BODY BGCOLOR="#4F748A" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="510" border="0" cellspacing="0" cellpadding="1" bgcolor="#FFFFFF" height="100%" align="center">
  <tr> 
    <td valign="top"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%" align="center">
        <tr> 
			<td height="102" width="684"> 
            <table width=684 border=0 cellpadding=0 cellspacing=0>
              <tr> 
                <td width=124 height=82><img src="templates/phpgroupware/images/logo.gif" width=124 height=82></td>
                <td width=130 height=82><img src="templates/phpgroupware/images/header01.gif" width=130 height=82></td>
                <td width=430 height=82><img src="templates/phpgroupware/images/header02.gif" width=430 height=82></td>
              </tr>
              <tr> 
                <td width=124 height=20><img src="templates/phpgroupware/images/layout02-header_04.gif" width=124 height=20 align="top"></td>
                <td width=130 height=20 background="templates/phpgroupware/images/current_release.gif"> 
					<div class="release" align="center">
						current release:&nbsp;
						<a href="{?page_name=downloads"}>
							<span class="release-num">0.9.14</span>
						</a>
                  </div>
                </td>
                <td width="430" height="20" background="templates/phpgroupware/images/current_release.gif"> <table border="0" cellspacing="0" cellpadding="0" height="20" width="100%">
						  <tr>
							<td width="86"><a href="{?home=1}"><img src="templates/phpgroupware/images/home.gif" width="86" height="20" hspace="0" vspace="0" border="0" alt="Home"></a></td>
							<td width="86"><a href="{?category_id=1}"><img src="templates/phpgroupware/images/about.gif" width="86" height="20" hspace="0" vspace="0" border="0" alt="About"></a></td>
							<td width="86"><a href="{?category_id=5}"><img src="templates/phpgroupware/images/documentation.gif" width="86" height="20" hspace="0" vspace="0" border="0" alt="Documentation"></a></td>
							<td width="86"><a href="{?category_id=3}"><img src="templates/phpgroupware/images/support.gif" width="86" height="20" hspace="0" vspace="0" border="0" alt="Support"></a></td>
							<td width="86"><a href="{?page_name=downloads}"><img src="templates/phpgroupware/images/downloads.gif" width="86" height="20" hspace="0" vspace="0" border="0" alt="Downloads"></a></td>
					</tr></table> 
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr> 
          <td valign="top"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%" bgcolor="#52809E">
              <tr> 
                <td valign="top" bgcolor="#52809E" width="*%"> 
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr> 
                      <td> 
						  <p><b><font face="Verdana, Arial, Helvetica, sans-serif"><img src="templates/phpgroupware/images/spacer.gif" width="39" height="1"></font></b><br>
						   <font color="#ffffff">
								<a href="{?phpgw:/index.php,}">Logged in as:</a> {user}<br>
							  {contentarea:header}
						  </font>
						 </p>
						 <h1>{title}</h1>
						 <h3>{subtitle}</h3>
						 {contentarea:center}
                        <br>
                      </td>
                    </tr>
                  </table>
                </td>
                <td valign="top" bgcolor="#FFFFFF" width="1"><img src="templates/phpgroupware/images/spacer.gif" width="1" height="80"></td>
                <td valign="top" bgcolor="#52809E" width="160"> 
                  <table width="160" border="0" cellspacing="0" cellpadding="0" align="center">
                    <tr> 
                      <td> 
                        <div align="center"><b><font face="Verdana, Arial, Helvetica, sans-serif"><img src="templates/phpgroupware/images/spacer.gif" width="39" height="1"></font></b><br>
							{contentarea:right}
                        </div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr> 
          <td>
			<p style="background:#FFFFFF;color:#537991;text-align:center" height="5">
            {contentarea:footer}&nbsp;&nbsp;&nbsp;::&nbsp;&nbsp;&nbsp;<a style="color:#537991;" href="{?page_name=privacy}">privacy  policy</a>&nbsp;&nbsp;&nbsp;:: 
                &nbsp;&nbsp;&nbsp;Copyright &copy; 2000 - 
                2002 phpGroupWare&nbsp;&nbsp;&nbsp;::&nbsp;&nbsp;&nbsp;
				<a style="color:#537991;" href="http://www.ov-media.com/">site design by ov media</a><br>
                <img src="templates/phpgroupware/images/spacer.gif" width="510" height="1"></p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>