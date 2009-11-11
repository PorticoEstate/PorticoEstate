<func:function name="phpgw:booking_link" xmlns:php="http://php.net/xsl">
	<xsl:param name="link_data"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$link_data/href">
				<a href="{$link_data/href}"><xsl:value-of select="$link_data/label"/></a>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$link_data/label" />
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>