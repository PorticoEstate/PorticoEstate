<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<script type="text/javascript">
		var documentOwnerType = "<xsl:value-of select="document/owner_type"/>";
		var documentOwnerAutocomplete = <xsl:value-of select="document/inline"/> == 0;
	</script>
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" enctype='multipart/form-data' id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="document/tabs"/>
			<div id="document" class="booking-container">
				<fieldset>
					<xsl:if test="document/id">
						<div class="heading">
							<legend>
								<h3>
									<xsl:value-of select="php:function('lang', 'Edit document')" />
								</h3>
							</legend>
						</div>
					</xsl:if>
					<xsl:if test="not(document/id)">
						<div class="heading">
							<legend>
								<h3>
									<xsl:value-of select="php:function('lang', 'Upload document')" />
								</h3>
							</legend>
						</div>
					</xsl:if>
					<xsl:if test="document/id">
						<input name='field_id' type='hidden'>
							<xsl:attribute name="value">
								<xsl:value-of select="document/id"/>
							</xsl:attribute>
						</input>
					</xsl:if>
					<div class="pure-control-group">
						<label for="field_name">
							<xsl:value-of select="php:function('lang', 'Document')" />
						</label>
						<input name="name" id='field_name'>
							<xsl:attribute name="value">
								<xsl:value-of select="document/name"/>
							</xsl:attribute>
							<xsl:attribute name="type">
								<xsl:choose>
									<xsl:when test="document/id">text</xsl:when>
									<xsl:otherwise>file</xsl:otherwise>
								</xsl:choose>
							</xsl:attribute>
							<xsl:if test="document/id">
								<xsl:attribute name="disabled" value="disabled"/>
							</xsl:if>
							<xsl:attribute name='title'>
								<xsl:value-of select="document/name"/>
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
						<textarea name="description" id='field_description'>
							<xsl:value-of select="document/description"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label for="field_category">
							<xsl:value-of select="php:function('lang', 'Category')" />
						</label>
						<select name='category' id='field_category'>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please select a category')" />
							</xsl:attribute>
							<option value=''>
								<xsl:value-of select="php:function('lang', 'Select Category...')" />
							</option>
							<xsl:for-each select="document/document_types/*">
								<option>
									<xsl:if test="../../category = local-name()">
										<xsl:attribute name="selected">selected</xsl:attribute>
									</xsl:if>
									<xsl:attribute name="value">
										<xsl:value-of select="local-name()"/>
									</xsl:attribute>
									<xsl:value-of select="php:function('lang', string(node()))"/>
								</option>
							</xsl:for-each>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="field_owner_name">
							<xsl:value-of select="php:function('lang', string(document/owner_type_label))" />
						</label>
						<input id="field_owner_name" name="owner_name" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="document/owner_name"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please enter an owner name')" />
							</xsl:attribute>
							<xsl:if test="document/inline = '1'">
								<xsl:attribute name="disabled">disabled</xsl:attribute>
							</xsl:if>
						</input>
						<input id="field_owner_id" name="owner_id" type="hidden">
							<xsl:attribute name="value">
								<xsl:value-of select="document/owner_id"/>
							</xsl:attribute>
						</input>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:choose>
						<xsl:when test="document/id">
							<xsl:value-of select="php:function('lang', 'Update')"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="php:function('lang', 'Create')"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
			</input>
			<input type="button" class="pure-button pure-button-primary" name="cancel">
				<xsl:attribute name="onclick">window.location="<xsl:value-of select="document/cancel_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</xsl:attribute>
			</input>
		</div>
	</form>
</xsl:template>
