<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

	<xsl:if test="step = 1">
		<dl class="form">
			<dt class="heading"><xsl:value-of select="php:function('lang', 'Report numbers')"/></dt>
		</dl>
		<p><strong><xsl:value-of select="php:function('lang', 'Please enter correct numbers for the event')"/>:</strong></p>

		<table id="agegroup">
			<tr>
				<th><xsl:value-of select="php:function('lang', 'Where')" />:</th>
				<td><xsl:value-of select="building/name" /></td>
			</tr>
			<tr>
				<th><xsl:value-of select="php:function('lang', 'When')" />:</th>
				<td><xsl:value-of select="php:function('pretty_timestamp', event_object/from_)" /> - <xsl:value-of select="php:function('pretty_timestamp', event_object/to_)" /></td>
			</tr>
			<xsl:if test="event_object/group_name">
			<tr>
				<th><xsl:value-of select="php:function('lang', 'Who')" />:</th>
				<td><xsl:value-of select="event_object/group_name" /></td>
			</tr>
			</xsl:if>
		</table>

		<form action="" method="POST">
			<dl class="form-col">
				<dt><label for="field_from"><xsl:value-of select="php:function('lang', 'Number of participants')" /></label></dt>
				<dd>
					<table id="agegroup">
						<tr><th/><th><xsl:value-of select="php:function('lang', 'Male')" /></th>
							<th><xsl:value-of select="php:function('lang', 'Female')" /></th></tr>
						<xsl:for-each select="agegroups">
							<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
							<tr>
								<th><xsl:value-of select="name"/></th>
								<td>
									<input type="text">
										<xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
										<xsl:attribute name="value"><xsl:value-of select="../event_object/agegroups/male[../agegroup_id = $id]"/></xsl:attribute>
									</input>
								</td>
								<td>
									<input type="text">
										<xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
										<xsl:attribute name="value"><xsl:value-of select="../event_object/agegroups/female[../agegroup_id = $id]"/></xsl:attribute>
									</input>
								</td>
							</tr>
						</xsl:for-each>
					</table>
				</dd>
			</dl>
			<div class="form-buttons">
				<input type="submit">
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
				</input>
			</div>
		</form>
	</xsl:if>
	<xsl:if test="step = 2">
		<dl class="form">
			<dt class="heading"><xsl:value-of select="php:function('lang', 'Thank you')"/>!</dt>
		</dl>
		<p><dt class="heading"><xsl:value-of select="php:function('lang', 'The data was successfully updated')"/>!</dt></p>
	</xsl:if>
    </div>
</xsl:template>
