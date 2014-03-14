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
		
    <!-- ==================  CHANGE STATUS FOR CHECKLIST  ===================== -->
 		<xsl:choose>
			<xsl:when test="check_list/id != 0">
		      <xsl:variable name="action_url"><xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicheck_list.update_status,phpgw_return_as:json')" /></xsl:variable>
			      <form id="update-check-list-status" class="done" action="{$action_url}" method="post">
					<input type="hidden" name="check_list_id" value="{check_list/id}" /> 
						<xsl:choose>
							<xsl:when test="check_list/status = 0">
						        <input id='update-check-list-status-value' type="hidden" name="status" value="1" />
						        <input id="status_submit" type="submit" class="btn">
						          <xsl:attribute name="value">
						            <xsl:value-of select="php:function('lang', 'Status not done')" />
						          </xsl:attribute>
						        </input>
							</xsl:when>
							<xsl:otherwise>
						        <input id='update-check-list-status-value' type="hidden" name="status" value="0" />
						        <input type="submit" class="btn">
						          <xsl:attribute name="value">
						            <xsl:value-of select="php:function('lang', 'is_executed')" />
						          </xsl:attribute>
						        </input>
							</xsl:otherwise>
						</xsl:choose>
<!--
						 <div id ='update-check-list-status-icon' class="not_done">
						   	<div class="icon">
						   		<img src="controller/images/red_ring.png" />
							</div>
						</div>
						<div id ='update-check-list-status-icon-done' class="done">
						  	<div class="icon">
								<img src="controller/images/green_ring.png" />
							</div>
						</div>
-->
			      </form>
				</xsl:when>
			</xsl:choose>
      
		<!-- ==================  CHECKLIST TAB MENU  ===================== -->
        <xsl:choose>
        <xsl:when test="count(check_list_type) = 0 or check_list_type != 'add_check_list'">
            <xsl:call-template name="check_list_menu">
                <xsl:with-param name="active_tab">
                	<xsl:value-of select="$active_tab" />
               	</xsl:with-param>
            </xsl:call-template>
        </xsl:when>
        <xsl:otherwise>
            <xsl:call-template name="add_check_list_menu" />
        </xsl:otherwise>
        </xsl:choose>
	</div>
		
</xsl:template>
