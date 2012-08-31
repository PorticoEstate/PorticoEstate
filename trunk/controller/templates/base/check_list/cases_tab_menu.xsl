<xsl:template name="cases_tab_menu" xmlns:php="http://php.net/xsl">
  <xsl:param name="active_tab" />
  
  <div class="tab_menu">
    <a id="view_open_cases">
      <xsl:if test="$active_tab = 'view_open_cases'">
        <xsl:attribute name="class">active</xsl:attribute>
      </xsl:if>				
      <xsl:attribute name="href">
        <xsl:text>index.php?menuaction=controller.uicheck_list.view_open_cases</xsl:text>
        <xsl:text>&amp;check_list_id=</xsl:text>
        <xsl:value-of select="check_list/id"/>
        <xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
      </xsl:attribute>
      Åpne saker
    </a>
    <a id="view_closed_cases">
      <xsl:if test="$active_tab = 'view_closed_cases'">
        <xsl:attribute name="class">active</xsl:attribute>
      </xsl:if>					
      <xsl:attribute name="href">
        <xsl:text>index.php?menuaction=controller.uicheck_list.view_closed_cases</xsl:text>
        <xsl:text>&amp;check_list_id=</xsl:text>
        <xsl:value-of select="check_list/id"/>
        <xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
      </xsl:attribute>
      Lukkede saker
    </a>
  </div>
</xsl:template>
