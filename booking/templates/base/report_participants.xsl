<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">

		<xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>

		<dl class="form">
			<dt class="heading">
				<xsl:value-of select="php:function('lang', 'Participants Per Age Group Per Month')" />
			</dt>
		</dl>

		<form action="" method="POST">
			<dl class="form-col">
				<dt><label for="field_from"><xsl:value-of select="php:function('lang', 'From')" /></label></dt>
				<dd>
					<div class="date-picker">
						<input id="field_from" name="from" type="text">
							<xsl:attribute name="value"><xsl:value-of select="from"/></xsl:attribute>
						</input>
					</div>
				</dd>
			</dl>
			<dl class="form-col">
				<dt><label for="field_to"><xsl:value-of select="php:function('lang', 'To')" /></label></dt>
				<dd>
					<div class="date-picker">
						<input id="field_to" name="to" type="text">
							<xsl:attribute name="value"><xsl:value-of select="to"/></xsl:attribute>
						</input>
					</div>
				</dd>
			</dl>
			<div class="clr" />
			<dl class="form-col">
				<dt><label><xsl:value-of select="php:function('lang', 'buildings')"/></label></dt>
				<dd>
					<select id="field_building" name="building[]" size="10" multiple="multiple" class="full-width">
						<xsl:for-each select="buildings">
							<xsl:sort select="name"/>
							<option>
								<xsl:if test="../building = id">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
								<xsl:value-of select="name"/>
							</option>
						</xsl:for-each>
					</select>
				</dd>
			</dl>
			<dl class="form-col">
				<dt><label for="output_type"><xsl:value-of select="php:function('lang', 'Format')" /></label></dt>
				<dd>
					<select id="otype" name="otype">
							<option value="PDF">PDF</option>
							<option value="CSV">CSV</option>
						<option value="XLS">XLS</option>
					</select>
				</dd>
			</dl>
			<div class="form-buttons">
				<input type="submit">
					<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create report')"/></xsl:attribute>
				</input>
			</div>
		</form>
	</div>
</xsl:template>
