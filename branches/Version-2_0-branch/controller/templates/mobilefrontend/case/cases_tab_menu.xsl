<xsl:template name="cases_tab_menu" xmlns:php="http://php.net/xsl">
	<xsl:param name="active_tab" />
	<xsl:variable name="session_url">
		<xsl:text>&amp;</xsl:text>
		<xsl:value-of select="php:function('get_phpgw_session_url')" />
	</xsl:variable>

	<div class="pure-menu pure-menu-horizontal pure-menu-scrollable">
		<ul class="pure-menu-list">
			<li class="pure-menu-item">
				<xsl:if test="$active_tab = 'view_open_cases'">
					<xsl:attribute name="class">pure-menu-item pure-menu-selected</xsl:attribute>
				</xsl:if>
				<a id="view_open_cases" class="pure-menu-link bigmenubutton">
					<xsl:attribute name="href">
						<xsl:text>index.php?menuaction=controller.uicase.view_open_cases</xsl:text>
						<xsl:text>&amp;check_list_id=</xsl:text>
						<xsl:value-of select="check_list/id"/>
						<xsl:value-of select="$session_url"/>
					</xsl:attribute>
					<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
					<xsl:text> </xsl:text>
					Åpne saker
				</a>
			</li>
			<li class="pure-menu-item">
				<xsl:if test="$active_tab = 'view_closed_cases'">
					<xsl:attribute name="class">pure-menu-item pure-menu-selected</xsl:attribute>
				</xsl:if>
				<a id="view_closed_cases" class="pure-menu-link bigmenubutton">
					<xsl:attribute name="href">
						<xsl:text>index.php?menuaction=controller.uicase.view_closed_cases</xsl:text>
						<xsl:text>&amp;check_list_id=</xsl:text>
						<xsl:value-of select="check_list/id"/>
						<xsl:value-of select="$session_url"/>
					</xsl:attribute>
					<i class="fa fa-check-square-o" aria-hidden="true"></i>
					<xsl:text> </xsl:text>
					Lukkede saker
				</a>
			</li>
		</ul>
	</div>
</xsl:template>
