<func:function name="phpgw:or">
	<xsl:param name="input"/>
	<xsl:param name="default"/>
	<xsl:choose>
		<xsl:when test="$input">
			<func:result select="$input"/>
		</xsl:when>
		<xsl:otherwise>
			<func:result select="$default"/>
		</xsl:otherwise>
	</xsl:choose>
</func:function>
