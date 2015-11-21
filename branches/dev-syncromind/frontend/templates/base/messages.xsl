<xsl:template match="section" xmlns:php="http://php.net/xsl">
	
	<xsl:variable name="tab_selected"><xsl:value-of select="tab_selected"/></xsl:variable>
	
	<div class="frontend_body">
		<div class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs" />
				<div id="{$tab_selected}">
					<div>
						<table>
							<tr>
								<th><xsl:value-of select="php:function('lang', 'date')"/></th>
								<th style="padding-left: 2em;"><xsl:value-of select="php:function('lang', 'title')"/></th>
								<th style="padding-left: 2em;"><xsl:value-of select="php:function('lang', 'from')"/></th>
							</tr>
							<xsl:for-each select="message">
							<tr>
								<td><img src="frontend/templates/base/images/16x16/email.png" class="list_image"/> <xsl:value-of disable-output-escaping="yes" select="date"/></td>
								<td style="padding-left: 2em;"><a href="index.php?menuaction=frontend.uimessages.index&amp;message_id={id}"><xsl:value-of select="subject" disable-output-escaping="yes"/></a></td>
								<td style="padding-left: 2em;"><xsl:value-of disable-output-escaping="yes" select="from"/></td>
							</tr>
							</xsl:for-each>
						</table>
					</div>
					<div>
						<ul>
							<xsl:choose>
								<xsl:when test="normalize-space(view)">
									<li class="ticket_detail">
										<img src="frontend/templates/base/images/16x16/email_open.png" class="list_image"/><xsl:value-of select="view/subject" disable-output-escaping="yes"/>
									</li>
									<li class="ticket_detail">
										<img src="frontend/templates/base/images/16x16/clock_edit.png" class="list_image"/><xsl:value-of select="view/date" disable-output-escaping="yes"/>
									</li>
									<li class="ticket_detail">
										<img src="frontend/templates/base/images/16x16/user_gray.png" class="list_image"/><xsl:value-of select="view/from" disable-output-escaping="yes"/>
									</li>
									<li class="ticket_detail">
										<img src="frontend/templates/base/images/16x16/page_white_edit.png" class="list_image"/><xsl:value-of select="view/content" disable-output-escaping="yes"/>
									</li>
								</xsl:when>
							</xsl:choose>
						</ul>
					</div>
				</div>
				<xsl:value-of disable-output-escaping="yes" select="tabs_content" />
			</div>	
		</div>
	</div>
</xsl:template>


