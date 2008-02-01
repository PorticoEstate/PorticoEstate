<?xml version="1.0"?>	
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output indent="yes"/>
<xsl:template match="APP">
	<center><b><xsl:value-of select="text"/></b></center><br/>
	<form method="post" action="index.php">
		<input type="hidden" name="op" value="admin.admin.adduser"/>
		username: <input name="username" style="width: 100px;"/><br/>
		password: <input name="passwd" style="width: 100px;"/><br/>
		first name: <input name="fname" style="width: 100px;"/><br/>
		last name: <input name="lname" style="width: 100px;"/><br/>
		Create as Admin: <input type="checkbox" name="isadmin" value="1"/>
		<input type="submit" value="doit" name="submit" />
	</form>
</xsl:template> 

</xsl:stylesheet>
