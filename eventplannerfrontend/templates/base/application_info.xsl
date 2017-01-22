
<xsl:template name="application_info" xmlns:php="http://php.net/xsl">
	<xsl:param name="application" />
	<xsl:param name="application_type_list" />

	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'vendor')"/>
		</label>
		<xsl:value-of select="application/vendor_name"/>
	</div>
	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'event')"/>
		</label>
		<xsl:value-of select="application/title"/>
	</div>

	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'remark')"/>
		</label>
		<xsl:value-of select="application/remark"/>
	</div>

	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'contact name')"/>
		</label>
		<xsl:value-of select="application/contact_name"/>
	</div>
	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'contact email')"/>
		</label>
		<xsl:value-of select="application/contact_email"/>
	</div>
	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'contact phone')"/>
		</label>
		<xsl:value-of select="application/contact_phone"/>

	</div>
	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'program type')"/>
		</label>
		<div class="pure-custom">
			<table class="pure-table pure-table-bordered" border="0" cellspacing="2" cellpadding="2">
				<thead>
					<tr>
						<th>
						</th>
						<th>
							<xsl:value-of select="php:function('lang', 'program type')"/>
						</th>
					</tr>
				</thead>
				<tbody>
					<xsl:for-each select="application_type_list">
						<tr>
							<td>
								<xsl:if test="selected = 1">
									<xsl:text>X</xsl:text>
								</xsl:if>
							</td>
							<td>
								<xsl:value-of disable-output-escaping="yes" select="name"/>
							</td>
						</tr>
					</xsl:for-each>
				</tbody>
			</table>
		</div>
		<div class="pure-control-group">
			<label>
				<xsl:value-of select="php:function('lang', 'event timespan')"/>
			</label>
			<xsl:value-of select="application/timespan"/>
		</div>

	</div>

</xsl:template>
