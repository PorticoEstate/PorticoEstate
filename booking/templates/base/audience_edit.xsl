<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="audience/tabs"/>
			<div id="audience_edit" class="booking-container">
				<fieldset>
					<div class="pure-control-group">
						<label for="field_activity">
							<xsl:value-of select="php:function('lang', 'Activity')" />
						</label>
						<select name="activity_id" id="field_activity">
							<xsl:attribute name="disabled">disabled</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please select an activity')" />
							</xsl:attribute>
							<xsl:for-each select="activities">
								<option>
									<xsl:if test="selected = 1">
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
					<div class="pure-control-group">
						<label for="field_name">
							<xsl:value-of select="php:function('lang', 'Target audience')" />
						</label>
						<input id="field_name" name="name" type="text">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please enter a name')" />
							</xsl:attribute>
							<xsl:attribute name="value">
								<xsl:value-of select="audience/name"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_active">
							<xsl:value-of select="php:function('lang', 'Active')" />
						</label>
						<select id="field_active" name="active">
							<option value="1">
								<xsl:if test="audience/active=1">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Active')" />
							</option>
							<option value="0">
								<xsl:if test="audience/active=0">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Inactive')" />
							</option>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="field_sort">
							<xsl:value-of select="php:function('lang', 'Sort order')" />
						</label>
						<input id="field_sort" name="sort" type="text" value="{audience/sort}">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please enter a sort order')" />
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_description">
							<xsl:value-of select="php:function('lang', 'Description')" />
						</label>
						<textarea rows="5" id="field_description" name="description">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please enter a description')" />
							</xsl:attribute>
							<xsl:value-of select="audience/description"/>
						</textarea>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="button pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Save')" />
				</xsl:attribute>
			</input>
			<a class="cancel pure-button pure-button-primary">
				<xsl:attribute name="href">
					<xsl:value-of select="audience/cancel_link"></xsl:value-of>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>        
	</form>
</xsl:template>
