<xsl:template name="check_list_menu" xmlns:php="http://php.net/xsl">
	<xsl:param name="active_tab" />
	<xsl:variable name="session_url">
		<xsl:text>&amp;</xsl:text>
		<xsl:value-of select="php:function('get_phpgw_session_url')" />
	</xsl:variable>

	<li class="pure-menu-item">
		<xsl:choose>
			<xsl:when test="$active_tab = 'view_details'">
				<xsl:attribute name="class">pure-menu-item pure-menu-selected</xsl:attribute>
			</xsl:when>
		</xsl:choose>
		<a class="pure-menu-link bigmenubutton">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:value-of select="$session_url"/>
			</xsl:attribute>
			<i class="fa fa-home fa-fw" aria-hidden="true"></i>
			Vis detaljer for sjekkliste
		</a>
	</li>
	<!-- ==================  LOADS CASES FOR CHECKLIST  ===================== -->
	<li class="pure-menu-item">
		<xsl:if test="$active_tab = 'view_cases'">
			<xsl:attribute name="class">pure-menu-item pure-menu-selected</xsl:attribute>
		</xsl:if>
		<a class="pure-menu-link bigmenubutton">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicase.view_open_cases</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:value-of select="$session_url"/>
			</xsl:attribute>
			<i class="fa fa-list-ol" aria-hidden="true"></i>
			<xsl:text> </xsl:text>
			Vis saker
		</a>
	</li>
	<!-- ==================  LOADS INFO ABOUT CONTROL  ===================== -->
	<li class="pure-menu-item">
		<xsl:choose>
			<xsl:when test="$active_tab = 'view_control_info'">
				<xsl:attribute name="class">pure-menu-item pure-menu-selected</xsl:attribute>
			</xsl:when>
		</xsl:choose>
		<a class="pure-menu-link bigmenubutton">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_control_info</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:value-of select="$session_url"/>
			</xsl:attribute>
			<i class="fa fa-info-circle" aria-hidden="true"></i>
			<xsl:text> </xsl:text>
			Vis info om kontroll
		</a>
	</li>
	<!-- ==================  REGISTER NEW CASE  ===================== -->
	<li class="pure-menu-item">
		<a class="pure-menu-link bigmenubutton">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicase.add_case</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:value-of select="$session_url"/>
			</xsl:attribute>
			<i class="fa fa-flag" aria-hidden="true"></i>
			<xsl:text> </xsl:text>
			<xsl:value-of select="php:function('lang', 'add case')"/>
		</a>
	</li>
	<!-- ==================  REGISTER NEW MESSAGE  ===================== -->
	<li class="pure-menu-item">
		<a class="pure-menu-link bigmenubutton">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicase.create_case_message</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:value-of select="$session_url"/>
			</xsl:attribute>
			<i class="fa fa-bolt" aria-hidden="true"></i>
			<xsl:text> </xsl:text>
			<xsl:value-of select="php:function('lang', 'add ticket')"/>
		</a>
	</li>
</xsl:template>
