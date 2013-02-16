<xsl:template name="check_list_menu" xmlns:php="http://php.net/xsl">
<xsl:param name="active_tab" />
<xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>

<div id="check_list_menu">
  <a class="first">
    <xsl:if test="$active_tab = 'reg_cases'">
      <xsl:attribute name="class">active</xsl:attribute>
    </xsl:if>
    <xsl:attribute name="href">
      <xsl:text>index.php?menuaction=controller.uicheck_list.add_case</xsl:text>
      <xsl:text>&amp;check_list_id=</xsl:text>
      <xsl:value-of select="check_list/id"/>
      <xsl:value-of select="$session_url"/>
    </xsl:attribute>
    Registrer sak
  </a>
  <a>
    <xsl:if test="$active_tab = 'view_cases'">
      <xsl:attribute name="class">active</xsl:attribute>
    </xsl:if>
    <xsl:attribute name="href">
      <xsl:text>index.php?menuaction=controller.uicase.view_open_cases</xsl:text>
      <xsl:text>&amp;check_list_id=</xsl:text>
      <xsl:value-of select="check_list/id"/>
      <xsl:value-of select="$session_url"/>
    </xsl:attribute>
    Vis åpne saker
  </a>
  <a class="last">
    <xsl:if test="$active_tab = 'view_cases'">
      <xsl:attribute name="class">active</xsl:attribute>
    </xsl:if>
    <xsl:attribute name="href">
      <xsl:text>index.php?menuaction=controller.uicase.view_closed_cases</xsl:text>
      <xsl:text>&amp;check_list_id=</xsl:text>
      <xsl:value-of select="check_list/id"/>
      <xsl:value-of select="$session_url"/>
    </xsl:attribute>
    Vis lukkede saker
  </a>
</div>
</xsl:template>
