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
			
			<dt><xsl:value-of select="php:function('lang', 'Street')" /></dt>
            <dd><xsl:value-of select="organization/street"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'Zip code')" /></dt>
            <dd><xsl:value-of select="organization/zip_code"/></dd>

			<dt><xsl:value-of select="php:function('lang', 'City')" /></dt>
            <dd><xsl:value-of select="organization/city"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'District')" /></dt>
            <dd><xsl:value-of select="organization/district"/></dd>
        </dl>

        <h3><xsl:value-of select="php:function('lang', 'Groups')" /></h3>
        <div id="groups_container"/>
    </div>
	
	<script type="text/javascript">
	var organization_id = <xsl:value-of select="organization/id"/>;
	var lang = new Object();
	lang.name = '<xsl:value-of select="php:function('lang', 'Name')"/>';
	lang.primary_contact_name = '<xsl:value-of select="php:function('lang', 'Primary contact')"/>';
	lang.primary_contact_phone = '<xsl:value-of select="php:function('lang', 'Phone')"/>';
	lang.primary_contact_mail = '<xsl:value-of select="php:function('lang', 'Email')"/>';
	
	<![CDATA[
	YAHOO.util.Event.addListener(window, "load", function() {
		var url = 'index.php?menuaction=bookingfrontend.uigroup.index&sort=name&filter_organization_id=' + organization_id + '&phpgw_return_as=json&';
		var colDefs = [
			{key: 'name', label: lang.name, formatter: YAHOO.booking.formatLink},
			/*{key: 'primary_contact_name', label: lang.primary_contact_name},
			{key: 'primary_contact_phone', label: lang.primary_contact_phone},
			{key: 'primary_contact_email', label: lang.primary_contact_mail},*/
		];
		YAHOO.booking.inlineTableHelper('groups_container', url, colDefs);
	});
	]]>
	</script>

</xsl:template>



