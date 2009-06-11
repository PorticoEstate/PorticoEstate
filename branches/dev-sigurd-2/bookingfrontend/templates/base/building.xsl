<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
		<ul class="pathway">
			<li>
				<xsl:value-of select="php:function('lang', 'Buildings')" />
			</li>
			<li>
				<a href="{resource/building_link}">
					<xsl:value-of select="building/name"/>
				</a>
			</li>
		</ul>

		<xsl:for-each select="building">	
			<dl class="proplist-col main">
				<dl class="proplist description">
					<dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
					<dd><xsl:value-of select="description" disable-output-escaping="yes"/></dd>
				</dl>
				
				<h3><xsl:value-of select="php:function('lang', 'Contact information')" /></h3>
				<dl class="contactinfo">
					<dt><xsl:value-of select="php:function('lang', 'Homepage')" /></dt>
					<dd><a href="{homepage}"><xsl:value-of select="homepage"/></a></dd>
					
					<xsl:if test="email and normalize-space(email)">
						<dt><xsl:value-of select="php:function('lang', 'Email')" /></dt>
						<dd><a href='mailto:{email}'><xsl:value-of select="email"/></a></dd>
					</xsl:if>
					
					<xsl:if test="phone and normalize-space(phone)">
						<dt><xsl:value-of select="php:function('lang', 'Telephone')" /></dt>
						<dd><xsl:value-of select="phone"/></dd>
					</xsl:if>
					
					<xsl:if test="street and normalize-space(street)">
						<dt><xsl:value-of select="php:function('lang', 'Street')" /></dt>
						<dd><xsl:value-of select="street"/></dd>
					</xsl:if>

					<xsl:if test="zip_code and normalize-space(zip_code)">
						<dt><xsl:value-of select="php:function('lang', 'Zip code')" /></dt>
						<dd><xsl:value-of select="zip_code"/></dd>
					</xsl:if>

					<xsl:if test="city and normalize-space(city)">
						<dt><xsl:value-of select="php:function('lang', 'City')" /></dt>
						<dd><xsl:value-of select="city"/></dd>	
					</xsl:if>

					<xsl:if test="district and normalize-space(district)">
						<dt><xsl:value-of select="php:function('lang', 'District')" /></dt>
						<dd><xsl:value-of select="district"/></dd>
					</xsl:if>
				</dl>
				
				<h3><xsl:value-of select="php:function('lang', 'Bookable resources')" /></h3>
				<div id="resources_container"/>

		        <button onclick="window.location.href='{schedule_link}'">
		            <xsl:value-of select="php:function('lang', 'Building schedule')" />
		        </button>

				<h3><xsl:value-of select="php:function('lang', 'Documents')" /></h3>
				<div id="documents_container"/>
			</dl>
			<dl class="proplist-col images">	
				<div id="images_container"></div>
			</dl>
			
			<script type="text/javascript">
				var building_id = <xsl:value-of select="id"/>;
				var lang = <xsl:value-of select="php:function('js_lang', 'Resource Name', 'Document Name', 'category', 'Activity')"/>;
				<![CDATA[
				
				YAHOO.util.Event.addListener(window, "load", function() {
				var url = 'index.php?menuaction=bookingfrontend.uiresource.index_json&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
				var colDefs = [{key: 'name', label: lang['Resource Name'], formatter: YAHOO.booking.formatLink}, {key: 'activity_name', label: lang['Activity']}];
				YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);
				});
				
				var url = 'index.php?menuaction=bookingfrontend.uidocument_building.index&sort=name&filter_owner_id=' + building_id + '&phpgw_return_as=json&';
				var colDefs = [{key: 'name', label: lang['Document Name'], formatter: YAHOO.booking.formatLink}, {key: 'category', label: lang.category}];
				YAHOO.booking.inlineTableHelper('documents_container', url, colDefs);
				
				var url = 'index.php?menuaction=bookingfrontend.uidocument_building.index_images&sort=name&filter_owner_id=' + building_id + '&phpgw_return_as=json&';
				YAHOO.booking.inlineImages('images_container', url);
				]]>
			</script>
		</xsl:for-each>
	</div>
</xsl:template>

