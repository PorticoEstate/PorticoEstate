<xsl:template match="data" xmlns:php="http://php.net/xsl">
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
				<a>
					<xsl:attribute name="href"><xsl:value-of select="organization/homepage" /></xsl:attribute>
					<xsl:value-of select="organization/homepage" />
				</a>
			</dd>

            <dt><xsl:value-of select="php:function('lang', 'Email')" /></dt>
            <dd><xsl:value-of select="organization/email"/></dd>

            <dt><xsl:value-of select="php:function('lang', 'Phone')" /></dt>
            <dd><xsl:value-of select="organization/phone"/></dd>

            <dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
            <dd><xsl:value-of select="organization/description" disable-output-escaping="yes"/></dd>

            <dt><xsl:value-of select="php:function('lang', 'Admins')" /></dt>
            <dd>
                <ul>
                    <li><xsl:value-of select="organization/admin_primary/name" /></li>
                    <li>
                        <xsl:if test="organization/admin_secondary/name">
                            <xsl:value-of select="organization/admin_secondary/name" />
                        </xsl:if>
                    </li>
                </ul>
            </dd>
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
