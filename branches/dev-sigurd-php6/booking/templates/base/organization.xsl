<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_booking_i18n"/>
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="organization/organizations_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Organization')" />
                </a>
            </li>
            <li>
                <a href="">
                    <xsl:value-of select="organization/name"/>
                </a>
            </li>
        </ul>
        <dl class="proplist-col">
            <dt><xsl:value-of select="php:function('lang', 'Homepage')" /></dt>
            <dd>
				<a target="blank" href="http://{organization/homepage}"><xsl:value-of select="organization/homepage" /></a>
			</dd>

            <dt><xsl:value-of select="php:function('lang', 'Email')" /></dt>
            <dd><a href="mailto:{organization/email}"><xsl:value-of select="organization/email"/></a></dd>

            <dt><xsl:value-of select="php:function('lang', 'Phone')" /></dt>
            <dd><xsl:value-of select="organization/phone"/></dd>

            <dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
            <dd><xsl:value-of select="organization/description" disable-output-escaping="yes"/></dd>

			<xsl:if test="count(organization/contacts/*) &gt; 0">
            	<dt><xsl:value-of select="php:function('lang', 'Admins')" /></dt>
	            <dd>
	                <ul>
						<xsl:if test="organization/contacts[1]">
							<li><xsl:value-of select="organization/contacts[1]/name"/></li>
						</xsl:if>
					
	                    <xsl:if test="organization/contacts[2]">
	                    	<li><xsl:value-of select="organization/contacts[2]/name"/></li>
						</xsl:if>
	                </ul>
	            </dd>
			</xsl:if>
        </dl>

		<dl class="proplist-col">
			<dt><xsl:value-of select="php:function('lang', 'Street')" /></dt>
            <dd><xsl:value-of select="organization/street"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'Zip code')" /></dt>
            <dd><xsl:value-of select="organization/zip_code"/></dd>

			<dt><xsl:value-of select="php:function('lang', 'City')" /></dt>
            <dd><xsl:value-of select="organization/city"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'District')" /></dt>
            <dd><xsl:value-of select="organization/district"/></dd>
		</dl>
		
		<div class="form-buttons">
			<button onclick="window.location.href='{organization/edit_link}'">
	            <xsl:value-of select="php:function('lang', 'Edit')" />
	        </button>
		</div>
    </div>
</xsl:template>
