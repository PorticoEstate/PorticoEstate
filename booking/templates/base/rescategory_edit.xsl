<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="rescategory/tabs"/>
			<div id="rescategory_edit" class="booking-container">
				<fieldset>
					<h1>#<xsl:value-of select="rescategory/id"/></h1>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Resource category')"/>
						</label>
					</div>
					<div class="pure-control-group">
						<label for="field_name">
							<xsl:value-of select="php:function('lang', 'Name')" />
						</label>
						<input id="field_name" name="name" type="text">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please enter a name')" />
							</xsl:attribute>
							<xsl:attribute name="value">
								<xsl:value-of select="rescategory/name"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_active">
							<xsl:value-of select="php:function('lang', 'Active')"/>
						</label>
						<select id="field_active" name="active">
							<option value="1">
								<xsl:if test="rescategory/active=1">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Active')"/>
							</option>
							<option value="0">
								<xsl:if test="rescategory/active=0">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Inactive')"/>
							</option>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Activities')" />
						</label>
						<input type="hidden" data-validation="rescategory_activities">
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please choose at least one activity')" />
							</xsl:attribute>
						</input>
						<div id="activities_container" class="custom-container">
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
					<xsl:value-of select="rescategory/cancel_link"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
	<script type="text/javascript">
		initialSelection = <xsl:value-of select="rescategory/activities_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name')"/>;
	    <![CDATA[
            var activitiesURL    = 'index.php?menuaction=booking.uiactivity.index&filter_top_level=1&sort=name&phpgw_return_as=json';
	        ]]>
		var colDefsRespurces = [
			{label: '', object: [
				{type: 'input', attrs: [
					{name: 'type', value: 'checkbox'},
					{name: 'name', value: 'activities[]'}
				]}],
				value: 'id', checked: initialSelection},
			{key: 'name', label: lang['Name']},
		];
		createTable('activities_container', activitiesURL, colDefsRespurces, '', 'pure-table pure-table-bordered');
	</script>
</xsl:template>
