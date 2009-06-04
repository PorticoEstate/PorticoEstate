<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
		<xsl:for-each select="search/results">
			<span style="font-size: 10px;margin-right: 2em;"><xsl:value-of select="type"/></span>
			<a class="Tillbaka">
				<xsl:attribute name="href"><xsl:value-of select="start"/></xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Building index')" />
			</a>

			<h2><xsl:value-of select="name"/></h2>
			
			<dl class="proplist">
				<dl class="proplist description">
					<dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
					<dd disable-output-escaping="yes"><xsl:value-of select="description"/></dd>
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
					
					<xsl:if test="address and normalize-space(address)">
						<dt><xsl:value-of select="php:function('lang', 'Address')" /></dt>
						<dd class="address"><xsl:value-of select="address"/></dd>
					</xsl:if>
				</dl>
				
				<h3><xsl:value-of select="php:function('lang', 'Bookable resources')" /></h3>
				<div id="resources_container"/>
				<div>
					<a>
						<xsl:attribute name="href"><xsl:value-of select="schedule_link"/></xsl:attribute>
						<xsl:value-of select="php:function('lang', 'View booking schedule for this building')" />
					</a>
				</div>
			
				<h3><xsl:value-of select="php:function('lang', 'Documents')" /></h3>
				<div id="documents_container"/>
				
				<h3><xsl:value-of select="php:function('lang', 'Images')" /></h3>
				<div id="images_container"/>
			</dl>
			
			<script type="text/javascript">
				var building_id = <xsl:value-of select="id"/>;
				<![CDATA[
				YAHOO.util.Event.addListener(window, "load", function() {
				var url = 'index.php?menuaction=bookingfrontend.uiresource.index_json&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
				var colDefs = [{key: 'name', label: 'Name', formatter: YAHOO.booking.formatLink}];
				YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);
				});
				
				var url = 'index.php?menuaction=bookingfrontend.uidocument_building.index&sort=name&filter_owner_id=' + building_id + '&phpgw_return_as=json&';
				var colDefs = [{key: 'name', label: 'Name', formatter: YAHOO.booking.formatLink}, {key: 'category', label: 'Category'}];
				YAHOO.booking.inlineTableHelper('documents_container', url, colDefs);
				
				var url = 'index.php?menuaction=bookingfrontend.uidocument_building.index_images&sort=name&filter_owner_id=' + building_id + '&phpgw_return_as=json&';
				YAHOO.booking.inlineImages('images_container', url);
				]]>
			</script>
		</xsl:for-each>
	</div>
</xsl:template>

