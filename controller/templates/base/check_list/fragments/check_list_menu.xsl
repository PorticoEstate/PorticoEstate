<xsl:template name="check_list_menu" xmlns:php="http://php.net/xsl">
	<xsl:param name="active_tab" />
	<xsl:variable name="session_url">
		<xsl:text>&amp;</xsl:text>
		<xsl:value-of select="php:function('get_phpgw_session_url')" />
	</xsl:variable>


	<li class="nav-item">
		<xsl:choose>
			<xsl:when test="$active_tab = 'view_details'">
				<xsl:attribute name="class">nav-item active</xsl:attribute>
			</xsl:when>
		</xsl:choose>
		<a class="nav-link">
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
	<li class="nav-item">
		<xsl:if test="$active_tab = 'view_cases'">
			<xsl:attribute name="class">nav-item active</xsl:attribute>
		</xsl:if>
		<a class="nav-link">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicase.view_open_cases</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:value-of select="$session_url"/>
			</xsl:attribute>
			<i class="fa fa-list-ol" aria-hidden="true"></i>
			<xsl:text> </xsl:text>
			Vis saker
			<xsl:if test="number_of_cases > 0">
				(<xsl:value-of select="number_of_cases"/>)
			</xsl:if>

		</a>
	</li>
	<!-- ==================  LOADS INFO ABOUT CONTROL  ===================== -->
	<li class="nav-item">
		<xsl:choose>
			<xsl:when test="$active_tab = 'view_control_info'">
				<xsl:attribute name="class">nav-item active</xsl:attribute>
			</xsl:when>
		</xsl:choose>
		<a class="nav-link">
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
	<li class="nav-item">
		<xsl:if test="$active_tab = 'add_case'">
			<xsl:attribute name="class">nav-item active</xsl:attribute>
		</xsl:if>
		<a class="nav-link">
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
	<li class="nav-item">
		<xsl:if test="$active_tab = 'create_case_message'">
			<xsl:attribute name="class">nav-item active</xsl:attribute>
		</xsl:if>
		<a class="nav-link">
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

	<li class="nav-item">
		<xsl:if test="$active_tab = 'create_case_message'">
			<xsl:attribute name="class">nav-item active</xsl:attribute>
		</xsl:if>
		<a class="nav-link" target="_blank">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.get_report</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:value-of select="$session_url"/>
			</xsl:attribute>
			<i class="far fa-file-pdf" aria-hidden="true"></i>
			<xsl:text> </xsl:text>
			<xsl:value-of select="php:function('lang', 'report')"/>
		</a>
	</li>

</xsl:template>
