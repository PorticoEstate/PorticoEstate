<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>
					
	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
				<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>
						
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="upload">
			<xsl:apply-templates select="upload"/>
		</xsl:when>
	</xsl:choose>
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>
					
<xsl:template match="upload">
	<div class="bimObject">
		<h1>Upload Bim file</h1>
		<xsl:variable name="form_action">
			<xsl:value-of select="import_action"/>
		</xsl:variable>
		<xsl:variable name="form_field_filename">
			<xsl:value-of select="form_field_filename"/>
		</xsl:variable>
		<xsl:variable name="form_field_modelname">
			<xsl:value-of select="form_field_modelname"/>
		</xsl:variable>
		<form method="post" enctype="multipart/form-data" name="uploadIfc" action="{$form_action}">
			<fieldset>
				<div class="pure-control-group">
					<label>
						Upload file
					</label>
					<input type="file" name="{$form_field_filename}" />
				</div>
				<div class="pure-control-group">
					<label>
						IFC Model name
					</label>
					<input type="text" name="{$form_field_modelname}" size="64"/>
				</div>
				<input type="submit" />
			</fieldset>
			</form>
		</div>
</xsl:template>
	