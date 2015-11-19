<xsl:template match="ticketinfo" xmlns:php="http://php.net/xsl">
	
	<xsl:choose>
	    <xsl:when test="msgbox_data != ''">
			<xsl:call-template name="msgbox"/>
	    </xsl:when>
    </xsl:choose>

	<xsl:variable name="tab_selected"><xsl:value-of select="tab_selected"/></xsl:variable>
	
	<div class="frontend_body">
		<div class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs" />
				<div id="{$tab_selected}">
					<ul style="margin: 2em;">
						<li style="margin-bottom: 1em;">
							<a href="{helpdesklist}"> &lt;&lt; <xsl:value-of select="php:function('lang', 'show_all_tickets')"/></a>
						</li>
						<li>
							<ul>
								<li style="margin-bottom: 5px;">
									<img src="frontend/templates/base/images/16x16/comment.png" class="list_image"/> <strong><xsl:value-of select="ticket/subject"/></strong>
								</li>
								<li class="ticket_detail">
									<img src="frontend/templates/base/images/16x16/clock_edit.png" class="list_image"/> <xsl:value-of select="php:function('lang', 'entry_date')"/> <xsl:value-of select="ticket/entry_date"/><xsl:value-of select="php:function('lang', 'of')"/><xsl:value-of select="ticket/user_name"/>
								</li>
								<li class="ticket_detail">
									<img src="frontend/templates/base/images/16x16/timeline_marker.png" class="list_image"/> <xsl:value-of select="php:function('lang', 'status')"/>: <xsl:value-of select="ticket/status_name"/>
								</li>
								<xsl:choose>
									<xsl:when test="ticket/value_vendor_name">
										<li class="ticket_detail">
											<img src="frontend/templates/base/images/16x16/user_suit.png" class="list_image"/> <xsl:value-of select="php:function('lang', 'vendor')"/>: <xsl:value-of select="ticket/value_vendor_name"/>
										</li>
									</xsl:when>
								</xsl:choose>
								<xsl:choose>
									<xsl:when test="ticket/assigned_to_name">
										<li class="ticket_detail">
											<img src="frontend/templates/base/images/16x16/user_red.png" class="list_image"/> <xsl:value-of select="php:function('lang', 'assigned_to')"/>: <xsl:value-of select="ticket/assigned_to_name"/>
										</li>
									</xsl:when>
								</xsl:choose>
								<xsl:choose>
									<xsl:when test="ticket/value_contact_name">
										<li class="ticket_detail">
											<img src="frontend/templates/base/images/16x16/user_green.png" class="list_image"/> <xsl:value-of select="php:function('lang', 'contact')"/>: <xsl:value-of select="ticket/value_contact_name"/>
											Telefon: <xsl:value-of select="ticket/value_contact_tel"/> 
											E-post: <xsl:value-of select="ticket/value_contact_email"/>
										</li>
									</xsl:when>
								</xsl:choose>
								<xsl:choose>
									<xsl:when test="ticket/publish_note = 1">
										<li class="ticket_detail">
											<img src="frontend/templates/base/images/16x16/page_white_edit.png" class="list_image"/><xsl:value-of select="php:function('lang', 'message')"/>: <xsl:value-of select="ticket/details"/>
										</li>
									</xsl:when>
								</xsl:choose>
								<li class="ticket_detail">
									<img src="frontend/templates/base/images/16x16/comments.png" class="list_image"/> <xsl:value-of select="php:function('lang', 'comments')"/>:<br/>
									<hr/>
									<ul>
										<xsl:for-each select="tickethistory/*[starts-with(name(), 'record')]">
											<li  class="ticket_detail">
												<img src="frontend/templates/base/images/16x16/page_white_edit.png" class="list_image"/> <xsl:value-of select="date"/> - 
												<img src="frontend/templates/base/images/16x16/user_gray.png" class="list_image"/> <xsl:value-of select="user"/><br/>
												<p style="padding: 10px;"><xsl:value-of select="note"/></p>
											</li>
										</xsl:for-each>
									</ul>
								</li>
							</ul>
						</li>
					</ul>
				</div>
				<xsl:value-of disable-output-escaping="yes" select="tabs_content" />
			</div>
		</div>
	</div>
</xsl:template>


