  <xsl:template name="nav_control_plan" xmlns:php="http://php.net/xsl">
	<xsl:param name="inactive" />
	<xsl:variable name="session_url">
		<xsl:text>&amp;</xsl:text>
		<xsl:value-of select="php:function('get_phpgw_session_url')" />
	</xsl:variable>

	<xsl:choose>
		<xsl:when test="type = 'component'">
			<li class="nav-item">
				<a class="nav-link">
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicomponent.index' )" />
						<xsl:text>&amp;year=</xsl:text>
						<xsl:value-of select="current_year"/>
						<xsl:text>&amp;month=</xsl:text>
						<xsl:value-of select="current_month_nr"/>
						<xsl:text>&amp;location_id=</xsl:text>
						<xsl:value-of select="component_array/location_id"/>
						<xsl:text>&amp;component_id=</xsl:text>
						<xsl:value-of select="component_array/id"/>
						<xsl:text>&amp;get_locations=</xsl:text>
						<xsl:value-of select="get_locations"/>
					</xsl:attribute>
					<i class="fa fa-calendar" aria-hidden="true"></i>
					<xsl:text> </xsl:text>
					Kontrollplan for komponent (år)
				</a>
			</li>
		</xsl:when>
		<xsl:otherwise>
			<li class="nav-item">
				<a class="nav-link">
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicalendar.view_calendar_for_year' )" />
						<xsl:text>&amp;year=</xsl:text>
						<xsl:value-of select="current_year"/>
						<xsl:text>&amp;location_code=</xsl:text>
						<xsl:value-of select="location_array/location_code"/>
					</xsl:attribute>
					<i class="fa fa-calendar" aria-hidden="true"></i>
					Kontrollplan for bygg/eiendom (år)
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link">
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicalendar.view_calendar_for_month' )" />
						<xsl:text>&amp;year=</xsl:text>
						<xsl:value-of select="current_year"/>
						<xsl:text>&amp;month=</xsl:text>
						<xsl:value-of select="current_month_nr"/>
						<xsl:text>&amp;location_code=</xsl:text>
						<xsl:value-of select="location_array/location_code"/>
					</xsl:attribute>
					<i class="fa fa-calendar" aria-hidden="true"></i>
					<xsl:text> </xsl:text>
					Kontrolplan for bygg/eiendom (måned)
				</a>
			</li>
		</xsl:otherwise>
	</xsl:choose>

</xsl:template>