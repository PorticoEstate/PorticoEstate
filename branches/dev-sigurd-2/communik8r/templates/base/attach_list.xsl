<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:phpgw="http://dtds.phpgroupware.org/phpgw.dtd"
	xmlns:phpgwapi="http://dtds.phpgroupware.org/phpgwapi.dtd"
	xmlns:communik8r="http://dtds.phpgroupware.org/communik8r.dtd">
	<xsl:output method="xml" indent="yes" />
	<xsl:template match="/">
		<ul>
		<xsl:for-each select="/phpgw:response/communik8r:attachments/communik8r:attachment">
			<xsl:call-template name="attach_item" />
		</xsl:for-each>
		</ul>
	</xsl:template>
	<xsl:template name="attach_item">
		<li id="attachment_{@id}">
			<img src="{@icon}" 
				title="{.}" /><br />
			<xsl:value-of select="." /><br />
			<xsl:value-of select="@size" />
		</li>
	</xsl:template>
</xsl:stylesheet>
