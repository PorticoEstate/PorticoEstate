<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">

		<xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>
		<dl class="form">
			<dt class="heading">
				<xsl:value-of select="php:function('lang', 'Search terms')" />
			</dt>
		</dl>

		<form action="" method="POST">
			<dl class="form-col">
				<dt><label for="field_period"><xsl:value-of select="php:function('lang', 'Date')" /></label></dt>
				<dd>
					<div class="date-picker">
						<input id="field_period" name="period" type="text">
							<xsl:attribute name="value"><xsl:value-of select="period"/></xsl:attribute>
						</input>
					</div>
				</dd>
			</dl>
			<dl class="form-col">
				<dt><label for="field_dimension"><xsl:value-of select="php:function('lang', 'View results from...')" /></label></dt>
				<dd>
					<select id="field_dimension" name="dimension">
						<option value="month"><xsl:value-of select="php:function('lang', 'Month')" /></option>
						<option value="year"><xsl:value-of select="php:function('lang', 'Year')" /></option>
						<option value="forever"><xsl:value-of select="php:function('lang', 'Forever')" /></option>
					</select>
				</dd>
			</dl>
			<dl class="form-col">
				<input type="submit">
					<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'View search terms')"/></xsl:attribute>
				</input>
			</dl>
			<div class="form-buttons">
			</div>
		</form>
		<table id="report">
			<thead>
				<tr>
					<th><xsl:value-of select="php:function('lang', 'Term')"/></th>
					<th><xsl:value-of select="php:function('lang', 'Count')"/></th>
				</tr>
			</thead>
			<tbody>
				<xsl:for-each select="terms">
					<tr>
						<td><xsl:value-of select="term"/></td>
						<td><xsl:value-of select="count"/></td>
					</tr>
				</xsl:for-each>
			</tbody>
		</table>
	</div>
</xsl:template>
