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
        <div class="yui-content">
        	<div id="ticketinfo">
        		<ul>
        			<li>
        				<img src="frontend/templates/base/images/16x16/comments.png" class="list_image"/><a href="?menuaction=frontend.uihelpdesk.index">Vis alle avviksmeldinger på bygget</a>
        			</li>
        			<li>
        				<dl>
        					<dt>
        						<img src="frontend/templates/base/images/16x16/comment.png" class="list_image"/>
        					</dt>
        					<dd>
        						<xsl:value-of select="ticket/subject"/>
        					</dd>
        					<dt>
        						Last opened
        					</dt>
        					<dd>
        						<xsl:value-of select="ticket/last_opened"/>
        					</dd>
        					<dt>
        						Meldt inn av:
        					</dt>
        					<dd>
        						<xsl:value-of select="ticket/user_name"/>
        					</dd>
        					<dt>
        						Status:
        					</dt>
        					<dd>
        						<xsl:value-of select="ticket/status_name"/>
        					</dd>
        					<dt>
        						Beskrivelse
        					</dt>
        					<dd>
        						<xsl:value-of select="ticket/details"/>
        					</dd>
        					<dt>
        						Kommentarer
        					</dt>
        					<dd>
        						<dl>
		        					<xsl:for-each select="tickethistory/*[starts-with(name(), 'record')]">
						                <dt>Sak oppdatert av <xsl:value-of select="user"/> den <xsl:value-of select="date"/></dt>
						                <dd><xsl:value-of select="note"/></dd>
						            </xsl:for-each>
				            	</dl>
        					
        					</dd>
        				</dl>
        			</li>
        		</ul>
        	</div>
        </div>
    </div>
</xsl:template>


