<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:phpgw="http://dtds.phpgroupware.org/phpgw.dtd"
	xmlns:phpgwapi="http://dtds.phpgroupware.org/phpgwapi.dtd"
	xmlns:communik8r="http://dtds.phpgroupware.org/communik8r.dtd">
	<xsl:output method="xml" indent="yes" />
	<xsl:template match="/">
		<xsl:variable name="subicon" select="/communik8r:accounts/@subicon"/>
		<xsl:variable name="noicon" select="/communik8r:accounts/@noicon"/>
		<div id="folders">
			<ul>
				<xsl:for-each select="/communik8r:accounts/communik8r:account">
					<xsl:call-template name="account">
						<xsl:with-param name="subicon" select="$subicon"/>
						<xsl:with-param name="noicon" select="$noicon"/>
					</xsl:call-template>
				</xsl:for-each>
			</ul>
		</div>
	</xsl:template>
	
	<xsl:template name="account">
		<xsl:param name="subicon"/>
		<xsl:param name="noicon"/>
		<li class="acct_folder" id="{concat('acct_', .)}">
			<strong>
				<xsl:choose>
					<xsl:when test="@subs='true'">
						<img src="{$subicon}" alt="sub folders"/>
					</xsl:when>
					<xsl:otherwise>
						<img src="{$noicon}" alt="no sub folders"/>
					</xsl:otherwise>
				</xsl:choose>
				<img src="{@icon}" alt="folder icon" /><xsl:value-of select="." />
			</strong>
		</li>
	</xsl:template>
</xsl:stylesheet>
