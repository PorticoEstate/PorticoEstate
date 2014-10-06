<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="group/organizations_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Organization')" />
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="group/organization_link"/></xsl:attribute>
                    <xsl:value-of select="group/organization_name"/>
                </a>
            </li>
            <li><xsl:value-of select="php:function('lang', 'Group')" /></li>
            <li>
                    <xsl:value-of select="group/name"/>
            </li>
        </ul>
        <xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>

        <dl class="proplist">
            <dt><xsl:value-of select="php:function('lang', 'Organization')" /></dt>
            <dd><xsl:value-of select="group/organization_name"/></dd>

            <dt><xsl:value-of select="php:function('lang', 'Name')" /></dt>
            <dd><xsl:value-of select="group/name"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Group shortname')" /></dt>
            <dd><xsl:value-of select="group/shortname"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Activity')" /></dt>
            <dd><xsl:value-of select="group/activity_name" /></dd>

			<xsl:if test="count(group/contacts/*) &gt; 0">
            	<dt><xsl:value-of select="php:function('lang', 'Team leaders')" /></dt>
	            <dd>
	                <ul>
						<xsl:if test="group/contacts[1]">
							<li><xsl:value-of select="group/contacts[1]/name"/></li>
						</xsl:if>
					
	                    <xsl:if test="group/contacts[2]">
	                    	<li><xsl:value-of select="group/contacts[2]/name"/></li>
						</xsl:if>
	                </ul>
	            </dd>
			</xsl:if>

            <dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
            <dd><xsl:value-of select="group/description" disable-output-escaping="yes"/></dd>


        </dl>

        <a class="button">
            <xsl:attribute name="href"><xsl:value-of select="group/edit_link"/></xsl:attribute>
            <xsl:value-of select="php:function('lang', 'Edit')" />
        </a>
    </div>
</xsl:template>
