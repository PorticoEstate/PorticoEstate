<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="schedule">
			<xsl:apply-templates select="schedule"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="schedule" name="schedule">
	<xsl:call-template name="rental_schedule">
		<xsl:with-param name="schedule" select ='schedule'/>
	</xsl:call-template>
</xsl:template>