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
		
    <!-- ==================  CHANGE STATUS FOR CHECKLIST  ===================== -->
      <xsl:variable name="action_url"><xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicheck_list.update_status,phpgw_return_as:json')" /></xsl:variable>
      <form id="update-check-list-status" class="done" action="{$action_url}" method="post">
				<input type="hidden" name="check_list_id" value="{check_list/id}" /> 
        <input type="hidden" name="status" value="1" />

        <input type="submit" class="btn">
          <xsl:attribute name="value">
            <xsl:value-of select="php:function('lang', 'Status not done')" />
          </xsl:attribute>
        </input>
        <div class="icon">
          <img src="/pe/controller/images/red_ring.png" />
        </div>
      </form>
      
      <form id="update-check-list-status" class="not_done" action="{$action_url}" method="post">
				<input type="hidden" name="check_list_id" value="{check_list/id}" /> 
        <input type="hidden" name="status" value="0" />

        <input type="submit" class="btn">
          <xsl:attribute name="value">
            <xsl:value-of select="php:function('lang', 'Status done')" />
          </xsl:attribute>
        </input>
        <div class="icon">
          <img src="/pe/controller/images/green_ring.png" />
        </div>
      </form>
       

		<!-- ==================  CHECKLIST TAB MENU  ===================== -->
		<xsl:call-template name="check_list_menu" />
	</div>		
</xsl:template>
