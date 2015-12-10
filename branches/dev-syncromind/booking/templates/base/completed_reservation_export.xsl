<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form" >
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="export/tabs"/>
			<div id="export" class="booking-container">
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Building')" />
					</label>
					<xsl:copy-of select="phpgw:booking_link(export/building_id)"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Season')" />
					</label>
					<xsl:copy-of select="phpgw:booking_link(export/season_id)"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Total Items')" />
					</label>
					<span>
						<xsl:value-of select="export/total_items"/>
					</span>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Total Cost')" />
					</label>
					<span>
						<xsl:value-of select="export/total_cost"/>
					</span>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Created')" />
					</label>
					<span>
						<xsl:value-of select="export/created_on"/>
					</span>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Created by')" />
					</label>
					<span>
						<xsl:value-of select="export/created_by_name"/>
					</span>
				</div>
			</div>
		</div>
	</form>
	<div class="form-buttons">
		<input type="button" class="pure-button pure-button-primary" name="cancel">
			<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="export/cancel_link"/>"</xsl:attribute>
			<xsl:attribute name="value">
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</xsl:attribute>
		</input>
	</div>
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'ID', 'Building', 'Season', 'From', 'To')"/>;
	</script>
</xsl:template>