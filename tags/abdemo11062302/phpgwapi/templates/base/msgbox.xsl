<!-- $Id$ -->

	<xsl:template name="msgbox">
		<xsl:if test="msgbox_data/msgbox_text != ''">
			<xsl:apply-templates select="msgbox_data"/>
		</xsl:if>
	</xsl:template>

	<xsl:template match="msgbox_data">
		<div>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="msgbox_class != ''">
						<xsl:value-of select="msgbox_class" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>error</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<xsl:choose>
				<xsl:when test="msgbox_img != ''">
					<xsl:variable name="msgbox_img"><xsl:value-of select="msgbox_img" /></xsl:variable>
					<xsl:variable name="msgbox_img_alt"><xsl:value-of select="msgbox_img_alt"/></xsl:variable>
					<img src="{$msgbox_img}" alt="{$msgbox_img_alt}" title="{$msgbox_img_alt}" />
				</xsl:when>
			</xsl:choose>
			<xsl:value-of disable-output-escaping="yes" select="msgbox_text" />
		</div>
	</xsl:template>
