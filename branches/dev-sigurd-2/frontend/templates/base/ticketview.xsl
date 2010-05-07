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
	<xsl:copy-of select="."/>

    <div class="yui-navset" id="ticket_tabview">
        <div class="yui-content">
        	<div id="ticketinfo">
        		<ul style="margin: 2em;">
        			<li style="margin-bottom: 1em;">
        				<img src="frontend/templates/base/images/16x16/comments.png" class="list_image"/><a href="?menuaction=frontend.uihelpdesk.index"> &lt;&lt; Vis alle avviksmeldinger på bygget</a>
        			</li>
        			<li>
        				<ul>
        					<li>
        						<img src="frontend/templates/base/images/16x16/comment.png" class="list_image"/> <strong><xsl:value-of select="ticket/subject"/></strong>
        					</li>
        					<li>
        						<img src="frontend/templates/base/images/16x16/clock_edit.png" class="list_image"/> Registrert <xsl:value-of select="ticket/last_opened"/> av <xsl:value-of select="ticket/user_name"/>
        					</li>
     						<li>
     							<img src="frontend/templates/base/images/16x16/timeline_marker.png" class="list_image"/> <xsl:value-of select="ticket/status_name"/>
     						</li>
     						<li>
     							<img src="frontend/templates/base/images/16x16/page_white_edit.png" class="list_image"/> <xsl:value-of select="ticket/details"/>
     						</li>
     						<li>
     							<img src="frontend/templates/base/images/16x16/comments.png" class="list_image"/>
     							<dl>
		        					<xsl:for-each select="tickethistory/*[starts-with(name(), 'record')]">
		        					<xsl:copy-of select="."/>
						                <dt>Sak oppdatert av <xsl:value-of select="user"/> den <xsl:value-of select="date"/></dt>
						                <dd><xsl:value-of select="note"/></dd>
						            </xsl:for-each>
				            	</dl>
     						</li>
     					</ul>
        			</li>
        		</ul>
        	</div>
        </div>
    </div>
</xsl:template>


