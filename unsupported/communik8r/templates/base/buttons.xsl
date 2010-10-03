<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:phpgw="http://dtds.phpgroupware.org/phpgw.dtd"
	xmlns:phpgwapi="http://dtds.phpgroupware.org/phpgwapi.dtd"
	xmlns:communik8r="http://dtds.phpgroupware.org/communik8r.dtd">
	<xsl:output method="xml" indent="yes" />
	<xsl:template match="/">
		<xsl:for-each select="/phpgw:response/communik8r:response/communik8r:buttons/communik8r:button">
			<xsl:call-template name="button"/>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="button">
		<xsl:choose>
			<xsl:when test="@label='--'">
				<!--<hr />-->
			</xsl:when>
			<xsl:otherwise>
				<button type="button" id="{concat('button_', @id)}" accesskey="{@shortcut}" title="{@label}">
					<img src="{@icon}" id="{concat('button_icon_', @id)}" alt="" /><br />
					<xsl:value-of select="substring-before(@label, @shortcut)" /><span class="shortcut"><xsl:value-of
					select="@shortcut" /></span><xsl:value-of select="substring-after(@label, @shortcut)" />
				</button>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
