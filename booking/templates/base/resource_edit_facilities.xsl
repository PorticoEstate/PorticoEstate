<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="resource/tabs"/>
			<div id="resource_edit_facilities" class="booking-container">
				<fieldset>
					<div class="pure-control-group">
						<label for="field_name">
							<xsl:value-of select="php:function('lang', 'Resource')" />
						</label>
						<span>
							<xsl:value-of select="resource/name"/>
						</span>
					</div>
					<div class="pure-control-group">
						<label for="field_name">
							<xsl:value-of select="php:function('lang', 'Main activity')" />
						</label>
						<span>
							<xsl:value-of select="resource/activity_name"/>
						</span>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Facilities')" />
						</label>
						<div id="facilities_container" class="custom-container">
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="pure-control-group form-buttons">
			<input type="submit" class="pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Save')"/>
				</xsl:attribute>
			</input>
			<a class="cancel pure-button pure-button-primary">
				<xsl:attribute name="href">
					<xsl:value-of select="resource/cancel_link"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
	<script type="text/javascript">
		initialSelection = <xsl:value-of select="resource/facilities_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name')"/>;
	    <![CDATA[
            var facilitiesURL = 'index.php?menuaction=booking.uifacility.index&phpgw_return_as=json';
	        ]]>
		var colDefsRespurces = [
			{label: '', object: [
				{type: 'input', attrs: [
					{name: 'type', value: 'checkbox'},
					{name: 'name', value: 'facilities[]'}
				]}],
				value: 'id', checked: initialSelection},
			{key: 'name', label: lang['Name']},
		];
		createTable('facilities_container', facilitiesURL, colDefsRespurces, '', 'pure-table pure-table-bordered');
	</script>
</xsl:template>
