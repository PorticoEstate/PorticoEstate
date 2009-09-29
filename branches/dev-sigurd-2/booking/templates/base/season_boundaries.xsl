<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="season/buildings_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Buildings')" />
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="season/building_link"/></xsl:attribute>
                    <xsl:value-of select="season/building_name"/>
                </a>
            </li>
            <li><xsl:value-of select="php:function('lang', 'Seasons')" /></li>
            <li>
				<a>
                    <xsl:attribute name="href"><xsl:value-of select="season/season_link"/></xsl:attribute>
					<xsl:value-of select="season/name"/>
				</a>
			</li>
            <li><xsl:value-of select="php:function('lang', 'Boundaries')" /></li>
        </ul>

        <xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>
				
		<table id="boundary-table">
			<thead>
				<tr>
					<th><xsl:value-of select="php:function('lang', 'Week day')" /></th>
					<th><xsl:value-of select="php:function('lang', 'From')" /></th>
					<th><xsl:value-of select="php:function('lang', 'To')" /></th>
					<th />
				</tr>
			</thead>
			<tbody>
				<xsl:choose>
					<xsl:when test="count(boundaries/*) &gt; 0">
						<xsl:for-each select="boundaries">
							<tr>
								<td><xsl:value-of select="wday_name"/></td>
								<td><xsl:value-of select="from_"/></td>
								<td><xsl:value-of select="to_"/></td>
								<td>
									<xsl:if test="../season/permission/write">
										<a href="{delete_link}">
											<xsl:value-of select="php:function('lang', 'Delete')" />
										</a>
									</xsl:if>
								</td>
							</tr>
						</xsl:for-each>
					</xsl:when>
					<xsl:otherwise>
						<td colspan='4'><xsl:value-of select="php:function('lang', 'No Data.')"/></td>
					</xsl:otherwise>
				</xsl:choose>
			</tbody>
		</table>
		<xsl:if test="season/permission/write">
			<form action="" method="POST">
			<dl class="form">
				<dt class="heading"><xsl:value-of select="php:function('lang', 'Add Boundary')" /></dt>
				<dd>
				</dd>
				<dt><label for="field_status"><xsl:value-of select="php:function('lang', 'Week day')" /></label></dt>
				<dd>
					<select name="wday">
						<option value="1"><xsl:value-of select="php:function('lang', 'Monday')" /></option>
						<option value="2"><xsl:value-of select="php:function('lang', 'Tuesday')" /></option>
						<option value="3"><xsl:value-of select="php:function('lang', 'Wednesday')" /></option>
						<option value="4"><xsl:value-of select="php:function('lang', 'Thursday')" /></option>
						<option value="5"><xsl:value-of select="php:function('lang', 'Friday')" /></option>
						<option value="6"><xsl:value-of select="php:function('lang', 'Saturday')" /></option>
						<option value="7"><xsl:value-of select="php:function('lang', 'Sunday')" /></option>
					</select>
				</dd>
				<dt><label><xsl:value-of select="php:function('lang', 'From')" /></label></dt>
				<dd>
					<div class="time-picker">
						<input id="field_from" name="from_" type="text">
	                    	<xsl:attribute name="value"><xsl:value-of select="boundary/from_"/></xsl:attribute>
						</input>
					</div>
				</dd>
				<dt><label><xsl:value-of select="php:function('lang', 'To')" /></label></dt>
				<dd>
					<div class="time-picker">
						<input id="field_to" name="to_" type="text">
	                    	<xsl:attribute name="value"><xsl:value-of select="boundary/to_"/></xsl:attribute>
						</input>
					</div>
				</dd>
			</dl>
			<div class="form-buttons">
				<input type="submit">
					<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Add')"/></xsl:attribute>
				</input>
				<a class="cancel">
					<xsl:attribute name="href"><xsl:value-of select="season/cancel_link"/></xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Back to season')"/>
				</a>
			</div>
		</form>
	</xsl:if>


			<!-- <form action="" method="POST">
			<dl class="form">
				<dt class="heading"><xsl:value-of select="php:function('lang', 'Copy boundaries')" /></dt>
				<input type="search"/>
				<input type="submit">
					<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Search')"/></xsl:attribute>
				</input>
				<div id="foo_container"/>
				<input type="submit">
					<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Clone boundaries')"/></xsl:attribute>
				</input>
			</dl>
			<div class="form-buttons">
				<input type="submit">
					<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Add')"/></xsl:attribute>
				</input>
				<a class="cancel">
					<xsl:attribute name="href"><xsl:value-of select="season/cancel_link"/></xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Cancel')"/>
				</a>
			</div>
		</form> -->

<script type="text/javascript">
    <![CDATA[
YAHOO.util.Event.addListener(window, "load", function() {
    var url = 'index.php?menuaction=booking.uiseason.index&sort=name&phpgw_return_as=json&';
]]>
	YAHOO.booking.radioTableHelper('foo_container', url, 'foo');
});
</script>
	</div>
</xsl:template>
