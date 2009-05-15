<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
		<h2>
			<xsl:value-of select="organization/name"/>
			<xsl:if test="loggedin &gt; 0">
				<span class="loggedin"><a>
						<xsl:attribute name="href">
							<xsl:value-of select="edit_self_link" />
						</xsl:attribute>
						<img src="/phpgwapi/templates/base/images/edit.png" />
				</a></span>
			</xsl:if>
		</h2>
        <dl class="proplist description">
            <dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
            <dd><xsl:value-of select="organization/description" disable-output-escaping="yes"/></dd>
        </dl>

        <h3><xsl:value-of select="php:function('lang', 'Contact information')" /></h3>
        <dl class="proplist contactinfo">
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
        </dl>

        <h3><xsl:value-of select="php:function('lang', 'Groups')" /></h3>
		<xsl:if test="loggedin &gt; 0">
			<span class="loggedin">(<a>
				<xsl:attribute name="href">
					<xsl:value-of select="edit_groups_link" />
				</xsl:attribute>
                Create new
			</a>)</span>
		</xsl:if>
        <table class="groups fancyTable">
            <thead>
                <tr>
                    <th><xsl:value-of select="php:function('lang', 'Group ID')" /></th>
                    <th><xsl:value-of select="php:function('lang', 'Group name')" /></th>
                    <th><xsl:value-of select="php:function('lang', 'Activity type')" /></th>
                    <th><xsl:value-of select="php:function('lang', 'Primary contact')" /></th>
                    <th><xsl:value-of select="php:function('lang', 'Secondary contact')" /></th>
                </tr>
            </thead>
            <tbody>
                <xsl:for-each select="organization/groups">
                    <xsl:sort select="name" order="ascending"/>
                    <tr>
                        <td><xsl:value-of select="id" /></td>
                        <td>
                            <a>
                                <xsl:attribute name="href">
                                    <xsl:value-of select="link" />	
                                </xsl:attribute>
                                <xsl:value-of select="name" />
                            </a>
                        </td>
                        <td><xsl:value-of select="description" /></td>
                        <td><xsl:value-of select="cp/name" /></td>
                        <td><xsl:value-of select="cs/name" /></td>
                    </tr>
                </xsl:for-each>
            </tbody>
        </table>

    </div>
</xsl:template>
