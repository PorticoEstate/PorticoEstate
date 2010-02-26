<!-- $Id: tts.xsl 4859 2010-02-18 23:09:16Z sigurd $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="helpdesk">
				<xsl:apply-templates select="helpdesk"/>
			</xsl:when>
			<xsl:when test="add_ticket">
				<xsl:apply-templates select="add_ticket"/>
			</xsl:when>
			<xsl:when test="contract">
				<xsl:apply-templates select="contract"/>
			</xsl:when>
			<xsl:when test="demo_1">
				<xsl:apply-templates select="demo_1"/>
			</xsl:when>
			<xsl:when test="demo_2">
				<xsl:apply-templates select="demo_2"/>
			</xsl:when>
			<xsl:when test="demo_3">
				<xsl:apply-templates select="demo_3"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="list">
	</xsl:template>

