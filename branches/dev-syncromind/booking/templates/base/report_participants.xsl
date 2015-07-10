<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<!--div id="content">

		<xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>

		<dl class="form">
			<dt class="heading">
				<xsl:value-of select="php:function('lang', 'Participants Per Age Group Per Month')" />
			</dt>
		</dl-->

    <form action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form">
        <input type="hidden" name="tab" value=""/>
            <div id="tab-content">
                <xsl:value-of disable-output-escaping="yes" select="data/tabs"/>
                <div id="report_part">
			<div class="pure-control-group">
				<!--dt><label for="field_from"><xsl:value-of select="php:function('lang', 'From')" /></label></dt>
				<dd>
					<div class="date-picker">
						<input id="field_from" name="from" type="text">
							<xsl:attribute name="value"><xsl:value-of select="from"/></xsl:attribute>
						</input>
					</div>
				</dd-->
                                <label>
                                    <xsl:value-of select="php:function('lang', 'From')" />
                                </label>
                                <input class="datetime" id="start_date" name="start_date" type="text">
                                    <xsl:attribute name="value"><xsl:value-of select="from"/></xsl:attribute>
                                </input>
                        </div>
			<div class="pure-control-group">
				<!--dt><label for="field_to"><xsl:value-of select="php:function('lang', 'To')" /></label></dt>
				<dd>
					<div class="date-picker">
						<input id="field_to" name="to" type="text">
							<xsl:attribute name="value"><xsl:value-of select="to"/></xsl:attribute>
						</input>
					</div>
				</dd-->
                                <label>
                                    <xsl:value-of select="php:function('lang', 'To')" />
                                </label>
                                <input class="datetime" id="end_date" name="end_date" type="text">
                                    <xsl:attribute name="value"><xsl:value-of select="to"/></xsl:attribute>
                                </input>
                        </div>
			<div class="clr" />
			<div class="pure-control-group">
				<label><xsl:value-of select="php:function('lang', 'buildings')"/></label>
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
                        </div>
			<div class="pure-control-group">
				<label><xsl:value-of select="php:function('lang', 'Format')" /></label>
					<select id="otype" name="otype">
							<option value="PDF">PDF</option>
							<option value="CSV">CSV</option>
						<option value="XLS">XLS</option>
					</select>
                        </div>
                </div>
            </div>
            <div class="form-buttons">
                    <input type="submit" class="pure-button pure-button-primary">
                            <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create report')"/></xsl:attribute>
                    </input>
            </div>
    </form>
	<!--/div-->
</xsl:template>
