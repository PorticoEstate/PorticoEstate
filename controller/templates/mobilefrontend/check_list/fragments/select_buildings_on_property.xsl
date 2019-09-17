<!-- $Id: view_calendar_month.xsl 9200 2012-04-21 20:05:34Z vator $ -->
<xsl:template name="select_buildings_on_property">
  
	<div class="pure-control-group row mt-3">
		<span style="display: block;font-size: 16px;margin-bottom: 8px;" class="ml-3 mr-3">
			Spesifiser lokalisering du vil registrere saken på.
			<xsl:if test="mandatory_location = 1">
				Lokalisering <u>må</u> velges før du registrerer ny sak.
			</xsl:if>
		</span>

		<select id="choose-building-on-property" class="custom-select">
			<xsl:choose>
				<xsl:when test="cases_view = 'open_cases' or cases_view = 'closed_cases'">
					<xsl:attribute name="class">view-cases custom-select ml-3</xsl:attribute>
				</xsl:when>
				<xsl:otherwise>
					<xsl:attribute name="class">add-case required custom-select ml-3</xsl:attribute>
				</xsl:otherwise>
			</xsl:choose>
    
			<option value="">Velg lokalisering</option>
			<xsl:for-each select="buildings_on_property">
				<option>
					<xsl:if test="selected = 1">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>
					<xsl:attribute name="value">
						<xsl:value-of select="id"/>
					</xsl:attribute>
					<xsl:value-of select="name" />
				</option>
			</xsl:for-each>
		</select>
	</div>
</xsl:template>
