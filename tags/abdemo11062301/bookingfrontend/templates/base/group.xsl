<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_booking_i18n"/>
	
    <div id="content">
        <ul class="pathway">
			<li><a href="index.php?menuaction=bookingfrontend.uisearch.index"><xsl:value-of select="php:function('lang', 'Home')" /></a></li>
            <li><a href="{group/organization_link}"><xsl:value-of select="group/organization_name"/></a></li>
            <li><xsl:value-of select="group/name"/></li>
        </ul>

	        <xsl:if test="group/permission/write">
	            <span class="loggedin">
		        	<button onclick="window.location.href='{edit_self_link}'"><xsl:value-of select="php:function('lang', 'edit')" /></button>
				</span>
	        </xsl:if>
        <xsl:call-template name="msgbox"/>

        <dl class="proplist">
            <dt><xsl:value-of select="php:function('lang', 'Group')" /></dt>
            <dd><xsl:value-of select="group/name"/></dd>

            <dt><xsl:value-of select="php:function('lang', 'Organization')" /></dt>
            <dd><xsl:value-of select="group/organization_name"/></dd>
			
			<xsl:if test="group/description and normalize-space(group/description)">
				<dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
		        <dd><xsl:value-of select="group/description" disable-output-escaping="yes"/></dd>
			</xsl:if>
			
			<xsl:for-each select="group/contacts">
				<xsl:if test="normalize-space(.)">
					<h3><xsl:value-of select="php:function('lang', 'Contact Person')" /></h3>
					
					<xsl:if test="name and string-length(normalize-space(name)) &gt; 0">
						<dt><xsl:value-of select="php:function('lang', 'Name')" /></dt>
						<dd><xsl:value-of select="name"/></dd>
					</xsl:if>
					
					<xsl:if test="phone and string-length(normalize-space(phone)) &gt; 0">
						<dt><xsl:value-of select="php:function('lang', 'Phone')" /></dt>
						<dd><xsl:value-of select="phone"/></dd>
					</xsl:if>
					
					<xsl:if test="email and string-length(normalize-space(email)) &gt; 0">
						<dt><xsl:value-of select="php:function('lang', 'Email')" /></dt>
						<dd><xsl:value-of select="email"/></dd>
					</xsl:if>
				</xsl:if>
			</xsl:for-each>

        </dl>
    </div>
</xsl:template>
