<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_booking_i18n"/>
	
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
		<ul class="pathway">
			<li><a href="index.php?menuaction=bookingfrontend.uisearch.index"><xsl:value-of select="php:function('lang', 'Home')" /></a></li>
			<li>
				<a href="{resource/building_link}">
					<xsl:value-of select="resource/building_name"/>
				</a>
			</li>
			<li>
                <xsl:value-of select="organization/name"/>
			</li>
		</ul>

		<xsl:if test="organization/permission/write">
			<span class="loggedin">
				<a href="{organization/edit_link}"><img src="../phpgwapi/templates/base/images/edit.png" /></a>
			</span>
		</xsl:if>
		
		<xsl:if test="organization/description and normalize-space(organization/description)">
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
						<dt><xsl:value-of select="php:function('lang', 'Address')" /></dt>
						<dd>
							<xsl:value-of select="organization/street"/><br/>
							<xsl:value-of select="organization/zip_code"/><span>&nbsp; </span>
							<xsl:value-of select="organization/city"/><br/>
							<xsl:value-of select="organization/district"/>
						</dd>
			</xsl:if>

        </dl>

        <h3><xsl:value-of select="php:function('lang', 'Groups')" /></h3>
        <div id="groups_container"/>

		  <h3><xsl:value-of select="php:function('lang', 'Used buildings')" /></h3>
        <div id="buildings_used_by_container"/>
    </div>
	
	<script type="text/javascript">
		var organization_id = <xsl:value-of select="organization/id"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name')"/>;
	
		<![CDATA[
		YAHOO.util.Event.addListener(window, "load", function() {
			var url = 'index.php?menuaction=bookingfrontend.uigroup.index&sort=name&filter_organization_id=' + organization_id + '&phpgw_return_as=json&';
			var colDefs = [
				{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}, {key: 'link', 'hidden': true}
			];
			YAHOO.booking.inlineTableHelper('groups_container', url, colDefs);
			
			var url = 'index.php?menuaction=bookingfrontend.uibuilding.find_buildings_used_by&sort=name&organization_id=' + organization_id + '&phpgw_return_as=json&';
			var colDefs = [{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}];
			YAHOO.booking.inlineTableHelper('buildings_used_by_container', url, colDefs);
		});
		]]>
	</script>

</xsl:template>



