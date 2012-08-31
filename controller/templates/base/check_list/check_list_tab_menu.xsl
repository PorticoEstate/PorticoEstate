<xsl:template name="check_list_tab_menu" xmlns:php="http://php.net/xsl">
  <xsl:param name="active_tab" />

  <div id="check_list_menu">
    <!-- ==================  LOADS VIEW CHECKLIST DETAILS   ===================== -->
    <div class="left_btns">
      <a>
        <xsl:if test="$active_tab = 'view_details'">
          <xsl:attribute name="class">active</xsl:attribute>
        </xsl:if>
        <xsl:attribute name="href">
          <xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
          <xsl:text>&amp;check_list_id=</xsl:text>
          <xsl:value-of select="check_list/id"/>
        </xsl:attribute>
        Vis detaljer for sjekkliste
      </a>
      <!-- ==================  LOADS CASES FOR CHECKLIST  ===================== -->
      <a>
        <xsl:if test="$active_tab = 'view_cases'">
          <xsl:attribute name="class">active</xsl:attribute>
        </xsl:if>
        <xsl:attribute name="href">
          <xsl:text>index.php?menuaction=controller.uicheck_list.view_cases_for_check_list</xsl:text>
          <xsl:text>&amp;check_list_id=</xsl:text>
          <xsl:value-of select="check_list/id"/>
        </xsl:attribute>
        Vis saker
      </a>
      <!-- ==================  LOADS INFO ABOUT CONTROL  ===================== -->
      <a>
        <xsl:if test="$active_tab = 'view_control_info'">
          <xsl:attribute name="class">active</xsl:attribute>
        </xsl:if>
        <xsl:attribute name="href">
          <xsl:text>index.php?menuaction=controller.uicheck_list.view_control_info</xsl:text>
          <xsl:text>&amp;check_list_id=</xsl:text>
          <xsl:value-of select="check_list/id"/>
        </xsl:attribute>
        Vis info om kontroll
      </a>
    </div>
		
    <div class="right_btns">
      <!-- ==================  REGISTER NEW CASE  ===================== -->
      <a class="btn focus first">
        <xsl:attribute name="href">
          <xsl:text>index.php?menuaction=controller.uicheck_list.add_case</xsl:text>
          <xsl:text>&amp;check_list_id=</xsl:text>
          <xsl:value-of select="check_list/id"/>
        </xsl:attribute>
        Registrer sak
      </a>
      <!-- ==================  REGISTER NEW MESSAGE  ===================== -->
      <a class="btn focus">
        <xsl:attribute name="href">
          <xsl:text>index.php?menuaction=controller.uicase.create_case_message</xsl:text>
          <xsl:text>&amp;check_list_id=</xsl:text>
          <xsl:value-of select="check_list/id"/>
        </xsl:attribute>
        Registrer melding
      </a>
    </div>
  </div>
		
</xsl:template>
