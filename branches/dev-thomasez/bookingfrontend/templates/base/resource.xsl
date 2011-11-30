<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_booking_i18n"/>
	
	<div id="content">
		<ul class="pathway">
			<li><a href="index.php?menuaction=bookingfrontend.uisearch.index"><xsl:value-of select="php:function('lang', 'Home')" /></a></li>
			<li>
				<a href="{resource/building_link}">
					<xsl:value-of select="resource/building_name"/>
				</a>
			</li>
			<li>
                <xsl:value-of select="resource/name"/>
			</li>
		</ul>

		<div>
        	<button onclick="window.location.href='{resource/schedule_link}'"><xsl:value-of select="php:function('lang', 'Resource schedule')" /></button>
- 				Søk ledig tid / gå til booking
		</div>

		<dl class="proplist-col main">
			
			<xsl:if test="resource/description and normalize-space(resource/description)">
				<dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
				<dd><xsl:value-of disable-output-escaping="yes" select="resource/description"/></dd>
			</xsl:if>
			
			<xsl:if test="resource/activity_name and normalize-space(resource/activity_name)">
				<dt><xsl:value-of select="php:function('lang', 'Activity')" /></dt>
				<dd><xsl:value-of select="resource/activity_name"/></dd>
			</xsl:if>
			
			<dt><xsl:value-of select="php:function('lang', 'Resource Type')" /></dt>
			<dd><xsl:value-of select="php:function('lang', string(resource/type))"/></dd>

			
			<xsl:if test="not (resource/internal_cost='')">
				<dt><xsl:value-of select="php:function('lang', 'Internal cost')"/></dt>
				<dd><span class="space"><xsl:value-of select="resource/internal_cost"/></span>
					<xsl:value-of select="php:function('lang', string(resource/cost_type))"/></dd>
			</xsl:if>
			<xsl:if test="not (resource/external_cost='')">
				<dt><xsl:value-of select="php:function('lang', 'External cost')"/></dt>
				<dd><span class="space"><xsl:value-of select="resource/external_cost"/></span>
					<xsl:value-of select="php:function('lang', string(resource/cost_type))"/></dd>
			</xsl:if>
			
			<h3><xsl:value-of select="php:function('lang', 'Documents')" /></h3>
			<div id="documents_container"/>
		</dl>
		<dl class="proplist-col images">	
			<div id="images_container"></div>

			<xsl:if test="not(resource/campsites='')">				
				<dt><label for="field_campsites"><xsl:value-of select="php:function('lang', 'Campsites')"/></label></dt>
				<dd><xsl:value-of select="resource/campsites"/></dd>
			</xsl:if>
			<xsl:if test="not(resource/bedspaces='')">				
				<dt><label for="field_bedspaces"><xsl:value-of select="php:function('lang', 'Bedspaces')"/></label></dt>
				<dd><xsl:value-of select="resource/bedspaces"/></dd>
			</xsl:if>
			<xsl:if test="not(resource/heating='')">				
				<dt><label for="field_heating"><xsl:value-of select="php:function('lang', 'Heating')"/></label></dt>
				<dd><xsl:value-of select="resource/heating"/></dd>
			</xsl:if>
			<xsl:if test="not(resource/kitchen='')">				
				<dt><label for='field_kitchen'><xsl:value-of select="php:function('lang', 'Kitchen')"/></label></dt>
				<dd><xsl:value-of select="resource/kitchen"/></dd>
			</xsl:if>
			<xsl:if test="not(resource/water='')">				
				<dt><label for="field_water"><xsl:value-of select="php:function('lang', 'Water')"/></label></dt>
				<dd><xsl:value-of select="resource/water"/></dd>
			</xsl:if>
			<xsl:if test="not(resource/location='')">				
				<dt><label for="field_location"><xsl:value-of select="php:function('lang', 'Locality')"/></label></dt>
				<dd><xsl:value-of select="resource/location"/></dd>
			</xsl:if>
			<xsl:if test="not(resource/communication='')">				
				<dt><label for='field_communication'><xsl:value-of select="php:function('lang', 'Communication')"/></label></dt>
				<dd><xsl:value-of select="resource/communication"/></dd>
			</xsl:if>
			<xsl:if test="not(resource/usage_time='')">				
				<dt><label for='field_usage_time'><xsl:value-of select="php:function('lang', 'Usage time')"/></label></dt>
				<dd><xsl:value-of select="resource/usage_time"/></dd>
			</xsl:if>

		</dl>
	</div>

	<script type="text/javascript">
		var resource_id = <xsl:value-of select="resource/id"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'category', 'Activity')"/>;
<![CDATA[
	YAHOO.util.Event.addListener(window, "load", function() {

	var url = 'index.php?menuaction=bookingfrontend.uidocument_resource.index&sort=name&no_images=1&filter_owner_id=' + resource_id + '&phpgw_return_as=json&';
	var colDefs = [{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}];
	YAHOO.booking.inlineTableHelper('documents_container', url, colDefs);
	
	var url = 'index.php?menuaction=bookingfrontend.uidocument_resource.index_images&sort=name&filter_owner_id=' + resource_id + '&phpgw_return_as=json&';
	YAHOO.booking.inlineImages('images_container', url);
});
]]>
	</script>

</xsl:template>
