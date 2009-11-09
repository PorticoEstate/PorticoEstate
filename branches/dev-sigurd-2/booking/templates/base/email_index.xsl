<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

    <form action="" method="POST">
		<input type="hidden" name="step" value="0"/>

		<dl class="form-col">
			<dt><label><xsl:value-of select="php:function('lang', 'buildings')"/></label></dt>
			<dd>
				<select id="field_building" name="building">
					<option value=""><xsl:value-of select="php:function('lang', '-- select a building --')" /></option>
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
			<dt><label for="field_season"><xsl:value-of select="php:function('lang', 'seasons')"/></label></dt>
			<dd>
				<select id="field_season" name="season">
					<option value=""><xsl:value-of select="php:function('lang', '-- select a season --')" /></option>
					<xsl:for-each select="seasons">
						<xsl:sort select="name"/>
						<option>
							<xsl:if test="../season = id">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
							<xsl:value-of select="name"/>
						</option>
					</xsl:for-each>
				</select>
			</dd>
		</dl>
		<div class="clr" />
		<dl class="form">
			<dt><label for="field_mailsubject"><xsl:value-of select="php:function('lang', 'Mail subject')" /></label></dt>
			<dd>
				<input type="text" id="field_mailsubject" name="mailsubject" class="full-width">
					<xsl:attribute name="value"><xsl:value-of select="mailsubject"/></xsl:attribute>
				</input>
			</dd>
		</dl>
		<div class="clr" />
		<dl class="form">
			<dt><label for="field_mailbody"><xsl:value-of select="php:function('lang', 'Mail body')" /></label></dt>
			<dd>
				<textarea id="field_mailbody" name="mailbody" class="full-width"><xsl:value-of select="mailbody"/></textarea>
			</dd>
		</dl>
		<div class="form-buttons">
			<input type="submit">
			<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'preview')"/></xsl:attribute>
			</input>
		</div>
    </form>
    </div>
</xsl:template>
