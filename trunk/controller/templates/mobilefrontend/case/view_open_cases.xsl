<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">

<div id="main_content" class="medium">
   <xsl:call-template name="check_list_top_section">
      <xsl:with-param name="active_tab">view_cases</xsl:with-param>
    </xsl:call-template>

	<xsl:choose>
		<xsl:when test="buildings_on_property/child::node()">
  			<div id="choose-building-wrp">
				<xsl:call-template name="select_buildings_on_property" />
			</div>
		</xsl:when>  
  </xsl:choose>
  <div id="view_cases">
    <xsl:call-template name="cases_tab_menu">
      <xsl:with-param name="active_tab">view_open_cases</xsl:with-param>
    </xsl:call-template>
	
    <div class="tab_item active">
      <xsl:choose>
        <xsl:when test="open_check_items_and_cases/child::node()">
          <ul class="check_items">
            <xsl:for-each select="open_check_items_and_cases">
                <xsl:choose>
                	<!--  ==================== CONTROL TYPE 1 ===================== -->
                  <xsl:when test="control_item/type = 'control_item_type_1'">
                  	<xsl:call-template name="case_row">
      								<xsl:with-param name="control_item_type">control_item_type_1</xsl:with-param>
    								</xsl:call-template>
                  </xsl:when>
                  <!--  ==================== CONTROL TYPE 2 ===================== -->
                  <xsl:when test="control_item/type = 'control_item_type_2'">
                  	<xsl:call-template name="case_row">
      								<xsl:with-param name="control_item_type">control_item_type_2</xsl:with-param>
    								</xsl:call-template>
                  </xsl:when>
                  <!--  ==================== CONTROL TYPE 3 ELLER CONTROL TYPE 4 ===================== -->
                  <xsl:when test="control_item/type = 'control_item_type_3' or control_item/type = 'control_item_type_4'">
                    <xsl:call-template name="case_row">
      								<xsl:with-param name="control_item_type">control_item_type_3</xsl:with-param>
    								</xsl:call-template>
                  </xsl:when>
                </xsl:choose>
            </xsl:for-each>
          </ul>
        </xsl:when>
        <xsl:otherwise>
          <p>Ingen åpne saker eller målinger</p>
        </xsl:otherwise>
      </xsl:choose>
    </div>
  </div>
</div>
</xsl:template>
