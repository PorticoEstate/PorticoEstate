<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
		<div id="result">
			<br />
			<br />
			<xsl:for-each select="search/results">
				<div style="margin-bottom: 2em;border: 0px solid #000000;"><span style="font-size: 10px;margin-right: 2em;"><xsl:value-of select="type"/></span>
					<a class="Tillbaka">
						<xsl:attribute name="href"><xsl:value-of select="start"/></xsl:attribute>
						<xsl:value-of select="php:function('lang', 'Building index')" />
					</a>
					<h2><xsl:value-of select="name"/></h2>

					<h4><xsl:value-of select="php:function('lang', 'Description')" /></h4>
					<div class="description"><xsl:value-of select="description"/></div>

					<h4><xsl:value-of select="php:function('lang', 'Activities')" /></h4>
					<h4><xsl:value-of select="php:function('lang', 'Bookable resources')" /></h4>
					<div id="resources_container"/>
					<div>
						<a>
							<xsl:attribute name="href"><xsl:value-of select="schedule_link"/></xsl:attribute>
							<xsl:value-of select="php:function('lang', 'View booking schedule for this building')" />
						</a>
					</div>

					<h4><xsl:value-of select="php:function('lang', 'Contact information')" /></h4>
					<dl class="contactinfo">
						<dt><xsl:value-of select="php:function('lang', 'Homepage')" /></dt>
						<dd>
							<a>
								<xsl:attribute name="href"><xsl:value-of select="homepage"/></xsl:attribute>
								<xsl:value-of select="homepage"/>
							</a>
						</dd>

						<dt><xsl:value-of select="php:function('lang', 'Email')" /></dt>
						<dd>
							<a>
								<xsl:attribute name="href">mailto:<xsl:value-of select="email"/></xsl:attribute>
								<xsl:value-of select="email"/>
							</a>
						</dd>

						<dt><xsl:value-of select="php:function('lang', 'Telephone')" /></dt>
						<dd><xsl:value-of select="phone"/></dd>

						<dt><xsl:value-of select="php:function('lang', 'Address')" /></dt>
						<dd class="address"><xsl:value-of select="address"/></dd>
					</dl>
				</div>


<script type="text/javascript">
var building_id = <xsl:value-of select="id"/>;
	<![CDATA[
YAHOO.util.Event.addListener(window, "load", function() {
	var url = 'index.php?menuaction=bookingfrontend.uiresource.index_json&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
	var colDefs = [{key: 'name', label: 'Name', formatter: YAHOO.booking.formatLink}];
	YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);
});
]]>
</script>

			</xsl:for-each>
		</div>
	</div>
</xsl:template>

