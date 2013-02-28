<xsl:template name="check_list_menu" xmlns:php="http://php.net/xsl">
    <xsl:param name="active_tab" />
    <xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>

    <div id="check_list_menu">
        <div class="left_btns">
            <a class="first">
                <xsl:choose>
                    <xsl:when test="$active_tab = 'view_details'">
                        <xsl:attribute name="class">first active</xsl:attribute>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:attribute name="class">first</xsl:attribute>
                    </xsl:otherwise>
                </xsl:choose>
                
                <xsl:attribute name="href">
                    <xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
                    <xsl:text>&amp;check_list_id=</xsl:text>
                    <xsl:value-of select="check_list/id"/>
                    <xsl:value-of select="$session_url"/>
                </xsl:attribute>
                Vis detaljer for sjekkliste
            </a>
            <!-- ==================  LOADS CASES FOR CHECKLIST  ===================== -->
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
                Vis saker
            </a>
            <!-- ==================  LOADS INFO ABOUT CONTROL  ===================== -->
            <a>
                <xsl:choose>
                    <xsl:when test="$active_tab = 'view_control_info'">
                        <xsl:attribute name="class">last active</xsl:attribute>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:attribute name="class">last</xsl:attribute>
                    </xsl:otherwise>
                </xsl:choose>
                <xsl:attribute name="href">
                    <xsl:text>index.php?menuaction=controller.uicheck_list.view_control_info</xsl:text>
                    <xsl:text>&amp;check_list_id=</xsl:text>
                    <xsl:value-of select="check_list/id"/>
                    <xsl:value-of select="$session_url"/>
                </xsl:attribute>
                Vis info om kontroll
            </a>
        </div>
		
        <div class="right_btns">
            <!-- ==================  REGISTER NEW CASE  ===================== -->
            <a class="btn focus first">
                <xsl:attribute name="href">
                    <xsl:text>index.php?menuaction=controller.uicase.add_case</xsl:text>
                    <xsl:text>&amp;check_list_id=</xsl:text>
                    <xsl:value-of select="check_list/id"/>
                    <xsl:value-of select="$session_url"/>
                </xsl:attribute>
                Registrer sak
            </a>
            <!-- ==================  REGISTER NEW MESSAGE  ===================== -->
            <a class="btn focus">
                <xsl:attribute name="href">
                    <xsl:text>index.php?menuaction=controller.uicase.create_case_message</xsl:text>
                    <xsl:text>&amp;check_list_id=</xsl:text>
                    <xsl:value-of select="check_list/id"/>
                    <xsl:value-of select="$session_url"/>
                </xsl:attribute>
                Registrer melding
            </a>
        </div>
    </div>
</xsl:template>
