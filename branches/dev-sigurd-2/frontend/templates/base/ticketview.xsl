<xsl:template match="ticketinfo" xmlns:php="http://php.net/xsl">
    <table cellpadding="2" cellspacing="2" width="95%" align="center">
        <xsl:choose>
            <xsl:when test="msgbox_data != ''">
                <tr>
                    <td align="left" colspan="3">
                        <xsl:call-template name="msgbox"/>
                    </td>
                </tr>
            </xsl:when>
        </xsl:choose>
    </table>

   
    <div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
        <div class="yui-content">

            <h1><xsl:value-of select="ticket/subject"/></h1>
            <div id="ticketinfo">
                <ul>
                    <li><xsl:value-of select="ticket/entry_date"/></li>
                    <li>meldt inn av <xsl:value-of select="ticket/user_name"/></li>
                    <li>sted: TODO</li>
                </ul>
                <p>Beskrivelse<br/>
                <xsl:value-of select="ticket/details"/></p>

                <p>Status: <xsl:value-of select="ticket/status_name"/></p>
                
            </div>
        </div>
    </div>


</xsl:template>