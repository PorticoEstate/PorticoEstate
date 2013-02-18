<xsl:template name="check_list_top_section" xmlns:php="http://php.net/xsl">
<xsl:param name="active_tab" />
<xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>

<!-- ==================  CHECKLIST DETAILS INFO  ========================= -->
	<div id="check-list-heading">
		<div class="box-1">
			<h1>Kontroll: <xsl:value-of select="control/title"/></h1>
			<xsl:choose>
				<xsl:when test="type = 'component'">
					<h2><xsl:value-of select="component_array/xml_short_desc"/></h2>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="location_level = 1">
							<h2>Eiendom: <xsl:value-of select="location_array/loc1_name"/></h2>
						</xsl:when>
						<xsl:otherwise>
							<h2>Bygg: <xsl:value-of select="location_array/loc2_name"/></h2>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</div>
		
		<div class="box-2 select-box">
            <xsl:call-template name="nav_control_plan" />
		</div>
		
		<!-- ==================  CHECKLIST TAB MENU  ===================== -->
        <xsl:choose>
        <xsl:when test="count(check_list_type) = 0 or check_list_type != 'add_check_list'">
            <xsl:call-template name="check_list_menu">
                <xsl:with-param name="active_tab">view_details</xsl:with-param>
            </xsl:call-template>
        </xsl:when>
        <xsl:otherwise>
            <xsl:call-template name="add_check_list_menu" />
        </xsl:otherwise>
        </xsl:choose>
	</div>
		
</xsl:template>
