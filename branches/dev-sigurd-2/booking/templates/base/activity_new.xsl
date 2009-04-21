<xsl:template match="data" class="foo">
	<div id="content">
	<h3><xsl:value-of select="lang/title" /></h3>
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST">
	
		<dl class="form-col">
			<dt><label for="field_name"><xsl:value-of select="lang/name" /></label></dt>
			<dd>
				<input id="field_name" name="name" type="text">
					<xsl:attribute name="value"><xsl:value-of select="activity/name"/></xsl:attribute>
				</input>
			</dd>
			
			<dt><label for="field_description"><xsl:value-of select="lang/description" /></label></dt>
			<dd>
				<textarea cols="5" rows="5" id="field_description" name="description"><xsl:value-of select="activity/description"/></textarea>
			</dd>
			<dt><label for="parent_id"><xsl:value-of select="lang/parent" /></label></dt>
			<dd>
				<div class="autocomplete">
				<select name="parent_id" id="field_parent_id">
				<option value="0"><xsl:value-of select="lang/novalue" /></option>
				<xsl:for-each select="dropdown/results">
					<option>
						<xsl:if test="../../activity/parent_id = id">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
						<xsl:value-of select="name"/>
					</option>
				</xsl:for-each>
				</select>
					<div id="parent_container"/>
				</div>
			</dd>
		</dl>



		<div class="form-buttons">
			<input type="submit">
				<xsl:attribute name="value"><xsl:value-of select="lang/create"/></xsl:attribute>
			</input>
			<a class="cancel">
				<xsl:attribute name="href"><xsl:value-of select="activity/cancel_link"/></xsl:attribute>
				<xsl:value-of select="lang/cancel" />
			</a>
		</div>
	</form>
	</div>
</xsl:template>
