  <xsl:template name="nav_control_plan" xmlns:php="http://php.net/xsl">
	<xsl:variable name="session_url">&amp;
		<xsl:value-of select="php:function('get_phpgw_session_url')" />
	</xsl:variable>

	<xsl:choose>
		<xsl:when test="type = 'component'">
			<a>
				<xsl:attribute name="href">
					<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicomponent.index' )" />
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="current_year"/>
					<xsl:text>&amp;location_id=</xsl:text>
					<xsl:value-of select="component_array/location_id"/>
					<xsl:text>&amp;component_id=</xsl:text>
					<xsl:value-of select="component_array/id"/>
				</xsl:attribute>
				Kontrollplan for komponent (år)
			</a>
		</xsl:when>
		<xsl:otherwise>
			<a>
				<xsl:attribute name="href">
					<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicalendar.view_calendar_for_year' )" />
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="current_year"/>
					<xsl:text>&amp;location_code=</xsl:text>
					<xsl:value-of select="location_array/location_code"/>
				</xsl:attribute>
				Kontrollplan for bygg/eiendom (år)
			</a>

			<a class="last">
				<xsl:attribute name="href">
					<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicalendar.view_calendar_for_month' )" />
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="current_year"/>
					<xsl:text>&amp;month=</xsl:text>
					<xsl:value-of select="current_month_nr"/>
					<xsl:text>&amp;location_code=</xsl:text>
					<xsl:value-of select="location_array/location_code"/>
				</xsl:attribute>
				Kontrolplan for bygg/eiendom (måned)
			</a>

		</xsl:otherwise>
	</xsl:choose>

</xsl:template>