<xsl:template name="add_check_list_menu" xmlns:php="http://php.net/xsl">
    <xsl:variable name="session_url"><xsl:text>&amp;</xsl:text>
        <xsl:value-of select="php:function('get_phpgw_session_url')" />
    </xsl:variable>

    <div id="check_list_menu">
        <div class="left_btns">
            <span class="first active">
                Vis detaljer for sjekkliste
            </span>
            <!-- ==================  LOADS CASES FOR CHECKLIST  ===================== -->
            <span>
                Vis saker
            </span>
            <!-- ==================  LOADS INFO ABOUT CONTROL  ===================== -->
            <span class="last">
                Vis info om kontroll
            </span>
        </div>
		
        <div class="right_btns">
            <!-- ==================  REGISTER NEW CASE  ===================== -->
            <span class="btn focus first">
				<xsl:value-of select="php:function('lang', 'add case')"/>
            </span>
            <!-- ==================  REGISTER NEW MESSAGE  ===================== -->
            <span class="btn focus">
				<xsl:value-of select="php:function('lang', 'add ticket')"/>
            </span>
        </div>
    </div>
</xsl:template>
