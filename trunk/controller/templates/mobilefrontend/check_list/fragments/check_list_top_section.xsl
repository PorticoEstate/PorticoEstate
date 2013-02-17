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
    
      <form id="update-check-list-status" action="{$action_url}" method="post">
        <xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
        <input type="hidden" name="control_item_id" value="{$control_item_id}" /> 
        <input type="hidden" name="check_list_id"><xsl:attribute name="value"><xsl:value-of select="//check_list/id"/></xsl:attribute></input>
        <input type="hidden" name="status" value="0" />
        <input type="hidden" name="type" value="control_item_type_1" />
        <input type="hidden" name="location_code"  value="" class="required" />

         <span class="btn"><xsl:value-of select="php:function('lang', 'Status not done')" /></span>
      <div class="icon">
        <img src="/pe/controller/images/red_ring.png" />
      </div>    
        
        <input type="submit" class="btn" name="save_control" value="Lagre sak" />
      </form>
       

		<!-- ==================  CHECKLIST TAB MENU  ===================== -->
		<xsl:call-template name="check_list_menu" />
	</div>		
</xsl:template>
