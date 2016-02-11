<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="activity/tabs"/>
			<div id="activity_add" class="booking-container">
				<div class="pure-control-group">
					<label for="field_name">
						<xsl:value-of select="php:function('lang', 'Activity')" />
					</label>
					<input id="field_name" name="name" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="activity/name"/>
						</xsl:attribute>
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please enter a name')" />
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label for="field_description">
						<xsl:value-of select="php:function('lang', 'Description')" />
					</label>
					<textarea rows="5" id="field_description" name="description">
						<xsl:value-of select="activity/description"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_parent_id">
						<xsl:value-of select="php:function('lang', 'Parent activity')" />
					</label>
					<select name="parent_id" id="field_parent_id">
						<option value="0">
							<xsl:value-of select="php:function('lang', 'No Parent')" />
						</option>
						<xsl:for-each select="activities">
							<option>
								<xsl:if test="../activity/parent_id = id">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:attribute name="value">
									<xsl:value-of select="id"/>
								</xsl:attribute>
								<xsl:value-of select="name"/>
							</option>
						</xsl:for-each>
					</select>
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="button pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Add')" />
				</xsl:attribute>
			</input>
			<a class="cancel pure-button pure-button-primary">
				<xsl:attribute name="href">
					<xsl:value-of select="activity/cancel_link"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
</xsl:template>
