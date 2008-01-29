<?xml version="1.0"?>	
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output indent="yes"/>

<xsl:template match="APP">
	<center><b><xsl:value-of select="text"/></b></center><br/>
	<center><b>Goto Setup prog <a href="{$baseurl}&amp;op=api.setup.start">click here</a></b></center><br/>
	<center><b>To create users <a href="{$baseurl}&amp;op=admin.admin.adduser">click here</a></b></center>
</xsl:template> 

</xsl:stylesheet>
