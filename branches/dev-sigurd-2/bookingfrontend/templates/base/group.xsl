<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
		<h3>
	        <ul class="pathway">
	            <li>
	                <a>
	                    <xsl:attribute name="href"><xsl:value-of select="group/organization_link"/></xsl:attribute>
	                    <xsl:value-of select="group/organization_name"/>
	                </a>
	            </li>
	            <li><xsl:value-of select="php:function('lang', 'Group')" /></li>
	            <li>
	                <a href="">
	                    <xsl:value-of select="group/name"/>
	                </a>
	            </li>
		        <xsl:if test="loggedin &gt; 0">
		            <span class="loggedin"><a>
		                <xsl:attribute name="href">
		                    <xsl:value-of select="edit_self_link" />
		                </xsl:attribute>
		                <img src="/phpgwapi/templates/base/images/edit.png" />
		            </a></span>
		        </xsl:if>
	        </ul>
		</h3>
        <xsl:call-template name="msgbox"/>

        <dl class="proplist">
            <dt><xsl:value-of select="php:function('lang', 'Name')" /></dt>
            <dd><xsl:value-of select="group/name"/></dd>

            <dt><xsl:value-of select="php:function('lang', 'Organization')" /></dt>
            <dd><xsl:value-of select="group/organization_name"/></dd>

			<dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
	        <dd><xsl:value-of select="group/description" disable-output-escaping="yes"/></dd>
			
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
