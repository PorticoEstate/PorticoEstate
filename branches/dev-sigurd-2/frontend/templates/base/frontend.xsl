<xsl:template match="header" xmlns:php="http://php.net/xsl">
	<div id="unit_selector">
		<div id="unit_image">
			<img src="http://www.greenofficeprojects.org/blog/images/building.jpg" alt="{loc1_name}" />
		</div>
		
		<form action="index.php?menuaction=frontend.uihelpdesk.index" method="post">
			<label>
				<xsl:value-of select="php:function('lang', 'select_unit')"/>
			</label>
			<br/>
			<select name="location" size="7" onchange="this.form.submit();">

				<xsl:for-each select="locations">
					<xsl:choose>
						<xsl:when test="location_code = //header/selected">
							<option value="{location_code}" selected="selected"><xsl:value-of select="loc1_name"/></option>
						</xsl:when>
						<xsl:otherwise>
							<option value="{location_code}"><xsl:value-of select="loc1_name"/></option>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>

			</select>
		</form>
	</div>
</xsl:template>

<xsl:template match="tabs">
	<xsl:value-of disable-output-escaping="yes" select="." />
</xsl:template>

