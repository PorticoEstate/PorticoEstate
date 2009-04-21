<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
		<ul class="pathway">
			<li>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="resource/buildings_link"/></xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Buildings')" />
				</a>
			</li>
			<li>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="resource/building_link"/></xsl:attribute>
					<xsl:value-of select="resource/building_name"/>
				</a>
			</li>
			<li><xsl:value-of select="php:function('lang', 'Resources')" /></li>
			<li>
				<a href="">
					<xsl:value-of select="resource/name"/>
				</a>
			</li>
		</ul>

		<dl class="proplist">
			<dt><xsl:value-of select="php:function('lang', 'Building')" /></dt>
			<dd><xsl:value-of select="resource/building_name"/></dd>
			<dt><xsl:value-of select="php:function('lang', 'Name')" /></dt>
			<dd><xsl:value-of select="resource/name"/></dd>
			<dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
			<dd>
				<xsl:choose>
					<xsl:when test="string-length(resource/description) &gt; 1">
						<xsl:value-of select="resource/description"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="php:function('lang', 'No description yet')" />
					</xsl:otherwise>
				</xsl:choose>
			</dd>
			<dt><xsl:value-of select="php:function('lang', 'Activity')" /></dt>
			<dd>
				<xsl:choose>
					<xsl:when test="string-length(resource/activity) &gt; 1">
						<xsl:value-of select="resource/activity"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="php:function('lang', 'No activities')" />
					</xsl:otherwise>
				</xsl:choose>
			</dd>
		</dl>

		<a class="button">
			<xsl:attribute name="href"><xsl:value-of select="resource/schedule_link"/></xsl:attribute>
			<xsl:value-of select="php:function('lang', 'Resource schedule')" />
		</a>


		<h4><xsl:value-of select="php:function('lang', 'Equipment')" /></h4>
		<div id="equipment_container"/>

	</div>

	<script type="text/javascript">
var resource_id = <xsl:value-of select="resource/id"/>;
<![CDATA[
YAHOO.util.Event.addListener(window, "load", function() {
	var url = 'index.php?menuaction=bookingfrontend.uiequipment.index&sort=name&filter_resource_id=' + resource_id + '&phpgw_return_as=json&';
	var colDefs = [{key: 'name', label: 'Name', formatter: YAHOO.booking.formatLink}];
	YAHOO.booking.inlineTableHelper('equipment_container', url, colDefs);
});
]]>
	</script>

</xsl:template>
