<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
		<ul id="metanav">
			<xsl:choose>
				<xsl:when test="organization/logged_on">
					<a href="{organization/logoff_link}"><xsl:value-of select="php:function('lang', 'Log off')" /></a>
				</xsl:when>
				<xsl:otherwise>
					<a href="{organization/login_link}"><xsl:value-of select="php:function('lang', 'Log on')" /></a>
				</xsl:otherwise>
			</xsl:choose>
	    </ul>
		<br/>
		<button onclick="window.location.href='{organization/start}'">
            <xsl:value-of select="php:function('lang', 'Organization index')" />
        </button>
		
		<h2>
			<xsl:value-of select="organization/name"/>
			<xsl:if test="organization/permission/write">
				<span class="loggedin">
					<a href="{organization/edit_link}"><img src="phpgwapi/templates/base/images/edit.png" /></a>
				</span>
			</xsl:if>
		</h2>
		
		<xsl:if test="description and normalize-space(description)">
			<dl class="proplist description">
	            <dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
	            <dd><xsl:value-of select="organization/description" disable-output-escaping="yes"/></dd>
	        </dl>
		</xsl:if>

        <h3><xsl:value-of select="php:function('lang', 'Contact information')" /></h3>
        <dl class="proplist contactinfo">
	
			<xsl:if test="organization/homepage and normalize-space(organization/homepage)">		
	            <dt><xsl:value-of select="php:function('lang', 'Homepage')" /></dt>
	            <dd>
	                <a target="blank" href="http://{organization/homepage}"><xsl:value-of select="organization/homepage" /></a>
	            </dd>
			</xsl:if>
			
			<xsl:if test="organization/email and normalize-space(organization/email)">
				<dt><xsl:value-of select="php:function('lang', 'Email')" /></dt>
	            <dd><a href="mailto:{organization/email}"><xsl:value-of select="organization/email"/></a></dd>
			</xsl:if>

			<xsl:if test="organization/phone and normalize-space(organization/phone)">
			    <dt><xsl:value-of select="php:function('lang', 'Phone')" /></dt>
	            <dd><xsl:value-of select="organization/phone"/></dd>	
			</xsl:if>

			<xsl:if test="organization/street and normalize-space(organization/street)">
				<dt><xsl:value-of select="php:function('lang', 'Street')" /></dt>
	            <dd><xsl:value-of select="organization/street"/></dd>
			</xsl:if>
			
			<xsl:if test="organization/zip_code and normalize-space(organization/zip_code)">
				<dt><xsl:value-of select="php:function('lang', 'Zip code')" /></dt>
	            <dd><xsl:value-of select="organization/zip_code"/></dd>
			</xsl:if>

			<xsl:if test="organization/city and normalize-space(organization/city)">
				<dt><xsl:value-of select="php:function('lang', 'City')" /></dt>
	            <dd><xsl:value-of select="organization/city"/></dd>	
			</xsl:if>

			<xsl:if test="organization/district and normalize-space(organization/district)">
				<dt><xsl:value-of select="php:function('lang', 'District')" /></dt>
	            <dd><xsl:value-of select="organization/district"/></dd>
			</xsl:if>

        </dl>

        <h3><xsl:value-of select="php:function('lang', 'Groups')" /></h3>
        <div id="groups_container"/>
    </div>
	
	<script type="text/javascript">
		var organization_id = <xsl:value-of select="organization/id"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Group')"/>;
	
		<![CDATA[
		YAHOO.util.Event.addListener(window, "load", function() {
			var url = 'index.php?menuaction=bookingfrontend.uigroup.index&sort=name&filter_organization_id=' + organization_id + '&phpgw_return_as=json&';
			var colDefs = [
				{key: 'name', label: 'Group', formatter: YAHOO.booking.formatLink}, {key: 'link', 'hidden': true}
			];
			YAHOO.booking.inlineTableHelper('groups_container', url, colDefs);
		});
		]]>
	</script>

</xsl:template>



