<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

    <form action="" method="POST">
		<input type="hidden" name="step" value="0"/>
        <dl class="form-col">
            <dt><label for="field_building"><xsl:value-of select="php:function('lang', 'Building')" /></label></dt>
            <dd>
                <div class="autocomplete">
                    <input id="field_building_id" name="building_id" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="building_id"/></xsl:attribute>
                    </input>
                    <input id="field_building_name" name="building_name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="building_name"/></xsl:attribute>
                    </input>
                    <div id="building_container"/>
                </div>
            </dd>
        </dl>
        <dl class="form-col">
            <dt><label for="field_season"><xsl:value-of select="php:function('lang', 'Season')" /></label></dt>
            <dd>
                <div id="season_container"><xsl:value-of select="php:function('lang', 'Select a building first')" /></div>
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
