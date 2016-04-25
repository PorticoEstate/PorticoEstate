<func:function name="phpgw:booking_link" xmlns:php="http://php.net/xsl">
	<xsl:param name="link_data"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$link_data/href">
				<a href="{$link_data/href}">
					<xsl:value-of select="$link_data/label"/>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$link_data/label" />
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>

<func:function name="phpgw:option_checkbox" xmlns:php="http://php.net/xsl">
	<xsl:param name="field"/>
	<xsl:param name="field_name"/>
	<xsl:param name="label_false" select="string('No')"/>
	<xsl:param name="label_true" select="string('Yes')"/>
	
	<func:result>
		<select id="field_{$field_name}" name="{$field_name}">
			<option value="1">
				<xsl:if test="$field=1">
					<xsl:attribute name="selected">checked</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="php:function('lang', $label_true)"/>
			</option>
			<option value="0">
				<xsl:if test="not($field=1)">
					<xsl:attribute name="selected">checked</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="php:function('lang', $label_false)"/>
			</option>
		</select>
	</func:result>
</func:function>